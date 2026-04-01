-- ProjetoResend - schema multi-tenant
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS projeto_resend
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE projeto_resend;

DROP TABLE IF EXISTS emails;
DROP TABLE IF EXISTS midias;
DROP TABLE IF EXISTS templates;
DROP TABLE IF EXISTS template_bases;
DROP TABLE IF EXISTS clientes;

CREATE TABLE clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  logo_url VARCHAR(2048) NULL,
  cor_primaria VARCHAR(32) NOT NULL DEFAULT '#2563eb',
  cor_secundaria VARCHAR(32) NOT NULL DEFAULT '#1e293b',
  layout_padrao MEDIUMTEXT NULL COMMENT 'HTML opcional com {{conteudo}}',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE template_bases (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  nome VARCHAR(255) NOT NULL,
  html MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tb_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  INDEX idx_tb_cliente (cliente_id)
) ENGINE=InnoDB;

CREATE TABLE templates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  template_base_id INT UNSIGNED NULL,
  nome VARCHAR(255) NOT NULL,
  assunto VARCHAR(500) NOT NULL,
  html MEDIUMTEXT NOT NULL,
  variaveis JSON NULL COMMENT 'Lista de nomes ou definições de variáveis',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tpl_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  CONSTRAINT fk_tpl_base FOREIGN KEY (template_base_id) REFERENCES template_bases(id) ON DELETE SET NULL,
  INDEX idx_tpl_cliente (cliente_id)
) ENGINE=InnoDB;

CREATE TABLE midias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  nome VARCHAR(255) NOT NULL,
  url VARCHAR(2048) NOT NULL,
  provider VARCHAR(64) NOT NULL DEFAULT 'cloudinary',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mid_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  INDEX idx_mid_cliente (cliente_id)
) ENGINE=InnoDB;

CREATE TABLE emails (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  destinatario VARCHAR(500) NOT NULL,
  assunto VARCHAR(500) NOT NULL,
  conteudo MEDIUMTEXT NOT NULL,
  status ENUM('PENDENTE','ENVIADO','ERRO') NOT NULL DEFAULT 'ENVIADO',
  resposta_api MEDIUMTEXT NULL,
  data_envio DATETIME NOT NULL,
  CONSTRAINT fk_em_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  INDEX idx_em_cliente (cliente_id),
  INDEX idx_em_data (data_envio)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
