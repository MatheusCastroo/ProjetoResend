<?php

class ClienteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function all(): array
    {
        $st = $this->db->query('SELECT * FROM clientes ORDER BY nome');
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM clientes WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO clientes (nome, logo_url, cor_primaria, cor_secundaria, layout_padrao)
             VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([
            $data['nome'],
            $data['logo_url'] ?? null,
            $data['cor_primaria'] ?? '#2563eb',
            $data['cor_secundaria'] ?? '#1e293b',
            $data['layout_padrao'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $st = $this->db->prepare(
            'UPDATE clientes SET nome = ?, logo_url = ?, cor_primaria = ?, cor_secundaria = ?, layout_padrao = ?
             WHERE id = ?'
        );
        $st->execute([
            $data['nome'],
            $data['logo_url'] ?? null,
            $data['cor_primaria'] ?? '#2563eb',
            $data['cor_secundaria'] ?? '#1e293b',
            $data['layout_padrao'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $st = $this->db->prepare('DELETE FROM clientes WHERE id = ?');
        $st->execute([$id]);
    }
}
