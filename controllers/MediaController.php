<?php

class MediaController
{
    private MediaRepository $repo;
    private ClienteRepository $cli;
    private MediaService $upload;

    public function __construct()
    {
        $this->repo = new MediaRepository();
        $this->cli = new ClienteRepository();
        $this->upload = new MediaService();
    }

    public function index(): void
    {
        $clienteId = (int) ($_GET['cliente_id'] ?? 0);
        if ($clienteId <= 0) {
            redirect('clientes');
            return;
        }
        $c = $this->cli->find($clienteId);
        if (!$c) {
            redirect('clientes');
            return;
        }
        view('midias/index', [
            'title' => 'Mídias — ' . $c['nome'],
            'cliente' => $c,
            'midias' => $this->repo->byCliente($clienteId),
        ]);
    }

    public function upload(): void
    {
        csrf_verify();
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $file = $_FILES['arquivo'] ?? null;

        if ($clienteId <= 0 || !$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('erro', 'Envie um arquivo válido.');
            redirect('midias?cliente_id=' . $clienteId);
            return;
        }

        $tmp = $file['tmp_name'];
        $orig = (string) ($file['name'] ?? 'upload');
        $mime = (string) ($file['type'] ?? 'application/octet-stream');

        try {
            $res = $this->upload->uploadFile($tmp, $orig, $mime);
            $this->repo->create([
                'cliente_id' => $clienteId,
                'nome' => $nome !== '' ? $nome : $orig,
                'url' => $res['url'],
                'provider' => 'cloudinary',
            ]);
            flash('ok', 'Upload concluído.');
        } catch (Throwable $e) {
            flash('erro', $e->getMessage());
        }
        redirect('midias?cliente_id=' . $clienteId);
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        if ($id > 0 && $clienteId > 0) {
            $this->repo->delete($id, $clienteId);
            flash('ok', 'Mídia removida.');
        }
        redirect('midias?cliente_id=' . $clienteId);
    }
}
