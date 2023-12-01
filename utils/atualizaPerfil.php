<?php
session_start();
include "conexao.php";
include "queriesSql.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoNome = $_POST["novoNome"];
    $novoEmail = $_POST["novoEmail"];
    $novoNivelTreino = $_POST["novoNivelTreino"];
    $novaEscolhaTreino = $_POST["novaEscolhaTreino"];
    $novoSexo = $_POST["novoSexo"];
    $novaDataNasc = $_POST["novaDataNasc"];
    $id_usuario = $_SESSION["id_usuario"];

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, QUERY_UPDATE_USER);

        mysqli_stmt_bind_param($stmt, "ssssssi", $novoNome, $novoEmail, $novoNivelTreino, $novaEscolhaTreino, $novaDataNasc, $novoSexo, $id_usuario);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
    } catch (Exception $e) {
        echo $e->getMessage();
        mysqli_rollback($conn);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Atualiza Perfil</h1>
</body>
</html>