<?php

class ApplicationController
{
    private ApplicationRepository $repo;

    public function __construct()
    {
        $this->repo = new ApplicationRepository();
    }

    public function index(): void
    {
        view('applications/index', [
            'title' => 'Applications',
            'applications' => $this->repo->all(),
        ]);
    }

    public function novo(): void
    {
        view('applications/form', [
            'title' => 'Nova application',
            'application' => null,
        ]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $a = $this->repo->find($id);
        if (!$a) {
            flash('erro', 'Application não encontrada.');
            redirect('applications');
            return;
        }
        view('applications/form', [
            'title' => 'Editar application',
            'application' => $a,
        ]);
    }

    public function salvar(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'resend_api_key' => trim((string) ($_POST['resend_api_key'] ?? '')),
            'resend_from' => trim((string) ($_POST['resend_from'] ?? 'onboarding@resend.dev')),
            'logo_url' => trim((string) ($_POST['logo_url'] ?? '')) ?: null,
            'cor_primaria' => trim((string) ($_POST['cor_primaria'] ?? '#6366f1')),
            'cor_secundaria' => trim((string) ($_POST['cor_secundaria'] ?? '#111827')),
        ];

        $logoFile = $_FILES['logo_file'] ?? null;
        if (
            is_array($logoFile)
            && ($logoFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
            && is_uploaded_file((string) ($logoFile['tmp_name'] ?? ''))
        ) {
            try {
                $up = new MediaService();
                $res = $up->uploadFile(
                    (string) $logoFile['tmp_name'],
                    (string) ($logoFile['name'] ?? 'logo'),
                    (string) ($logoFile['type'] ?? 'image/png')
                );
                $data['logo_url'] = $res['url'];
            } catch (Throwable $e) {
                flash('erro', 'Logo: ' . $e->getMessage());
                redirect($id ? 'applications/editar?id=' . $id : 'applications/novo');
                return;
            }
        }

        if ($data['nome'] === '' || $data['resend_api_key'] === '') {
            flash('erro', 'Nome e Resend API Key são obrigatórios.');
            redirect($id ? 'applications/editar?id=' . $id : 'applications/novo');
            return;
        }

        if ($id > 0) {
            $this->repo->update($id, $data);
            flash('ok', 'Application atualizada.');
        } else {
            $data['api_key'] = $this->generateApiKey();
            $this->repo->create($data);
            flash('ok', 'Application criada. Guarde a API Key exibida na listagem.');
        }
        redirect('applications');
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->repo->delete($id);
            flash('ok', 'Application removida.');
        }
        redirect('applications');
    }

    public function regenerarChave(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->repo->updateApiKey($id, $this->generateApiKey());
            flash('ok', 'Nova API Key gerada.');
        }
        redirect('applications/editar?id=' . $id);
    }

    private function generateApiKey(): string
    {
        return bin2hex(random_bytes(24));
    }
}
