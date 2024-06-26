-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS curriculos_db;
USE curriculos_db;

-- Criação da tabela para armazenar os dados do formulário principal
CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    idade INT NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    objetivo_profissional TEXT NOT NULL,
    cep VARCHAR(8) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(255) NOT NULL,
    numero_casa INT NOT NULL,
    telefone1 VARCHAR(15) NOT NULL,
    telefone2 VARCHAR(15),
    email VARCHAR(255) NOT NULL,
    formacao ENUM('cursando_ensino_medio', 'ensino_medio_completo', 'cursando_ensino_superior', 'ensino_superior_completo', 'pos_graduacao_completa') NOT NULL
    nome_curso VARCHAR(255) NOT NULL,
    instituicao VARCHAR(255) NOT NULL,
    duracao VARCHAR(50) NOT NULL,
    conclusao VARCHAR(20) NOT NULL,
);
