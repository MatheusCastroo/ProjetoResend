<?php

/**
 * Orquestra compose + envio Resend + log em emails (API send/event).
 */
class TransactionalMailSender
{
    /**
     * @param array<string, mixed> $application Application row (resend_*, branding)
     * @param array<string, mixed> $template    Template row
     * @param array<string, mixed> $data        Variáveis extras para o template
     * @return array{ok: bool, http: int, body: string, decoded: mixed}
     */
    public function send(array $application, array $template, string $recipientEmail, array $data): array
    {
        $templateId = (int) $template['id'];

        $composer = new ApplicationMailComposer(new TemplateEngine());
        $out = $composer->compose($application, $template, $data);

        $mail = new EmailService();
        $res = $mail->sendWithCredentials(
            (string) $application['resend_api_key'],
            (string) ($application['resend_from'] ?? 'onboarding@resend.dev'),
            $recipientEmail,
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
            'destinatario' => $recipientEmail,
            'assunto' => $out['assunto'],
            'conteudo' => $out['html'],
            'status' => $status,
            'resposta_api' => $resposta,
        ]);

        return $res;
    }
}
