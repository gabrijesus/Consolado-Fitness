<?php
session_start();

// Encerrar a sessão
session_unset();
session_destroy();

// Redirecionar para a página de login ou qualquer outra página desejada
header("Location: ../pages/login.php");
exit();
?>
