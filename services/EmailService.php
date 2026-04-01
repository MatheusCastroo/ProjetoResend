<?php

class EmailService
{
    private string $apiKey;
    private string $from;

    public function __construct(?array $resendConfig = null)
    {
        $c = $resendConfig ?? config('resend');
        $this->apiKey = (string) ($c['api_key'] ?? '');
        $this->from = (string) ($c['from'] ?? '');
    }

    /**
     * @return array{ok: bool, http: int, body: string, decoded: mixed}
     */
    public function send(string $to, string $subject, string $html): array
    {
        if ($this->apiKey === '' || $this->apiKey === 're_xxxxxxxx') {
            return [
                'ok' => false,
                'http' => 0,
                'body' => 'Resend API key não configurada',
                'decoded' => null,
            ];
        }

        $payload = [
            'from' => $this->from,
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
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
