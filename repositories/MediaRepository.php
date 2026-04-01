<?php

class MediaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function byApplication(int $applicationId): array
    {
        $st = $this->db->prepare(
            'SELECT * FROM media WHERE application_id = ? ORDER BY created_at DESC'
        );
        $st->execute([$applicationId]);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM media WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO media (application_id, nome, url, provider) VALUES (?, ?, ?, ?)'
        );
        $st->execute([
            $data['application_id'],
            $data['nome'],
            $data['url'],
            $data['provider'] ?? 'cloudinary',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id, int $applicationId): void
    {
        $st = $this->db->prepare('DELETE FROM media WHERE id = ? AND application_id = ?');
        $st->execute([$id, $applicationId]);
    }
}
