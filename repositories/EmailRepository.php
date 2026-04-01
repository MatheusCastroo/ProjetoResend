<?php

class EmailRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    public function all(?int $clienteId = null, int $limit = 200): array
    {
        if ($clienteId !== null) {
            $st = $this->db->prepare(
                'SELECT e.*, c.nome AS cliente_nome FROM emails e
                 JOIN clientes c ON c.id = e.cliente_id
                 WHERE e.cliente_id = ?
                 ORDER BY e.data_envio DESC
                 LIMIT ' . (int) $limit
            );
            $st->execute([$clienteId]);
        } else {
            $st = $this->db->query(
                'SELECT e.*, c.nome AS cliente_nome FROM emails e
                 JOIN clientes c ON c.id = e.cliente_id
                 ORDER BY e.data_envio DESC
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

    public function create(array $data): int
    {
        $st = $this->db->prepare(
            'INSERT INTO emails (cliente_id, destinatario, assunto, conteudo, status, resposta_api, data_envio)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $data['cliente_id'],
            $data['destinatario'],
            $data['assunto'],
            $data['conteudo'],
            $data['status'] ?? 'ENVIADO',
            $data['resposta_api'] ?? null,
            $data['data_envio'],
        ]);
        return (int) $this->db->lastInsertId();
    }
}
