<?php

class MediaService
{
    private string $url;
    private string $apiKey;
    private string $provider;

    public function __construct(?array $cawConfig = null)
    {
        $c = $cawConfig ?? config('caw_upload');
        $this->url = $c['url'];
        $this->apiKey = (string) ($c['api_key'] ?? '');
        $this->provider = (string) ($c['provider'] ?? 'cloudinary');
    }

    /**
     * @return array{url: string, raw: mixed}
     * @throws RuntimeException
     */
    public function uploadFile(string $tmpPath, string $originalName, string $mime = 'application/octet-stream'): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('Configure caw_upload.api_key em config/config.php');
        }

        if (!is_uploaded_file($tmpPath) && !is_readable($tmpPath)) {
            throw new RuntimeException('Arquivo inválido ou inacessível');
        }

        $cfile = curl_file_create($tmpPath, $mime, $originalName);
        $post = [
            'file' => $cfile,
            'provider' => $this->provider,
        ];

        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
        ]);

        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new RuntimeException('Upload falhou: ' . $err);
        }

        $json = json_decode($body, true);
        $url = null;
        if (is_array($json)) {
            $url = $json['url'] ?? $json['data']['url'] ?? $json['secure_url'] ?? null;
            if (is_string($url) && $url !== '') {
                return ['url' => $url, 'raw' => $json];
            }
        }

        throw new RuntimeException(
            'Resposta inválida do upload (HTTP ' . $code . '): ' . mb_substr($body, 0, 500)
        );
    }
}
