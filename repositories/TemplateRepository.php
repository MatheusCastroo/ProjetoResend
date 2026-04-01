<?php

class TemplateRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function byCliente(int $clienteId): array
    {
        $st = $this->db->prepare(
            'SELECT t.* FROM templates t WHERE t.cliente_id = ? ORDER BY t.nome'
        );
        $st->execute([$clienteId]);
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
            'INSERT INTO templates (cliente_id, template_base_id, nome, assunto, html, variaveis)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $data['cliente_id'],
            $data['template_base_id'] ?: null,
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
            'UPDATE templates SET template_base_id = ?, nome = ?, assunto = ?, html = ?, variaveis = ?
             WHERE id = ? AND cliente_id = ?'
        );
        $st->execute([
            $data['template_base_id'] ?: null,
            $data['nome'],
            $data['assunto'],
            $data['html'],
            $variaveis,
            $id,
            $data['cliente_id'],
        ]);
    }

    public function delete(int $id, int $clienteId): void
    {
        $st = $this->db->prepare('DELETE FROM templates WHERE id = ? AND cliente_id = ?');
        $st->execute([$id, $clienteId]);
    }
}
