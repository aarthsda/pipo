-- =============================================================================
-- Migration 001 — Criar tabela biometria_facial
-- Projeto    : Pipo
-- Branch     : manutencao/mongo-to-mysql
-- Base-commit: 96fc106
-- Data       : 2026-10-08
-- Objetivo   : Substituir armazenamento MongoDB (mongoose) por MySQL para
--              os descritores faciais do módulo _Reconhecimento.
-- Reversão   : Executar a seção ROLLBACK ao final deste arquivo.
-- Pré-req    : Banco 'pipo' existente com tabela 'usuario' (BD_pipo.txt).
-- =============================================================================

-- ── APLICAR ──────────────────────────────────────────────────────────────────

USE `pipo`;

CREATE TABLE IF NOT EXISTS `biometria_facial` (
  -- Chave primária própria
  `id_biometria`      INT          NOT NULL AUTO_INCREMENT,

  -- Referência ao usuário do sistema principal (FK na PK — segura sem UNIQUE extra)
  -- Permite NULL para o caso de usuário biométrico ainda não vinculado ao PHP app.
  `id_usuario`        INT          DEFAULT NULL,

  -- Matrícula armazenada diretamente para evitar JOIN no endpoint /api/reconhecer.
  -- Deve corresponder a usuario.matricula_usuario do mesmo registro.
  -- UNIQUE garante 1 descritor por matrícula (semântica do upsert atual).
  `matricula`         VARCHAR(19)  NOT NULL,

  -- Descritor facial: array de 128 floats gerado pelo face-api.js (FaceRecognitionNet).
  -- Armazenado como JSON para preservar precisão double e permitir reconstrução exata.
  -- TEXT suporta até 65 535 bytes; 128 floats em JSON ≈ 2-3 KB — suficiente.
  `face_descriptor`   TEXT         NOT NULL,

  -- Foto capturada no momento do cadastro (JPEG em Base64).
  -- LONGTEXT suporta até ~4 GB; uma foto 640×480 JPEG ≈ 50-200 KB em base64.
  -- Nullable: campo não consumido por nenhum endpoint ativo pós-cadastro.
  `imagem_base64`     LONGTEXT     DEFAULT NULL,

  -- Timestamp de criação/última atualização do registro biométrico.
  `data_cadastro`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id_biometria`),

  -- Garante 1 descritor por matrícula — preserva semântica de upsert do MongoDB.
  UNIQUE KEY `uq_matricula` (`matricula`),

  -- FK opcional para o sistema principal. ON DELETE SET NULL permite remover
  -- o usuário PHP sem apagar os dados biométricos (decisão conservadora).
  CONSTRAINT `fk_biometria_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `usuario` (`id_usuario`)
    ON DELETE SET NULL

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci
  COMMENT='Descritores faciais do módulo _Reconhecimento. Substitui MongoDB.';


-- =============================================================================
-- ROLLBACK (executar para desfazer esta migration)
-- =============================================================================
-- USE `pipo`;
-- DROP TABLE IF EXISTS `biometria_facial`;
-- =============================================================================
