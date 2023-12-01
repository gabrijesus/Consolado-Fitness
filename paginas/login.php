<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    header("Location: geraTreino.php");
    exit();
}

include "../utils/conexao.php";
include "../utils/queriesSql.php";


$erro = '';

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha as credenciais do formulário
    $emailDigitado = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $senhaDigitada = $_POST['senha'];

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, "SELECT id_usuario, email, senha FROM usuarios WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $emailDigitado);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows > 0) {
            // Usuario encontrado
            $row = $result->fetch_assoc();
            $senhaHash = $row['senha'];
            $idUsuario = $row['id_usuario'];
            $senhaSemAspas = str_replace('"', '', $senhaHash);

            // Verifica se a senha fornecida corresponde ao hash no banco de dados
            if (password_verify($senhaDigitada, $senhaSemAspas)) {
                // Credenciais válidas, redirecione para a página principal
                $_SESSION['id_usuario'] = $idUsuario;
                header("Location: ../php/geraTreino.php");
            } else {
                // Senha inválida, exiba uma mensagem de erro
                $erro = "Credenciais inválidas. Tente novamente.";
            }
        } else {
            // Usuario não encontrado, exiba uma mensagem de erro
            $erro = "Usuário Não Encontrado. Tente novamente.";
        }

        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        echo $e->getMessage();
        mysqli_rollback($conn);
    }
    mysqli_commit($conn);
}

// Feche a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    <title>Login | Consulado Fitness</title>
</head>

<body>
    <div>
        <img src="../imagens/login.png" alt="login">
        <form action="" method="POST">
            <?php if ($erro != '') : ?>
                <p style="color: red;"><?php echo $erro; ?></p>
            <?php endif; ?>

            <div class="login">
                <label for="">Email</label>
                <input type="email" name="email" id="email">
            </div>

            <div class="login">
                <label for="">Senha</label>
                <input type="password" name="senha" id="senha">
            </div>

            <div class="login">
                <button type="submit">Entrar</button>
            </div>
        </form>
    </div>
</body>

</html>