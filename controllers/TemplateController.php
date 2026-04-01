<?php

class TemplateController
{
    private TemplateRepository $tpl;
    private ApplicationRepository $apps;

    public function __construct()
    {
        $this->tpl = new TemplateRepository();
        $this->apps = new ApplicationRepository();
    }

    public function index(): void
    {
        $applicationId = (int) ($_GET['application_id'] ?? 0);
        if ($applicationId <= 0) {
            view('templates/index_all', [
                'title' => 'Templates',
                'rows' => $this->tpl->allForAdmin(),
                'applications' => $this->apps->all(),
            ]);
            return;
        }
        $app = $this->apps->find($applicationId);
        if (!$app) {
            flash('erro', 'Application não encontrada.');
            redirect('applications');
            return;
        }
        view('templates/index', [
            'title' => 'Templates — ' . $app['nome'],
            'application' => $app,
            'templates' => $this->tpl->byApplication($applicationId),
        ]);
    }

    public function novo(): void
    {
        $applicationId = (int) ($_GET['application_id'] ?? 0);
        $app = $this->apps->find($applicationId);
        if (!$app) {
            flash('erro', 'Application não encontrada.');
            redirect('applications');
            return;
        }
        view('templates/form', [
            'title' => 'Novo template',
            'application' => $app,
            'template' => null,
        ]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $t = $this->tpl->find($id);
        if (!$t) {
            flash('erro', 'Template não encontrado.');
            redirect('applications');
            return;
        }
        $app = $this->apps->find((int) $t['application_id']);
        if (!$app) {
            redirect('applications');
            return;
        }
        view('templates/form', [
            'title' => 'Editar template',
            'application' => $app,
            'template' => $t,
        ]);
    }

    public function salvar(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $variaveisRaw = trim((string) ($_POST['variaveis_json'] ?? ''));
        $variaveis = null;
        if ($variaveisRaw !== '') {
            $decoded = json_decode($variaveisRaw, true);
            $variaveis = is_array($decoded) ? $decoded : null;
        }
        $data = [
            'application_id' => $applicationId,
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'assunto' => trim((string) ($_POST['assunto'] ?? '')),
            'html' => (string) ($_POST['html'] ?? ''),
            'variaveis' => $variaveis,
        ];
        if ($data['nome'] === '' || $data['assunto'] === '') {
            flash('erro', 'Nome e assunto são obrigatórios.');
            redirect($id ? 'templates/editar?id=' . $id : 'templates/novo?application_id=' . $applicationId);
            return;
        }
        if ($id > 0) {
            $this->tpl->update($id, $data);
            flash('ok', 'Template atualizado.');
        } else {
            $this->tpl->create($data);
            flash('ok', 'Template criado.');
        }
        redirect('templates?application_id=' . $applicationId);
    }

    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        if ($id > 0 && $applicationId > 0) {
            $this->tpl->delete($id, $applicationId);
            flash('ok', 'Template removido.');
        }
        redirect('templates?application_id=' . $applicationId);
    }
}
