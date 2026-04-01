<?php

class EnvioController
{
    public function index(): void
    {
        $cli = new ClienteRepository();
        $tpl = new TemplateRepository();
        $clienteId = (int) ($_GET['cliente_id'] ?? 0);
        $templateId = (int) ($_GET['template_id'] ?? 0);

        $clientes = $cli->all();
        $cliente = $clienteId > 0 ? $cli->find($clienteId) : null;
        $templates = $cliente ? $tpl->byCliente($clienteId) : [];
        $template = $templateId > 0 ? $tpl->find($templateId) : null;

        if ($template && $cliente && (int) $template['cliente_id'] !== (int) $cliente['id']) {
            $template = null;
        }

        $varKeys = $template
            ? TemplateEngine::variaveisFromJson($template['variaveis'] ?? null)
            : [];

        view('envio/index', [
            'title' => 'Enviar e-mail',
            'clientes' => $clientes,
            'cliente' => $cliente,
            'templates' => $templates,
            'template' => $template,
            'varKeys' => $varKeys,
            'defaultEmail' => (string) config('app.default_test_email'),
        ]);
    }

    public function preview(): void
    {
        csrf_verify();
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $cli = new ClienteRepository();
        $tpl = new TemplateRepository();
        $c = $cli->find($clienteId);
        $t = $tpl->find($templateId);
        if (!$c || !$t || (int) $t['cliente_id'] !== (int) $c['id']) {
            http_response_code(400);
            echo 'Dados inválidos';
            return;
        }
        $vars = $this->collectVars($t['variaveis'] ?? null);
        $composer = new MailComposerService(
            new TemplateEngine(),
            new TemplateBaseRepository()
        );
        $out = $composer->compose($c, $t, $vars);
        header('Content-Type: text/html; charset=utf-8');
        echo $out['html'];
        exit;
    }

    public function enviar(): void
    {
        csrf_verify();
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $dest = trim((string) ($_POST['destinatario'] ?? ''));

        $cli = new ClienteRepository();
        $tpl = new TemplateRepository();
        $emailRepo = new EmailRepository();

        $c = $cli->find($clienteId);
        $t = $tpl->find($templateId);
        if (!$c || !$t || (int) $t['cliente_id'] !== (int) $c['id']) {
            flash('erro', 'Cliente ou template inválidos.');
            redirect('envio');
            return;
        }
        if ($dest === '' || !filter_var($dest, FILTER_VALIDATE_EMAIL)) {
            flash('erro', 'Destinatário inválido.');
            redirect('envio?cliente_id=' . $clienteId . '&template_id=' . $templateId);
            return;
        }

        $vars = $this->collectVars($t['variaveis'] ?? null);
        $composer = new MailComposerService(
            new TemplateEngine(),
            new TemplateBaseRepository()
        );
        $out = $composer->compose($c, $t, $vars);

        $mail = new EmailService();
        $res = $mail->send($dest, $out['assunto'], $out['html']);

        $status = $res['ok'] ? 'ENVIADO' : 'ERRO';
        $resposta = json_encode(
            ['http' => $res['http'], 'body' => $res['body'], 'decoded' => $res['decoded']],
            JSON_UNESCAPED_UNICODE
        );

        $emailRepo->create([
            'cliente_id' => $clienteId,
            'destinatario' => $dest,
            'assunto' => $out['assunto'],
            'conteudo' => $out['html'],
            'status' => $status,
            'resposta_api' => $resposta,
            'data_envio' => date('Y-m-d H:i:s'),
        ]);

        if ($res['ok']) {
            flash('ok', 'E-mail enviado com sucesso.');
        } else {
            flash('erro', 'Falha no envio. Verifique a API Resend e o histórico.');
        }
        redirect('historico');
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
