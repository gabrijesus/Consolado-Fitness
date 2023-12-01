<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Redirecionar para a página de login ou fazer qualquer outra ação necessária
    header("Location: login.php");
    exit();
}

include "../utils/conexao.php";
include "../utils/queriesSql.php";

$id_usuario = $_SESSION["id_usuario"];

$infoUsuario = $conn->query("SELECT preferencia_treino FROM usuarios WHERE id_usuario = $id_usuario");

$usuarioInfo = $infoUsuario->fetch_assoc();
$preferenciaUsuario = $usuarioInfo['preferencia_treino'];

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/confirmado.css">
    <link rel="shortcut icon" href="../imagens/favicon.ico" type="image/x-icon">
    <title>Cadastro | Consulado Fitness</title>
</head>

<body>
    <?php
    include_once "../utils/cabecalho.php";
    ?>

    <main class="area_total">
        <div class="texto">

            <h1>Parabens!!</h1>

            <p>Você deu o primeiro passo para a sua transformação física.</p>
            <p>Agora que você escolheu treinar <?php echo $preferenciaUsuario;?>. Clique abaixo para sabermos sua aptidão fisíca e assim mudar seu treino</p>

            <div class="link_perfil">
                <a href="geraTreino.php">Bora montar seu treino!!</a>
            </div>
        </div>
    </main>
</body>

</html>