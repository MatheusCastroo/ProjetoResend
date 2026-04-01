<?php

/**
 * POST /api/send/{template_id}
 * Header: X-API-KEY
 * Body JSON: { "email": "...", "data": { ... } }
 */
class ApiSendController
{
    public function send(int $templateId): void
    {
        $this->corsHeaders();

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        $apiKey = $this->readApiKey();
        if ($apiKey === '') {
            $this->json(403, ['success' => false, 'message' => 'API Key obrigatória (header X-API-KEY)']);
            return;
        }

        $appRepo = new ApplicationRepository();
        $application = $appRepo->findByApiKey($apiKey);
        if (!$application) {
            $this->json(403, ['success' => false, 'message' => 'API Key inválida']);
            return;
        }

        $tplRepo = new TemplateRepository();
        $template = $tplRepo->find($templateId);
        if (!$template || (int) $template['application_id'] !== (int) $application['id']) {
            $this->json(401, ['success' => false, 'message' => 'Template inválido ou não pertence a esta aplicação']);
            return;
        }

        $raw = file_get_contents('php://input');
        $body = json_decode($raw !== false ? $raw : '', true);
        if (!is_array($body)) {
            $this->json(400, ['success' => false, 'message' => 'JSON inválido']);
            return;
        }

        $email = trim((string) ($body['email'] ?? ''));
        $data = $body['data'] ?? [];
        if (!is_array($data)) {
            $data = [];
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(400, ['success' => false, 'message' => 'Campo email inválido ou ausente']);
            return;
        }

        try {
            $composer = new ApplicationMailComposer(new TemplateEngine());
            $out = $composer->compose($application, $template, $data);

            $mail = new EmailService();
            $res = $mail->sendWithCredentials(
                (string) $application['resend_api_key'],
                (string) ($application['resend_from'] ?? 'onboarding@resend.dev'),
                $email,
                $out['assunto'],
                $out['html']
            );

            $emailRepo = new EmailRepository();
            $status = $res['ok'] ? 'ENVIADO' : 'ERRO';
            $resposta = json_encode(
                ['http' => $res['http'], 'body' => $res['body'], 'decoded' => $res['decoded']],
                JSON_UNESCAPED_UNICODE
            );

            $emailRepo->create([
                'application_id' => (int) $application['id'],
                'template_id' => $templateId,
                'destinatario' => $email,
                'assunto' => $out['assunto'],
                'conteudo' => $out['html'],
                'status' => $status,
                'resposta_api' => $resposta,
            ]);

            if ($res['ok']) {
                $this->json(200, ['success' => true, 'message' => 'E-mail enviado com sucesso']);
                return;
            }

            $this->json(500, [
                'success' => false,
                'message' => 'Falha ao enviar e-mail',
                'detail' => $res['body'],
            ]);
        } catch (Throwable $e) {
            $this->json(500, ['success' => false, 'message' => 'Erro interno', 'detail' => $e->getMessage()]);
        }
    }

    private function readApiKey(): string
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $name => $value) {
                    if (strtolower((string) $name) === 'x-api-key' && is_string($value)) {
                        return trim($value);
                    }
                }
            }
        }
        $h = $_SERVER['HTTP_X_API_KEY'] ?? '';
        return is_string($h) && $h !== '' ? trim($h) : '';
    }

    private function corsHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: X-API-KEY, Content-Type, Accept');
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function json(int $code, array $payload): void
    {
        $this->corsHeaders();
        http_response_code($code);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
