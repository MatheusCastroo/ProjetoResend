-- ProjetoResend — SaaS multi-tenant (Applications + API)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS projeto_resend
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE projeto_resend;

DROP TABLE IF EXISTS emails;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS templates;
DROP TABLE IF EXISTS applications;

CREATE TABLE applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  api_key VARCHAR(128) NOT NULL,
  resend_api_key VARCHAR(512) NOT NULL,
  resend_from VARCHAR(255) NOT NULL DEFAULT 'onboarding@resend.dev',
  logo_url VARCHAR(2048) NULL,
  cor_primaria VARCHAR(32) NOT NULL DEFAULT '#6366f1',
  cor_secundaria VARCHAR(32) NOT NULL DEFAULT '#111827',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_applications_api_key (api_key),
  INDEX idx_applications_nome (nome)
) ENGINE=InnoDB;

CREATE TABLE templates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  nome VARCHAR(255) NOT NULL,
  assunto VARCHAR(500) NOT NULL,
  html MEDIUMTEXT NOT NULL,
  variaveis JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_templates_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  INDEX idx_templates_app (application_id)
) ENGINE=InnoDB;

CREATE TABLE emails (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  template_id INT UNSIGNED NULL,
  destinatario VARCHAR(500) NOT NULL,
  assunto VARCHAR(500) NOT NULL,
  conteudo MEDIUMTEXT NOT NULL,
  status ENUM('PENDENTE','ENVIADO','ERRO') NOT NULL DEFAULT 'ENVIADO',
  resposta_api MEDIUMTEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_emails_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  CONSTRAINT fk_emails_tpl FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE SET NULL,
  INDEX idx_emails_app (application_id),
  INDEX idx_emails_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  nome VARCHAR(255) NOT NULL,
  url VARCHAR(2048) NOT NULL,
  provider VARCHAR(64) NOT NULL DEFAULT 'cloudinary',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_media_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  INDEX idx_media_app (application_id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
