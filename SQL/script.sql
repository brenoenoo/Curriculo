CREATE TABLE perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    bio TEXT,
    dados_pessoais TEXT,
    foto_url VARCHAR(255) DEFAULT 'default_profile.jpg',
    banner_url VARCHAR(255) DEFAULT 'default_banner.jpg'
);

INSERT INTO perfil (nome, bio, dados_pessoais) 
VALUES ('Seu Nome', 'Sua biografia profissional aqui.', 'Email: contato@exemplo.com | Cel: (11) 99999-9999');

CREATE TABLE experiencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cargo VARCHAR(100),
    empresa VARCHAR(100),
    periodo VARCHAR(50),
    descricao TEXT
);

CREATE TABLE formacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso VARCHAR(100),
    instituicao VARCHAR(100),
    periodo VARCHAR(50)
);

CREATE TABLE competencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50)
);