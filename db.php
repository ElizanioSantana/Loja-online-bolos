<?php
$host = 'localhost';
$dbname = 'loja_bolos';
$user = 'root';
$pass = 'senha do banco de dados';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET NAMES 'utf8mb4'");

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
