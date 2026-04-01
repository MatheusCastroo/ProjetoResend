<?php

class TemplateRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function allForAdmin(): array
    {
        $st = $this->db->query(
            'SELECT t.*, a.nome AS application_nome FROM templates t
             JOIN applications a ON a.id = t.application_id
             ORDER BY a.nome, t.nome'
        );
        return $st->fetchAll();
    }

    public function byApplication(int $applicationId): array
    {
        $st = $this->db->prepare(
            'SELECT * FROM templates WHERE application_id = ? ORDER BY nome'
        );
        $st->execute([$applicationId]);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM templates WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $variaveis = $data['variaveis'] ?? null;
        if (is_array($variaveis)) {
            $variaveis = json_encode($variaveis, JSON_UNESCAPED_UNICODE);
        }
        $st = $this->db->prepare(
            'INSERT INTO templates (application_id, nome, assunto, html, variaveis)
             VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([
            $data['application_id'],
            $data['nome'],
            $data['assunto'],
            $data['html'],
            $variaveis,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $variaveis = $data['variaveis'] ?? null;
        if (is_array($variaveis)) {
            $variaveis = json_encode($variaveis, JSON_UNESCAPED_UNICODE);
        }
        $st = $this->db->prepare(
            'UPDATE templates SET nome = ?, assunto = ?, html = ?, variaveis = ?
             WHERE id = ? AND application_id = ?'
        );
        $st->execute([
            $data['nome'],
            $data['assunto'],
            $data['html'],
            $variaveis,
            $id,
            $data['application_id'],
        ]);
    }

    public function delete(int $id, int $applicationId): void
    {
        $st = $this->db->prepare('DELETE FROM templates WHERE id = ? AND application_id = ?');
        $st->execute([$id, $applicationId]);
    }
}
