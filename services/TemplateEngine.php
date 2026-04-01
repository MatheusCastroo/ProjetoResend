<?php

class TemplateEngine
{
    /**
     * Substitui placeholders {{chave}} no HTML/texto.
     * Valores escalares são escapados para HTML; use _html suffix ou chaves em $rawKeys para inserir HTML bruto.
     *
     * @param array<string, mixed> $data
     * @param array<string, bool> $rawKeys chaves que não devem ser escapadas (ex.: conteúdo já em HTML)
     */
    public function render(string $html, array $data, array $rawKeys = []): string
    {
        return (string) preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            function (array $m) use ($data, $rawKeys): string {
                $key = $m[1];
                if (!array_key_exists($key, $data)) {
                    return '';
                }
                $val = $data[$key];
                if ($val === null) {
                    return '';
                }
                if (is_array($val) || is_object($val)) {
                    $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                } else {
                    $val = (string) $val;
                }
                if (isset($rawKeys[$key]) && $rawKeys[$key]) {
                    return $val;
                }
                return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
            },
            $html
        );
    }

    /**
     * Extrai nomes de variáveis a partir do JSON do template (lista de strings ou objetos com "key").
     *
     * @return list<string>
     */
    public static function variaveisFromJson(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $out[] = $item;
            } elseif (is_array($item) && isset($item['key'])) {
                $out[] = (string) $item['key'];
            }
        }
        return array_values(array_unique($out));
    }
}
