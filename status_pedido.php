<?php
session_start();


if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}


$usuario_id = $_SESSION['usuario']['id'];  


require 'db.php';


$stmt = $pdo->prepare("
    SELECT p.id, p.total, p.metodo_pagamento, p.status, p.data_pedido
    FROM pedidos p
    WHERE p.usuario_id = :usuario_id
    ORDER BY p.data_pedido DESC
");
$stmt->execute([':usuario_id' => $usuario_id]);


$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="stylesstatus.css">
</head>
<body>
    <div class="container">
        <h1>Meus Pedidos</h1>
        
        <?php if (count($pedidos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Número do Pedido</th>
                        <th>Total</th>
                        <th>Método de Pagamento</th>
                        <th>Status</th>
                        <th>Data do Pedido</th>
                       
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                            <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($pedido['metodo_pagamento'])); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($pedido['status'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
 <p><a href="index.php" class="botao">Voltar para a Loja</a></p>
        <?php else: ?>
            <p>Você ainda não fez nenhum pedido.</p>
        <?php endif; ?>
    </div>
</body>
</html>
