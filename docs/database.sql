CREATE DATABASE u722026046_textpro;

CREATE USER 'u722026046_textpro'@'localhost' IDENTIFIED BY '#TeXT9r0#';
GRANT ALL PRIVILEGES ON * . * TO 'u722026046_textpro'@'localhost' ;
FLUSH PRIVILEGES;


-- 1. Tabela de Usuários (Admins e Clientes)
CREATE TABLE system_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente',
    stripe_customer_id VARCHAR(100) NULL, -- ID do cliente no Stripe
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE system_users
ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER tipo;

-- 2. Tabela de Planos
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    stripe_price_id VARCHAR(100) NOT NULL, -- ID do preço no Stripe
    recursos JSON NULL, -- Armazena limites (ex: total de documentos, acesso a MP3)
    status ENUM('ativo', 'inativo') DEFAULT 'ativo'
) ENGINE=InnoDB;

ALTER TABLE plans ADD limite_documentos INT DEFAULT NULL;


-- 3. Tabela de Assinaturas (Controle de Pagamento)
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    stripe_subscription_id VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL, -- active, trialing, canceled, past_due
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES system_users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id)
) ENGINE=InnoDB;

ALTER TABLE `subscriptions` ADD `trial_ends_at` DATETIME NULL AFTER `data_fim`; 

-- 4. Tabela de Templates (Definidos pelo Admin)
CREATE TABLE templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    
    -- Configurações de Página (Normas ABNT ou customizado)
    tamanho_papel VARCHAR(10) DEFAULT 'A4',
    margem_superior FLOAT DEFAULT 3.0, -- em cm
    margem_inferior FLOAT DEFAULT 2.0, -- em cm
    margem_esquerda FLOAT DEFAULT 3.0, -- em cm
    margem_direita FLOAT DEFAULT 2.0, -- em cm
    
    -- Configurações de Texto
    fonte_familia VARCHAR(50) DEFAULT 'Arial',
    fonte_tamanho INT DEFAULT 12,
    alinhamento VARCHAR(20) DEFAULT 'justify',
    entre_linhas FLOAT DEFAULT 1.5,
    
    -- Elementos de Layout
    cabecalho_html TEXT NULL,
    rodape_html TEXT NULL,
    posicao_numeracao ENUM('superior_direita', 'inferior_direita', 'oculto') DEFAULT 'superior_direita',
    
    -- Capa Predefinida (Estrutura em JSON ou HTML base)
    template_capa_html LONGTEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. Tabela de Documentos (Onde o Cliente escreve)
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    conteudo_html LONGTEXT, -- Conteúdo vindo do editor de texto
    prefacio TEXT NULL,
    capa_personalizada JSON NULL, -- Dados específicos da capa (título, autor, ano)
    gerar_indice BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES system_users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id)
) ENGINE=InnoDB;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(150) NULL,
    ativo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    descricao VARCHAR(150) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),

    CONSTRAINT fk_user_roles_user
        FOREIGN KEY (user_id) REFERENCES system_users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user_roles_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE
);

CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),

    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON DELETE CASCADE
);

CREATE TABLE plan_features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    feature_key VARCHAR(100) NOT NULL,
    enabled TINYINT(1) DEFAULT 1,
    UNIQUE(plan_id, feature_key),
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
);

CREATE TABLE document_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    year INT NOT NULL,
    month INT NOT NULL,
    total INT DEFAULT 0,
    UNIQUE(user_id, year, month)
);

INSERT INTO `plans` (`id`, `nome`, `descricao`, `preco`, `stripe_price_id`, `recursos`, `status`) VALUES (NULL, 'Básico', 'Plano Básico Mensal', '20', '', NULL, 'ativo'), (NULL, 'Premium', 'Plano Premium Mensal', '50', '', NULL, 'ativo'); 


-- Plano Básico
INSERT INTO plan_features (plan_id, feature_key) VALUES
(1, 'editor_access'),
(1, 'export_pdf');

-- Plano Premium
INSERT INTO plan_features (plan_id, feature_key) VALUES
(2, 'editor_access'),
(2, 'export_pdf'),
(2, 'templates_premium'),
(2, 'multiple_documents');

INSERT INTO roles (id, nome) VALUES
(1, 'admin'),
(2, 'cliente');

INSERT INTO permissions (chave, descricao) VALUES
('admin.access', 'Acesso administrativo'),
('users.manage', 'Gerenciar usuários'),
('plans.manage', 'Gerenciar planos'),
('templates.manage', 'Gerenciar templates'),
('subscriptions.manage', 'Gerenciar assinaturas'),
('client.dashboard', 'Dashboard do cliente'),
('documents.edit', 'Editar documentos'),
('documents.save', 'Salvar documentos'),
('documents.export', 'Exportar PDF');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions
WHERE chave IN ('client.dashboard','documents.edit','documents.save');

-- ATRIBUI ADMIN AO SEU USUÁRIO
INSERT INTO user_roles (user_id, role_id)
VALUES (1, 1);

INSERT INTO permissions (chave, descricao)
VALUES
('admin.dashboard',   'Acesso ao dashboard administrativo'),
('templates.manage',  'Listar templates'),
('templates.create',  'Criar templates'),
('templates.edit',    'Editar templates'),
('templates.delete',  'Excluir templates')
ON DUPLICATE KEY UPDATE chave = chave;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.chave IN (
    'admin.dashboard',
    'templates.manage',
    'templates.create',
    'templates.edit',
    'templates.delete'
)
WHERE r.nome = 'admin'
ON DUPLICATE KEY UPDATE role_id = role_id;


ALTER TABLE plans
ADD COLUMN duracao_meses INT NOT NULL DEFAULT 1 AFTER preco;

UPDATE plans
SET recursos = JSON_UNQUOTE(recursos)
WHERE JSON_VALID(JSON_UNQUOTE(recursos));

INSERT INTO permissions (chave, descricao) VALUES
('users.create', 'Criar usuários'),
('users.edit',   'Editar usuários'),
('users.delete', 'Excluir usuários'),
('roles.manage', 'Gerenciar roles');




