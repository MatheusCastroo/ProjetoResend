<?php

class HistoricoController
{
    public function index(): void
    {
        $repo = new EmailRepository();
        $raw = $_GET['cliente_id'] ?? null;
        $clienteId = ($raw !== null && $raw !== '') ? (int) $raw : null;
        if ($clienteId === 0) {
            $clienteId = null;
        }
        $emails = $repo->all($clienteId);
        $clientes = (new ClienteRepository())->all();
        view('historico/index', [
            'title' => 'Histórico de envios',
            'emails' => $emails,
            'clientes' => $clientes,
            'filtroClienteId' => $clienteId,
        ]);
    }

    public function ver(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $repo = new EmailRepository();
        $pdo = Database::get();
        $st = $pdo->prepare(
            'SELECT e.*, c.nome AS cliente_nome FROM emails e
             JOIN clientes c ON c.id = e.cliente_id WHERE e.id = ?'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) {
            flash('erro', 'Registro não encontrado.');
            redirect('historico');
            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo $row['conteudo'];
        exit;
    }
}
