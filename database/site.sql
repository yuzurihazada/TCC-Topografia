-- Criação do banco
CREATE DATABASE IF NOT EXISTS `tcc_topografia`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `tcc_topografia`;

-- Tabela de serviços
CREATE TABLE IF NOT EXISTS `servicos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(150) NOT NULL,
  `descricao` TEXT NOT NULL,
  `preco` DECIMAL(10,2) NULL,              -- preço base (exibir “a partir de”)
  `order_index` INT NOT NULL DEFAULT 0,    -- ordenação
  `active` TINYINT(1) NOT NULL DEFAULT 1,  -- ativar/desativar
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de FAQs
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pergunta` VARCHAR(255) NOT NULL,
  `resposta` TEXT NOT NULL,
  `order_index` INT NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de feedbacks
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NULL,
  `mensagem` TEXT NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de contatos (formulário Contato)
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `message` TEXT NOT NULL,
  `source` VARCHAR(50) NOT NULL DEFAULT 'site',
  `status` ENUM('new','in_progress','done') NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacts_status` (`status`),
  KEY `idx_contacts_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de configurações (settings simples: chave/valor)
CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados iniciais (opcionais)
INSERT INTO `servicos` (titulo, descricao, preco) VALUES
('Levantamento Planialtimétrico', 'Levantamento detalhado com curvas de nível.', 1500.00),
('Georreferenciamento', 'Georreferenciamento conforme normas.', 2500.00);

INSERT INTO `faqs` (pergunta, resposta) VALUES
('Qual o prazo médio para um levantamento?', 'Depende da área e complexidade; em média 3 a 7 dias.'),
('Vocês atendem fora da cidade?', 'Sim, mediante orçamento e logística.');

-- Settings iniciais (pode editar depois no Admin futuramente)
INSERT INTO `settings` (`key`, `value`) VALUES
('company_name', 'ALFA TOP - Serviços topográficos'),
('trt_label', 'TRT'),
('trt_number', ''),
('whatsapp_number', '+5515981194365'),
('email', 'alfatopst@gmail.com'),
('instagram_handle', '@alessandro.topografia'),
('instagram_url', 'https://instagram.com/alessandro.topografia'),
('facebook_url', 'https://www.facebook.com/share/1BFWR7WdN3/'),
('regions_text', 'Atende a aproximadamente um raio de até 100 km de Tatuí'),
('hours_text', 'Seg–Sex, 8h–17h'),
('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento');
