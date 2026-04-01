<?php

class LogsController
{
    public function index(): void
    {
        $repo = new EmailRepository();
        $raw = $_GET['application_id'] ?? null;
        $applicationId = ($raw !== null && $raw !== '') ? (int) $raw : null;
        if ($applicationId === 0) {
            $applicationId = null;
        }
        $emails = $repo->all($applicationId);
        $applications = (new ApplicationRepository())->all();
        view('logs/index', [
            'title' => 'Logs de envio',
            'emails' => $emails,
            'applications' => $applications,
            'filtroApplicationId' => $applicationId,
        ]);
    }

    public function ver(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $repo = new EmailRepository();
        $row = $repo->find($id);
        if (!$row) {
            flash('erro', 'Registro não encontrado.');
            redirect('logs');
            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo $row['conteudo'];
        exit;
    }
}
