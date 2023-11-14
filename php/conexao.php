
<?php
// Dados do banco de dados e do servidor
$host = "fdb1032.awardspace.net";
$username = "4401063_consuladofitness";
$password = "Consul@do15";
$database = "consuladofitness";

try {
    // Criando uma nova conexão usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);

    // Definindo o modo de erro do PDO para exceção
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Exibindo mensagem de erro em caso de falha
    die("Conexão falhou: " . $e->getMessage());
}
?>