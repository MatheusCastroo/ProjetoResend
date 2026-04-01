<?php

class TemplateBaseController
{
    private TemplateBaseRepository $repo;
    private ClienteRepository $cli;

    public function __construct()
    {
        $this->repo = new TemplateBaseRepository();
        $this->cli = new ClienteRepository();
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
        view('template_bases/index', [
            'title' => 'Templates base — ' . $c['nome'],
            'cliente' => $c,
            'bases' => $this->repo->byCliente($clienteId),
        ]);
    }

    public function novo(): void
    {
        $clienteId = (int) ($_GET['cliente_id'] ?? 0);
        $c = $this->cli->find($clienteId);
        if (!$c) {
            redirect('clientes');
            return;
        }
        view('template_bases/form', [
            'title' => 'Novo template base',
            'cliente' => $c,
            'base' => null,
        ]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $b = $this->repo->find($id);
        if (!$b) {
            flash('erro', 'Registro não encontrado.');
            redirect('clientes');
            return;
        }
        $c = $this->cli->find((int) $b['cliente_id']);
        if (!$c) {
            redirect('clientes');
            return;
        }
        view('template_bases/form', [
            'title' => 'Editar template base',
            'cliente' => $c,
            'base' => $b,
        ]);
    }

    public function salvar(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $data = [
            'cliente_id' => $clienteId,
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'html' => (string) ($_POST['html'] ?? ''),
        ];
        if ($data['nome'] === '') {
            flash('erro', 'Nome é obrigatório.');
            redirect($id ? 'template-bases/editar?id=' . $id : 'template-bases/novo?cliente_id=' . $clienteId);
            return;
        }
        if ($id > 0) {
            $this->repo->update($id, $data);
            flash('ok', 'Template base atualizado.');
        } else {
            $this->repo->create($data);
            flash('ok', 'Template base criado.');
        }
        redirect('template-bases?cliente_id=' . $clienteId);
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        if ($id > 0 && $clienteId > 0) {
            $this->repo->delete($id, $clienteId);
            flash('ok', 'Removido.');
        }
        redirect('template-bases?cliente_id=' . $clienteId);
    }
}
