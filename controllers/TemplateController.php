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
            'unattached' => $this->tpl->unattachedForApplication($applicationId),
        ]);
    }

    public function novo(): void
    {
        $pre = (int) ($_GET['application_id'] ?? 0);
        view('templates/form', [
            'title' => 'Novo template',
            'template' => null,
            'applications' => $this->apps->all(),
            'linkedApplicationIds' => $pre > 0 ? [$pre] : [],
            'backUrl' => $pre > 0 ? 'templates?application_id=' . $pre : 'templates',
        ]);
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $t = $this->tpl->find($id);
        if (!$t) {
            flash('erro', 'Template não encontrado.');
            redirect('templates');
            return;
        }
        view('templates/form', [
            'title' => 'Editar template',
            'template' => $t,
            'applications' => $this->apps->all(),
            'linkedApplicationIds' => $this->tpl->linkedApplicationIds($id),
            'backUrl' => 'templates',
        ]);
    }

    public function salvar(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        $rawIds = $_POST['application_ids'] ?? [];
        if (!is_array($rawIds)) {
            $rawIds = [];
        }
        $applicationIds = array_values(array_unique(array_filter(array_map('intval', $rawIds), static function ($x) {
            return $x > 0;
        })));

        $variaveisRaw = trim((string) ($_POST['variaveis_json'] ?? ''));
        $variaveis = null;
        if ($variaveisRaw !== '') {
            $decoded = json_decode($variaveisRaw, true);
            $variaveis = is_array($decoded) ? $decoded : null;
        }
        $data = [
            'nome' => trim((string) ($_POST['nome'] ?? '')),
            'event_key' => trim((string) ($_POST['event_key'] ?? '')),
            'assunto' => trim((string) ($_POST['assunto'] ?? '')),
            'html' => (string) ($_POST['html'] ?? ''),
            'variaveis' => $variaveis,
        ];
        if ($data['nome'] === '' || $data['assunto'] === '') {
            flash('erro', 'Nome e assunto são obrigatórios.');
            redirect($id ? 'templates/editar?id=' . $id : 'templates/novo');
            return;
        }
        if ($applicationIds === []) {
            flash('erro', 'Selecione ao menos uma application que poderá usar este template.');
            redirect($id ? 'templates/editar?id=' . $id : 'templates/novo');
            return;
        }

        if ($id > 0) {
            $this->tpl->update($id, $data);
            $this->tpl->syncApplicationLinks($id, $applicationIds);
            flash('ok', 'Template atualizado.');
        } else {
            $newId = $this->tpl->create($data);
            $this->tpl->syncApplicationLinks($newId, $applicationIds);
            flash('ok', 'Template criado.');
        }
        $first = $applicationIds[0];
        redirect('templates?application_id=' . $first);
    }

    /** Remove apenas o vínculo template ↔ application. */
    public function desvincular(): void
    {
        csrf_verify();
        $id = (int) ($_POST['template_id'] ?? 0);
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        if ($id > 0 && $applicationId > 0) {
            $this->tpl->detach($applicationId, $id);
            flash('ok', 'Template desvinculado desta application.');
        }
        redirect('templates?application_id=' . $applicationId);
    }

    /** Exclui o template de todas as applications. */
    public function excluir(): void
    {
        csrf_verify();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->tpl->deleteTemplate($id);
            flash('ok', 'Template excluído.');
        }
        redirect('templates');
    }

    public function vincular(): void
    {
        $applicationId = (int) ($_GET['application_id'] ?? $_POST['application_id'] ?? 0);
        if ($applicationId <= 0) {
            redirect('applications');
            return;
        }
        $app = $this->apps->find($applicationId);
        if (!$app) {
            redirect('applications');
            return;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            csrf_verify();
            $tid = (int) ($_POST['template_id'] ?? 0);
            if ($tid > 0) {
                $this->tpl->attach($applicationId, $tid);
                flash('ok', 'Template vinculado.');
            }
            redirect('templates?application_id=' . $applicationId);
            return;
        }

        view('templates/vincular', [
            'title' => 'Vincular template',
            'application' => $app,
            'unattached' => $this->tpl->unattachedForApplication($applicationId),
        ]);
    }
}
