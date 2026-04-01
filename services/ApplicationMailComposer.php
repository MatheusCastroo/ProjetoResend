<?php

/**
 * Monta assunto e HTML a partir do template e da identidade da application.
 */
class ApplicationMailComposer
{
    public function __construct(private TemplateEngine $engine)
    {
    }

    /**
     * @param array<string, mixed> $variaveis Request ou formulário (nome, link, etc.)
     * @return array{html: string, assunto: string}
     */
    public function compose(array $application, array $template, array $variaveis): array
    {
        $data = array_merge($this->brandingData($application), $variaveis);
        $assunto = $this->engine->render((string) $template['assunto'], $data);
        $html = $this->engine->render((string) $template['html'], $data);
        return ['html' => $html, 'assunto' => $assunto];
    }

    /**
     * @return array<string, string>
     */
    private function brandingData(array $application): array
    {
        return [
            'empresa' => (string) ($application['nome'] ?? ''),
            'logo' => (string) ($application['logo_url'] ?? ''),
            'cor_primaria' => (string) ($application['cor_primaria'] ?? '#6366f1'),
            'cor_secundaria' => (string) ($application['cor_secundaria'] ?? '#111827'),
        ];
    }
}
