<?php

/**
 * POST /api/event/{event_key}
 * Header: X-API-KEY
 * Body JSON: { "email": "...", "data": { ... } }
 */
class ApiEventController
{
    public function dispatch(string $eventKeyRaw): void
    {
        $this->corsHeaders();

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        $eventKey = trim(rawurldecode($eventKeyRaw));
        if ($eventKey === '') {
            $this->json(404, ['success' => false, 'message' => 'event_key não encontrado']);
            return;
        }

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
        $template = $tplRepo->findFirstLinkedByEventKey((int) $application['id'], $eventKey);

        if (!$template) {
            if ($tplRepo->countByEventKey($eventKey) > 0) {
                $this->json(401, ['success' => false, 'message' => 'Template não vinculado a esta application']);
                return;
            }
            $this->json(404, ['success' => false, 'message' => 'event_key não encontrado']);
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
            $sender = new TransactionalMailSender();
            $res = $sender->send($application, $template, $email, $data);

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
