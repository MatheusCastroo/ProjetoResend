<?php

class TemplateRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    /**
     * Lista todos os templates com applications vinculadas (admin).
     *
     * @return list<array<string, mixed>>
     */
    public function allForAdmin(): array
    {
        $st = $this->db->query(
            "SELECT t.*,
                    GROUP_CONCAT(a.nome ORDER BY a.nome SEPARATOR ', ') AS applications_nomes,
                    COUNT(at.application_id) AS apps_count
             FROM templates t
             LEFT JOIN application_templates at ON at.template_id = t.id
             LEFT JOIN applications a ON a.id = at.application_id
             GROUP BY t.id
             ORDER BY t.nome"
        );
        return $st->fetchAll();
    }

    public function byApplication(int $applicationId): array
    {
        $st = $this->db->prepare(
            'SELECT t.* FROM templates t
             INNER JOIN application_templates at ON at.template_id = t.id
             WHERE at.application_id = ?
             ORDER BY t.nome'
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

    /**
     * IDs das applications que usam este template.
     *
     * @return list<int>
     */
    public function linkedApplicationIds(int $templateId): array
    {
        $st = $this->db->prepare(
            'SELECT application_id FROM application_templates WHERE template_id = ? ORDER BY application_id'
        );
        $st->execute([$templateId]);
        return array_map('intval', array_column($st->fetchAll(), 'application_id'));
    }

    public function isLinked(int $applicationId, int $templateId): bool
    {
        $st = $this->db->prepare(
            'SELECT 1 FROM application_templates WHERE application_id = ? AND template_id = ? LIMIT 1'
        );
        $st->execute([$applicationId, $templateId]);
        return (bool) $st->fetch();
    }

    /**
     * Templates ainda não vinculados à application (para tela “vincular”).
     *
     * @return list<array<string, mixed>>
     */
    public function unattachedForApplication(int $applicationId): array
    {
        $st = $this->db->prepare(
            'SELECT t.id, t.nome, t.assunto FROM templates t
             WHERE t.id NOT IN (
               SELECT template_id FROM application_templates WHERE application_id = ?
             )
             ORDER BY t.nome'
        );
        $st->execute([$applicationId]);
        return $st->fetchAll();
    }

    public function create(array $data): int
    {
        $variaveis = $data['variaveis'] ?? null;
        if (is_array($variaveis)) {
            $variaveis = json_encode($variaveis, JSON_UNESCAPED_UNICODE);
        }
        $st = $this->db->prepare(
            'INSERT INTO templates (nome, assunto, html, variaveis) VALUES (?, ?, ?, ?)'
        );
        $st->execute([
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
            'UPDATE templates SET nome = ?, assunto = ?, html = ?, variaveis = ? WHERE id = ?'
        );
        $st->execute([
            $data['nome'],
            $data['assunto'],
            $data['html'],
            $variaveis,
            $id,
        ]);
    }

    /**
     * Substitui vínculos: ao menos um application_id em produção é recomendado.
     *
     * @param list<int> $applicationIds
     */
    public function syncApplicationLinks(int $templateId, array $applicationIds): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $applicationIds), static function ($x) {
            return $x > 0;
        })));
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare('DELETE FROM application_templates WHERE template_id = ?');
            $st->execute([$templateId]);
            $ins = $this->db->prepare(
                'INSERT INTO application_templates (application_id, template_id) VALUES (?, ?)'
            );
            foreach ($ids as $aid) {
                $ins->execute([$aid, $templateId]);
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function attach(int $applicationId, int $templateId): void
    {
        $st = $this->db->prepare(
            'INSERT IGNORE INTO application_templates (application_id, template_id) VALUES (?, ?)'
        );
        $st->execute([$applicationId, $templateId]);
    }

    public function detach(int $applicationId, int $templateId): void
    {
        $st = $this->db->prepare(
            'DELETE FROM application_templates WHERE application_id = ? AND template_id = ?'
        );
        $st->execute([$applicationId, $templateId]);
    }

    /** Remove o template e vínculos (CASCADE em application_templates). */
    public function deleteTemplate(int $id): void
    {
        $st = $this->db->prepare('DELETE FROM templates WHERE id = ?');
        $st->execute([$id]);
    }
}
