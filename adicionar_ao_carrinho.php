<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado']);
    exit;
}

$produto_id = $_POST['produto_id'];
$usuario_id = $_SESSION['usuario']['id'];

$stmt = $pdo->prepare("INSERT INTO carrinho (produto_id, usuario_id, quantidade) VALUES (?, ?, 1) 
                      ON DUPLICATE KEY UPDATE quantidade = quantidade + 1");
$stmt->execute([$produto_id, $usuario_id]);

echo json_encode(['sucesso' => true]);
exit;
