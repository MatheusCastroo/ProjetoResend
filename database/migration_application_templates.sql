-- Cria application_templates e, se existir templates.application_id (schema antigo), migra os vínculos.
-- Execute no banco projeto_resend (phpMyAdmin ou: mysql -u root projeto_resend < database/migration_application_templates.sql)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS application_templates (
  application_id INT UNSIGNED NOT NULL,
  template_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (application_id, template_id),
  CONSTRAINT fk_at_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  CONSTRAINT fk_at_tpl FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
  INDEX idx_at_template (template_id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- Migração opcional: só roda se a coluna application_id ainda existir em templates
SET @dbname = DATABASE();
SET @has_legacy = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'templates' AND COLUMN_NAME = 'application_id'
);
SET @migrate = IF(
  @has_legacy > 0,
  'INSERT IGNORE INTO application_templates (application_id, template_id) SELECT application_id, id FROM templates WHERE application_id IS NOT NULL',
  'SELECT 1 AS skipped_legacy_migration'
);
PREPARE stmt FROM @migrate;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Remove coluna legada templates.application_id (o código novo só usa application_templates).
-- Sem isso, INSERT em templates sem application_id pode virar 0 e quebrar fk_templates_app.
SET @has_legacy2 = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'templates' AND COLUMN_NAME = 'application_id'
);
SET @fkname = (
  SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'templates'
    AND COLUMN_NAME = 'application_id' AND REFERENCED_TABLE_NAME IS NOT NULL
  LIMIT 1
);
SET @dropfk = IF(
  @has_legacy2 > 0 AND @fkname IS NOT NULL,
  CONCAT('ALTER TABLE templates DROP FOREIGN KEY `', @fkname, '`'),
  'SELECT 1 AS skip_drop_fk'
);
PREPARE stmt2 FROM @dropfk;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

SET @dropcol = IF(
  @has_legacy2 > 0,
  'ALTER TABLE templates DROP COLUMN application_id',
  'SELECT 1 AS skip_drop_col'
);
PREPARE stmt3 FROM @dropcol;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;
