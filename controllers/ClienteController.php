<?php

class ClienteController
{
    private ClienteRepository $repo;

    public function __construct()
    {
        $this->repo = new ClienteRepository();
    }

    public function index(): void
    {
        view('clientes/index', [
            'title' => 'Clientes',
            'clientes' => $this->repo->all(),
        ]);
    }

    public function novo(): void
    {
        view('clientes/form', [
            'title' => 'Novo cliente',
            'cliente' => null,
        ]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $c = $this->repo->find($id);
        if (!$c) {
            flash('erro', 'Cliente não encontrado.');
            redirect('clientes');
            return;
        }
        view('clientes/form', [
            'title' => 'Editar cliente',
            'cliente' => $c,
        ]);
    }

    public function salvar(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'logo_url' => trim((string) ($_POST['logo_url'] ?? '')) ?: null,
            'cor_primaria' => trim((string) ($_POST['cor_primaria'] ?? '#2563eb')),
            'cor_secundaria' => trim((string) ($_POST['cor_secundaria'] ?? '#1e293b')),
            'layout_padrao' => trim((string) ($_POST['layout_padrao'] ?? '')) ?: null,
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
                redirect($id ? 'clientes/editar?id=' . $id : 'clientes/novo');
                return;
            }
        }
        if ($data['nome'] === '') {
            flash('erro', 'Nome é obrigatório.');
            redirect($id ? 'clientes/editar?id=' . $id : 'clientes/novo');
            return;
        }
        if ($id > 0) {
            $this->repo->update($id, $data);
            flash('ok', 'Cliente atualizado.');
        } else {
            $this->repo->create($data);
            flash('ok', 'Cliente criado.');
        }
        redirect('clientes');
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->repo->delete($id);
            flash('ok', 'Cliente removido.');
        }
        redirect('clientes');
    }
}
