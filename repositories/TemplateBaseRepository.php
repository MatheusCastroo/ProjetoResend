<?php

class TemplateBaseRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function byCliente(int $clienteId): array
    {
        $st = $this->db->prepare(
            'SELECT * FROM template_bases WHERE cliente_id = ? ORDER BY nome'
        );
        $st->execute([$clienteId]);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM template_bases WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO template_bases (cliente_id, nome, html) VALUES (?, ?, ?)'
        );
        $st->execute([$data['cliente_id'], $data['nome'], $data['html']]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $st = $this->db->prepare(
            'UPDATE template_bases SET nome = ?, html = ? WHERE id = ? AND cliente_id = ?'
        );
        $st->execute([$data['nome'], $data['html'], $id, $data['cliente_id']]);
    }

    public function delete(int $id, int $clienteId): void
    {
        $st = $this->db->prepare('DELETE FROM template_bases WHERE id = ? AND cliente_id = ?');
        $st->execute([$id, $clienteId]);
    }
}
