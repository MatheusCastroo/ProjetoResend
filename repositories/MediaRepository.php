<?php

class MediaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function byCliente(int $clienteId): array
    {
        $st = $this->db->prepare(
            'SELECT * FROM midias WHERE cliente_id = ? ORDER BY created_at DESC'
        );
        $st->execute([$clienteId]);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM midias WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO midias (cliente_id, nome, url, provider) VALUES (?, ?, ?, ?)'
        );
        $st->execute([
            $data['cliente_id'],
            $data['nome'],
            $data['url'],
            $data['provider'] ?? 'cloudinary',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id, int $clienteId): void
    {
        $st = $this->db->prepare('DELETE FROM midias WHERE id = ? AND cliente_id = ?');
        $st->execute([$id, $clienteId]);
    }
}
