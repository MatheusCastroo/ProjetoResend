<?php

class DashboardController
{
    public function index(): void
    {
        $apps = new ApplicationRepository();
        $emails = new EmailRepository();
        view('dashboard/index', [
            'title' => 'Início',
            'totalApplications' => count($apps->all()),
            'totalEmails' => $emails->countTotal(),
            'ultimosEmails' => $emails->all(null, 10),
        ]);
    }
}
