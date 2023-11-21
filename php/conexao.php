
<?php
// Dados do banco de dados e do servidor
$host = "localhost";
$username = "root";
$password = "";
$database = "4401063_consuladofitness";

$conn = mysqli_connect($host, $username, $password, $database);

// Verificação se houve falha na conexão
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

?>