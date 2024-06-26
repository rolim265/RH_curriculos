<?php
session_start();

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Configurações do banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "curriculos_db";

    // Conectar ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Escapar e coletar os dados do formulário
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $idade = mysqli_real_escape_string($conn, $_POST['idade']);
    $cpf = mysqli_real_escape_string($conn, $_POST['cpf']);
    $obje = mysqli_real_escape_string($conn, $_POST['obje']);
    $cep = mysqli_real_escape_string($conn, $_POST['cep']);
    $endereco = mysqli_real_escape_string($conn, $_POST['endereco']);
    $cidade = mysqli_real_escape_string($conn, $_POST['cidade']);
    $numero = mysqli_real_escape_string($conn, $_POST['numero']);
    $telefone1 = mysqli_real_escape_string($conn, $_POST['telefone1']);
    $telefone2 = isset($_POST['telefone2']) ? mysqli_real_escape_string($conn, $_POST['telefone2']) : NULL;
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $formacao = mysqli_real_escape_string($conn, $_POST['formacao']);
    $cursos = isset($_POST['cursos']) ? $_POST['cursos'] : [];

    // Preparar a instrução SQL para inserir dados na tabela candidatos
    $stmt = $conn->prepare("INSERT INTO candidatos (nome, idade, cpf, obje, cep, endereco, cidade, numero_casa, telefone1, telefone2, email, formacao, nome_curso, instituicao, duracao, conclusao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Verificar se a preparação da instrução falhou
    if ($stmt === false) {
        echo "Erro na preparação da instrução SQL: " . $conn->error;
        exit;
    }

    // Inicializar variáveis para os cursos
    $nome_curso = $instituicao = $duracao = $conclusao = '';

    // Verificar se há cursos e pegar o primeiro curso, se houver
    if (!empty($cursos)) {
        list($nome_curso, $instituicao, $duracao, $conclusao) = explode(',', $cursos[0]);
    }

    // Vincular parâmetros e executar a instrução para o currículo principal
    $stmt->bind_param("sisssssissssssss", $nome, $idade, $cpf, $obje, $cep, $endereco, $cidade, $numero, $telefone1, $telefone2, $email, $formacao, $nome_curso, $instituicao, $duracao, $conclusao);

    // Executar a instrução para o currículo principal
    if ($stmt->execute()) {
        // Obter o ID do currículo inserido
        $curriculo_id = $stmt->insert_id;

        // Inserir cursos adicionais, se houver mais de um
        if (count($cursos) > 1) {
            $stmt_cursos = $conn->prepare("INSERT INTO candidatos (nome, idade, cpf, obje, cep, endereco, cidade, numero_casa, telefone1, telefone2, email, formacao, nome_curso, instituicao, duracao, conclusao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach (array_slice($cursos, 1) as $curso) {
                list($nome_curso, $instituicao, $duracao, $conclusao) = explode(',', $curso);
                $stmt_cursos->bind_param("sisssssissssssss", $nome, $idade, $cpf, $obje, $cep, $endereco, $cidade, $numero, $telefone1, $telefone2, $email, $formacao, $nome_curso, $instituicao, $duracao, $conclusao);
                $stmt_cursos->execute();
            }
            $stmt_cursos->close();
        }

        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados no currículo principal: " . $stmt->error;
    }

    // Fechar a conexão e liberar recursos
    $stmt->close();
    $conn->close();

} else {
    echo "Formulário não foi enviado corretamente.";
}
?>
