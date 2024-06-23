<?php

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Configurações do banco de dados (substitua pelos seus dados)
    $servername = "localhost";
    $username = "seu_usuario";
    $password = "sua_senha";
    $dbname = "RH";

    // Conecta ao banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Prepara os dados recebidos do formulário
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $cpf = $_POST['cpf'];
    $obje = $_POST['obje'];
    $endereco = $_POST['endereco'];
    $cidade = $_POST['cidade'];
    $numero = $_POST['numero'];
    $telefone1 = $_POST['telefone1'];
    $telefone2 = $_POST['telefone2'];
    $email = $_POST['email'];
    $formacao = $_POST['formacao'];

    // Insere os dados principais na tabela 'curriculos'
    $sql = "INSERT INTO curriculos (nome, idade, cpf, objetivo, endereco, cidade, numero_casa, telefone1, telefone2, email, formacao)
            VALUES ('$nome', $idade, '$cpf', '$obje', '$endereco', '$cidade', $numero, '$telefone1', '$telefone2', '$email', '$formacao')";

    if ($conn->query($sql) === TRUE) {
        $curriculo_id = $conn->insert_id; // Obtém o ID do currículo inserido
        // Verifica se há cursos adicionados
        if (isset($_POST['cursos'])) {
            // Prepara para inserir os cursos na tabela 'cursos'
            $cursos = $_POST['cursos'];
            foreach ($cursos as $curso) {
                $curso_data = explode(',', $curso);
                $nome_curso = $curso_data[0];
                $instituicao = $curso_data[1];
                $duracao = $curso_data[2];
                $conclusao = $curso_data[3];
                // Insere na tabela 'cursos'
                $sql_cursos = "INSERT INTO cursos (curriculo_id, nome_curso, instituicao, duracao, conclusao)
                               VALUES ($curriculo_id, '$nome_curso', '$instituicao', '$duracao', '$conclusao')";
                $conn->query($sql_cursos);
            }
        }
        echo "Currículo enviado com sucesso!";
    } else {
        echo "Erro ao enviar o currículo: " . $conn->error;
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
} else {
    // Se o formulário não foi enviado, redireciona para a página do formulário
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Formulário de Envio de Currículos</title>
    
</head>

<body>
    <div class="container">
        <h2>Formulário de Envio de Currículos</h2>
        <form action="processa_formulario.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="idade">Idade:</label>
            <input type="number" id="idade" name="idade" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required>

            <label for="obje">Objetivo Profissional:</label>
            <input type="text" id="obje" name="obje" required>

            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" required onblur="handleCepChange(event)">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" required>

            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" required>

            <label for="numero">Número da casa:</label>
            <input type="number" id="numero" name="numero" required>

            <label for="telefone1">Telefone de Contato:</label>
            <input type="text" id="telefone1" name="telefone1" required>

            <label for="telefone2">Telefone 2 de Contato:</label>
            <input type="text" id="telefone2" name="telefone2">

            <label for="email">Email de Contato:</label>
            <input type="email" id="email" name="email" required>

            <label for="formacao">Formação Acadêmica:</label>
            <select id="formacao" name="formacao" required>
                <option value="">Selecione uma opção</option>
                <option value="cursando_ensino_medio">Cursando Ensino Médio</option>
                <option value="ensino_medio_completo">Ensino Médio Completo</option>
                <option value="cursando_ensino_superior">Cursando Ensino Superior</option>
                <option value="ensino_superior_completo">Ensino Superior Completo</option>
                <option value="pos_graduacao_completa">Pós-graduação Completa</option>
            </select>

            <div id="cursos">
                <!-- Aqui serão adicionados os cursos dinamicamente -->
            </div>

            <button type="button" onclick="openModal()">Adicionar Curso</button>

            <div id="modal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>Adicionar Curso</h2>
                    <div class="curso-inputs">
                        <label for="nome_curso">Nome do Curso:</label>
                        <input type="text" id="nome_curso" required>

                        <label for="instituicao">Nome da Instituição:</label>
                        <input type="text" id="instituicao" required>

                        <label for="duracao">Duração:</label>
                        <input type="text" id="duracao" required>

                        <label for="conclusao">Mês e Ano de Conclusão:</label>
                        <input type="text" id="conclusao" required>
                    </div>
                    <button type="button" onclick="addCurso()">Salvar Curso</button>
                    <button type="button" onclick="addMaisUm()">+ Adicionar Mais um Curso</button>
                </div>
            </div>

            <input type="submit" value="Enviar">
        </form>
    </div>

    <script>
        async function buscarCEP(cep) {
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                if (data.erro) {
                    alert("CEP não encontrado.");
                    return;
                }
                document.getElementById('endereco').value = data.logradouro;
                document.getElementById('cidade').value = data.localidade;
            } catch (error) {
                alert("Erro ao buscar o CEP.");
            }
        }

        function handleCepChange(event) {
            const cep = event.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCEP(cep);
            }
        }

        // Funções para abrir e fechar o modal
        function openModal() {
            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Função para adicionar curso
        function addCurso() {
            const nomeCurso = document.getElementById('nome_curso').value;
            const instituicao = document.getElementById('instituicao').value;
            const duracao = document.getElementById('duracao').value;
            const conclusao = document.getElementById('conclusao').value;

            if (nomeCurso && instituicao && duracao && conclusao) {
                const cursosContainer = document.getElementById('cursos');
                const cursoDiv = document.createElement('div');
                cursoDiv.className = 'curso';
                cursoDiv.innerHTML = `
                    <input type="hidden" name="cursos[]" value="${nomeCurso},${instituicao},${duracao},${conclusao}">
                    <p><strong>Curso:</strong> ${nomeCurso}</p>
                    <p><strong>Instituição:</strong> ${instituicao}</p>
                    <p><strong>Duração:</strong> ${duracao}</p>
                    <p><strong>Conclusão:</strong> ${conclusao}</p>
                `;
                cursosContainer.appendChild(cursoDiv);
                closeModal();
                // Limpar campos do modal após adicionar curso
                document.getElementById('nome_curso').value = '';
                document.getElementById('instituicao').value = '';
                document.getElementById('duracao').value = '';
                document.getElementById('conclusao').value = '';
            } else {
                alert("Por favor, preencha todos os campos do curso.");
            }
        }

        // Função para adicionar mais um campo de curso dentro do modal
        function addMaisUm() {
            const cursoInputs = document.createElement('div');
            cursoInputs.className = 'curso-inputs';
            cursoInputs.innerHTML = `
                <label for="nome_curso">Nome do Curso:</label>
                <input type="text" id="nome_curso" required>

                <label for="instituicao">Nome da Instituição:</label>
                <input type="text" id="instituicao" required>

                <label for="duracao">Duração:</label>
                <input type="text" id="duracao" required>

                <label for="conclusao">Mês e Ano de Conclusão:</label>
                <input type="text" id="conclusao" required>
            `;
            document.querySelector('.modal-content').insertBefore(cursoInputs, document.querySelector('.modal-content').lastElementChild);
        }
    </script>
</body>

</html>