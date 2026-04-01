<?php

/**
 * Monta HTML final do e-mail (base do cliente, template base, variáveis).
 */
class MailComposerService
{
    public function __construct(
        private TemplateEngine $engine,
        private TemplateBaseRepository $bases
    ) {
    }

    /**
     * @param array<string, mixed> $variaveisUsuario
     * @return array{html: string, assunto: string}
     */
    public function compose(array $cliente, array $template, array $variaveisUsuario): array
    {
        $data = array_merge($this->brandingData($cliente), $variaveisUsuario);

        $innerHtml = (string) $template['html'];

        if (!empty($template['template_base_id'])) {
            $base = $this->bases->find((int) $template['template_base_id']);
            if ($base && (int) $base['cliente_id'] === (int) $cliente['id']) {
                $innerHtml = str_replace('{{conteudo}}', $innerHtml, (string) $base['html']);
            }
        } elseif (!empty($cliente['layout_padrao'])) {
            $innerHtml = str_replace('{{conteudo}}', $innerHtml, (string) $cliente['layout_padrao']);
        }

        $assunto = $this->engine->render((string) $template['assunto'], $data);
        $html = $this->engine->render($innerHtml, $data);

        return ['html' => $html, 'assunto' => $assunto];
    }

    /**
     * @return array<string, string>
     */
    private function brandingData(array $cliente): array
    {
        return [
            'nome_cliente' => (string) ($cliente['nome'] ?? ''),
            'logo' => (string) ($cliente['logo_url'] ?? ''),
            'cor_primaria' => (string) ($cliente['cor_primaria'] ?? '#2563eb'),
            'cor_secundaria' => (string) ($cliente['cor_secundaria'] ?? '#1e293b'),
        ];
    }
}
