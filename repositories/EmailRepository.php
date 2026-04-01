<?php

class EmailRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function all(?int $applicationId = null, int $limit = 500): array
    {
        if ($applicationId !== null) {
            $st = $this->db->prepare(
                'SELECT e.*, a.nome AS application_nome, t.nome AS template_nome
                 FROM emails e
                 JOIN applications a ON a.id = e.application_id
                 LEFT JOIN templates t ON t.id = e.template_id
                 WHERE e.application_id = ?
                 ORDER BY e.created_at DESC
                 LIMIT ' . (int) $limit
            );
            $st->execute([$applicationId]);
        } else {
            $st = $this->db->query(
                'SELECT e.*, a.nome AS application_nome, t.nome AS template_nome
                 FROM emails e
                 JOIN applications a ON a.id = e.application_id
                 LEFT JOIN templates t ON t.id = e.template_id
                 ORDER BY e.created_at DESC
                 LIMIT ' . (int) $limit
            );
        }
        return $st->fetchAll();
    }

    public function countTotal(): int
    {
        $st = $this->db->query('SELECT COUNT(*) AS n FROM emails');
        $row = $st->fetch();
        return (int) ($row['n'] ?? 0);
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare(
            'SELECT e.*, a.nome AS application_nome FROM emails e
             JOIN applications a ON a.id = e.application_id WHERE e.id = ?'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * @param array{application_id: int, template_id?: int|null, destinatario: string, assunto: string, conteudo: string, status: string, resposta_api?: string|null} $data
     */
    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO emails (application_id, template_id, destinatario, assunto, conteudo, status, resposta_api)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $data['application_id'],
            $data['template_id'] ?? null,
            $data['destinatario'],
            $data['assunto'],
            $data['conteudo'],
            $data['status'] ?? 'ENVIADO',
            $data['resposta_api'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
