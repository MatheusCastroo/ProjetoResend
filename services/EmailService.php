<?php

class EmailService
{
    /**
     * Envio usando credenciais Resend da própria application (API ou painel).
     *
     * @return array{ok: bool, http: int, body: string, decoded: mixed}
     */
    public function sendWithCredentials(
        string $resendApiKey,
        string $from,
        string $to,
        string $subject,
        string $html
    ): array {
        $resendApiKey = trim($resendApiKey);
        if ($resendApiKey === '') {
            return [
                'ok' => false,
                'http' => 0,
                'body' => 'resend_api_key não configurada para esta aplicação',
                'decoded' => null,
            ];
        }

        $payload = [
            'from' => $from,
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $resendApiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $body = curl_exec($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode((string) $body, true);

        return [
            'ok' => $http >= 200 && $http < 300,
            'http' => $http,
            'body' => (string) $body,
            'decoded' => $decoded,
        ];
    }
}
