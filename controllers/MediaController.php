<?php

class MediaController
{
    private MediaRepository $repo;
    private ApplicationRepository $apps;
    private MediaService $upload;

    public function __construct()
    {
        $this->repo = new MediaRepository();
        $this->apps = new ApplicationRepository();
        $this->upload = new MediaService();
    }

    public function index(): void
    {
        $applicationId = (int) ($_GET['application_id'] ?? 0);
        if ($applicationId <= 0) {
            redirect('applications');
            return;
        }
        $app = $this->apps->find($applicationId);
        if (!$app) {
            redirect('applications');
            return;
        }
        view('media/index', [
            'title' => 'Media — ' . $app['nome'],
            'application' => $app,
            'items' => $this->repo->byApplication($applicationId),
        ]);
    }

    public function upload(): void
    {
        csrf_verify();
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $file = $_FILES['arquivo'] ?? null;

        if ($applicationId <= 0 || !$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('erro', 'Envie um arquivo válido.');
            redirect('media?application_id=' . $applicationId);
            return;
        }

        $tmp = $file['tmp_name'];
        $orig = (string) ($file['name'] ?? 'upload');
        $mime = (string) ($file['type'] ?? 'application/octet-stream');

        try {
            $res = $this->upload->uploadFile($tmp, $orig, $mime);
            $this->repo->create([
                'application_id' => $applicationId,
                'nome' => $nome !== '' ? $nome : $orig,
                'url' => $res['url'],
                'provider' => 'cloudinary',
            ]);
            flash('ok', 'Upload concluído.');
        } catch (Throwable $e) {
            flash('erro', $e->getMessage());
        }
        redirect('media?application_id=' . $applicationId);
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        if ($id > 0 && $applicationId > 0) {
            $this->repo->delete($id, $applicationId);
            flash('ok', 'Mídia removida.');
        }
        redirect('media?application_id=' . $applicationId);
    }
}
