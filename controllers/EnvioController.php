<?php

class EnvioController
{
    public function index(): void
    {
        $apps = new ApplicationRepository();
        $tpl = new TemplateRepository();
        $applicationId = (int) ($_GET['application_id'] ?? 0);
        $templateId = (int) ($_GET['template_id'] ?? 0);

        $list = $apps->all();
        $application = $applicationId > 0 ? $apps->find($applicationId) : null;
        $templates = $application ? $tpl->byApplication($applicationId) : [];
        $template = $templateId > 0 ? $tpl->find($templateId) : null;

        if ($template && $application && !$tpl->isLinked((int) $application['id'], (int) $template['id'])) {
            $template = null;
        }

        $varKeys = $template
            ? TemplateEngine::variaveisFromJson($template['variaveis'] ?? null)
            : [];

        view('envio/index', [
            'title' => 'Enviar e-mail',
            'applications' => $list,
            'application' => $application,
            'templates' => $templates,
            'template' => $template,
            'varKeys' => $varKeys,
            'defaultEmail' => (string) config('app.default_test_email'),
        ]);
    }

    public function preview(): void
    {
        csrf_verify();
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $apps = new ApplicationRepository();
        $tpl = new TemplateRepository();
        $app = $apps->find($applicationId);
        $t = $tpl->find($templateId);
        if (!$app || !$t || !$tpl->isLinked((int) $app['id'], (int) $t['id'])) {
            http_response_code(400);
            echo 'Dados inválidos';
            return;
        }
        $vars = $this->collectVars($t['variaveis'] ?? null);
        $composer = new ApplicationMailComposer(new TemplateEngine());
        $out = $composer->compose($app, $t, $vars);
        header('Content-Type: text/html; charset=utf-8');
        echo $out['html'];
        exit;
    }

    public function enviar(): void
    {
        csrf_verify();
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $dest = trim((string) ($_POST['destinatario'] ?? ''));

        $apps = new ApplicationRepository();
        $tpl = new TemplateRepository();
        $emailRepo = new EmailRepository();

        $app = $apps->find($applicationId);
        $t = $tpl->find($templateId);
        if (!$app || !$t || !$tpl->isLinked($applicationId, $templateId)) {
            flash('erro', 'Application ou template inválidos (template não vinculado).');
            redirect('envio');
            return;
        }
        if ($dest === '' || !filter_var($dest, FILTER_VALIDATE_EMAIL)) {
            flash('erro', 'Destinatário inválido.');
            redirect('envio?application_id=' . $applicationId . '&template_id=' . $templateId);
            return;
        }

        $vars = $this->collectVars($t['variaveis'] ?? null);
        $composer = new ApplicationMailComposer(new TemplateEngine());
        $out = $composer->compose($app, $t, $vars);

        $mail = new EmailService();
        $res = $mail->sendWithCredentials(
            (string) $app['resend_api_key'],
            (string) ($app['resend_from'] ?? 'onboarding@resend.dev'),
            $dest,
            $out['assunto'],
            $out['html']
        );

        $status = $res['ok'] ? 'ENVIADO' : 'ERRO';
        $resposta = json_encode(
            ['http' => $res['http'], 'body' => $res['body'], 'decoded' => $res['decoded']],
            JSON_UNESCAPED_UNICODE
        );

        $emailRepo->create([
            'application_id' => $applicationId,
            'template_id' => $templateId,
            'destinatario' => $dest,
            'assunto' => $out['assunto'],
            'conteudo' => $out['html'],
            'status' => $status,
            'resposta_api' => $resposta,
        ]);

        if ($res['ok']) {
            flash('ok', 'E-mail enviado com sucesso.');
        } else {
            flash('erro', 'Falha no envio. Verifique resend_api_key / remetente.');
        }
        redirect('logs');
    }

    /**
     * @return array<string, string>
     */
    private function collectVars(?string $variaveisJson): array
    {
        $keys = TemplateEngine::variaveisFromJson($variaveisJson);
        $out = [];
        foreach ($keys as $k) {
            $field = 'var_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $k);
            $out[$k] = (string) ($_POST[$field] ?? '');
        }
        return $out;
    }
}
