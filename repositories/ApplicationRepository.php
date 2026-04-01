<?php

class ApplicationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM applications ORDER BY nome')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM applications WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function findByApiKey(string $apiKey): ?array
    {
        $st = $this->db->prepare('SELECT * FROM applications WHERE api_key = ? LIMIT 1');
        $st->execute([$apiKey]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO applications (nome, api_key, resend_api_key, resend_from, logo_url, cor_primaria, cor_secundaria)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $data['nome'],
            $data['api_key'],
            $data['resend_api_key'],
            $data['resend_from'] ?? 'onboarding@resend.dev',
            $data['logo_url'] ?? null,
            $data['cor_primaria'] ?? '#6366f1',
            $data['cor_secundaria'] ?? '#111827',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $st = $this->db->prepare(
            'UPDATE applications SET nome = ?, resend_api_key = ?, resend_from = ?, logo_url = ?, cor_primaria = ?, cor_secundaria = ?
             WHERE id = ?'
        );
        $st->execute([
            $data['nome'],
            $data['resend_api_key'],
            $data['resend_from'] ?? 'onboarding@resend.dev',
            $data['logo_url'] ?? null,
            $data['cor_primaria'] ?? '#6366f1',
            $data['cor_secundaria'] ?? '#111827',
            $id,
        ]);
    }

    /** Regenera API Key (painel). */
    public function updateApiKey(int $id, string $apiKey): void
    {
        $st = $this->db->prepare('UPDATE applications SET api_key = ? WHERE id = ?');
        $st->execute([$apiKey, $id]);
    }

    public function delete(int $id): void
    {
        $st = $this->db->prepare('DELETE FROM applications WHERE id = ?');
        $st->execute([$id]);
    }
}
