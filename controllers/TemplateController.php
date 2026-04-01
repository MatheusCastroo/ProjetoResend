<?php

class TemplateController
{
    private TemplateRepository $tpl;
    private ClienteRepository $cli;
    private TemplateBaseRepository $bases;

    public function __construct()
    {
        $this->tpl = new TemplateRepository();
        $this->cli = new ClienteRepository();
        $this->bases = new TemplateBaseRepository();
    }

    public function index(): void
    {
        $clienteId = (int) ($_GET['cliente_id'] ?? 0);
        if ($clienteId <= 0) {
            flash('erro', 'Selecione um cliente.');
            redirect('clientes');
            return;
        }
        $c = $this->cli->find($clienteId);
        if (!$c) {
            flash('erro', 'Cliente não encontrado.');
            redirect('clientes');
            return;
        }
        view('templates/index', [
            'title' => 'Templates — ' . $c['nome'],
            'cliente' => $c,
            'templates' => $this->tpl->byCliente($clienteId),
        ]);
    }

    public function novo(): void
    {
        $clienteId = (int) ($_GET['cliente_id'] ?? 0);
        $c = $this->cli->find($clienteId);
        if (!$c) {
            flash('erro', 'Cliente não encontrado.');
            redirect('clientes');
            return;
        }
        view('templates/form', [
            'title' => 'Novo template',
            'cliente' => $c,
            'template' => null,
            'bases' => $this->bases->byCliente($clienteId),
        ]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $t = $this->tpl->find($id);
        if (!$t) {
            flash('erro', 'Template não encontrado.');
            redirect('clientes');
            return;
        }
        $c = $this->cli->find((int) $t['cliente_id']);
        if (!$c) {
            redirect('clientes');
            return;
        }
        view('templates/form', [
            'title' => 'Editar template',
            'cliente' => $c,
            'template' => $t,
            'bases' => $this->bases->byCliente((int) $c['id']),
        ]);
    }

    public function salvar(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $variaveisRaw = trim((string) ($_POST['variaveis_json'] ?? ''));
        $variaveis = null;
        if ($variaveisRaw !== '') {
            $decoded = json_decode($variaveisRaw, true);
            $variaveis = is_array($decoded) ? $decoded : null;
        }
        $data = [
            'cliente_id' => $clienteId,
            'template_base_id' => (int) ($_POST['template_base_id'] ?? 0) ?: null,
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'assunto' => trim((string) ($_POST['assunto'] ?? '')),
            'html' => (string) ($_POST['html'] ?? ''),
            'variaveis' => $variaveis,
        ];
        if ($data['nome'] === '' || $data['assunto'] === '') {
            flash('erro', 'Nome e assunto são obrigatórios.');
            redirect($id ? 'templates/editar?id=' . $id : 'templates/novo?cliente_id=' . $clienteId);
            return;
        }
        if ($id > 0) {
            $this->tpl->update($id, $data);
            flash('ok', 'Template atualizado.');
        } else {
            $this->tpl->create($data);
            flash('ok', 'Template criado.');
        }
        redirect('templates?cliente_id=' . $clienteId);
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        if ($id > 0 && $clienteId > 0) {
            $this->tpl->delete($id, $clienteId);
            flash('ok', 'Template removido.');
        }
        redirect('templates?cliente_id=' . $clienteId);
    }
}
