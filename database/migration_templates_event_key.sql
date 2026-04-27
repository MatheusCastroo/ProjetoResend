-- Adiciona event_key aos templates (envios por POST /api/event/{event_key})
SET NAMES utf8mb4;

ALTER TABLE templates
  ADD COLUMN event_key VARCHAR(128) NULL AFTER nome;

CREATE INDEX idx_templates_event_key ON templates (event_key);
