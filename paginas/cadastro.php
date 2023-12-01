<?php
session_start();
include "../utils/conexao.php";
include "../utils/queriesSql.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recuperar dados do formulário
    $nomeUsuario = $_POST['nome'];
    $emailUsuario = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $nivelTreinoUsuario = $_POST['nivel_treino'];
    $preferenciaTreinoUsuario = $_POST['escolha_treino'];
    $dataNascimentoUsuario = $_POST['date'];
    $sexoUsuario = $_POST['sexo'];
    $senhaUsuario = password_hash($_POST['senha'], PASSWORD_DEFAULT);


    mysqli_begin_transaction($conn);
    try {
        $emailUsuario = mysqli_real_escape_string($conn, $emailUsuario);
        $stmt = mysqli_prepare($conn, QUERY_INSERT_USER);
        mysqli_stmt_bind_param($stmt, "sssssss", $nomeUsuario, $emailUsuario, $senhaUsuario, $nivelTreinoUsuario, $preferenciaTreinoUsuario, $dataNascimentoUsuario, $sexoUsuario);
        mysqli_stmt_execute($stmt);

        $result = $conn->query("SELECT id_usuario FROM usuarios WHERE email = '$emailUsuario'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['id_usuario'] = $row['id_usuario'];
        }

        mysqli_stmt_close($stmt);
        header("Location: ../php/geraTreino.php");
    } catch (Exception $e) {
        echo $e->getMessage();
        mysqli_rollback($conn);
    }
    mysqli_commit($conn);

}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
    <title>Cadastro | Consulado Fitness</title>
</head>

<body>

    <div class="area_cadastro">
        <form action="" method="post">

            <h1>Crie sua conta</h1>

            <div class="info_pessoal">
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="nome" required>
            </div>

            <div class="info_pessoal">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" class="input" required>
            </div>

            <div class="info_pessoal escolha">

                <span>
                    <label for="date">Data de nascimento</label>
                    <input type="date" name="date" id="date" class="date" required>
                </span>

                <span>

                    <label for="sexo">Sexo</label>

                    <select name="sexo" id="sexo" required>
                        <option value="homem">Masculino</option>
                        <option value="mulher">Feminino</option>
                    </select>

                </span>

                <span>

                    <label for="nivel_treino">Nível de treino</label>

                    <select name="nivel_treino" id="nivel_treino" required>
                        <option value="iniciante">Iniciante</option>
                        <option value="intermediario">Intermediario</option>
                        <option value="avancado">Avançado</option>
                    </select>

                </span>
                <span>

                    <label for="escolha_treino">Preferência de Treino</label>

                    <select name="escolha_treino" id="escolha_treino" required>
                        <option value="musculação">Academia</option>
                        <option value="exercicios-em-casa">Em casa</option>
                    </select>

                </span>

            </div>

            <div class="info_pessoal">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" style="color:black;" required>
            </div>

            <button type="submit" class="botao_cadastro">Cadastrar</button>

            <a href="login.php">Já possui conta? Faça login</a>
            
        </form>

        <img src="../imagens/cadastro-imagem.png" alt="cadastro" class="img_cadastro">
    </div>
</body>

</html>