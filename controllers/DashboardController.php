<?php

class DashboardController
{
    public function index(): void
    {
        $clientes = new ClienteRepository();
        $emails = new EmailRepository();
        view('dashboard/index', [
            'title' => 'Início',
            'totalClientes' => count($clientes->all()),
            'totalEmails' => $emails->countTotal(),
            'ultimosEmails' => $emails->all(null, 10),
        ]);
    }
}
