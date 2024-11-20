<?php
session_start();


if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}


if (!isset($_SESSION['numero_pedido'])) {
    header('Location: pagamento.php');
    exit;
}


$numero_pedido = $_SESSION['numero_pedido'];


require 'db.php';
$stmt = $pdo->prepare("
    SELECT p.id AS pedido_id, p.total, p.metodo_pagamento, p.data_pedido 
    FROM pedidos p 
    WHERE p.id = :numero_pedido
");
$stmt->execute([':numero_pedido' => $numero_pedido]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido não encontrado.");
}


unset($_SESSION['numero_pedido']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Pedido</title>
    <link rel="stylesheet" href="stylesconfirmacao.css">
</head>
<body>
    <div class="container">
        <h1>Pedido Confirmado!</h1>
        <p>Obrigado pela sua compra! Seu pedido foi processado com sucesso.</p>

        
        <div class="detalhes-pedido">
            <h2>Detalhes do Pedido</h2>
            <p><strong>Número do Pedido:</strong> <?php echo htmlspecialchars($pedido['pedido_id']); ?></p>
            <p><strong>Total:</strong> R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></p>
            <p><strong>Método de Pagamento:</strong> <?php echo ucfirst(htmlspecialchars($pedido['metodo_pagamento'])); ?></p>
            <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
        </div>

        <a href="index.php" class="botao">Voltar para a Loja</a>
    </div>
</body>
</html>
