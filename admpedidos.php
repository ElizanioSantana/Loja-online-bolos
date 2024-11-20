<?php
session_start();


if (!isset($_SESSION['administrador'])) {
    header('Location: admlogin.php');
    exit;
}


require 'db.php';


$stmt = $pdo->prepare("
    SELECT 
        p.id AS pedido_id, 
        p.total, 
        p.metodo_pagamento, 
        p.status, 
        p.data_pedido, 
        u.nome AS usuario_nome,
        u.telefone, 
               u.endereco, 
        u.cidade, 
        u.estado, 
        u.cep, 
        u.nomecompleto, 
        u.numcasa, 
        u.complemento
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.data_pedido DESC
");
$stmt->execute();


$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar_status'])) {
    $pedido_id = $_POST['pedido_id']; 
    $novo_status = $_POST['novo_status'];

    
    $status_permitidos = ['pendente', 'processando', 'enviado', 'entregue', 'cancelado'];
    if (in_array($novo_status, $status_permitidos)) {
        
        $stmt = $pdo->prepare("UPDATE pedidos SET status = :status WHERE id = :pedido_id");
        $stmt->execute([':status' => $novo_status, ':pedido_id' => $pedido_id]);
        
        
        header('Location: admpedidos.php');
        exit;
    } else {
        echo "<p>Status inválido.</p>";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletar_pedido'])) {
    $pedido_id = $_POST['pedido_id'];

   
    $stmt = $pdo->prepare("DELETE FROM detalhes_pedido WHERE pedido_id = :pedido_id");
    $stmt->execute([':pedido_id' => $pedido_id]);

    
    $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = :pedido_id");
    $stmt->execute([':pedido_id' => $pedido_id]);

    
    header('Location: admpedidos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Administração de Pedidos</title>
    <link rel="stylesheet" href="stylesadminpedidos.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciar Pedidos</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Número do Pedido</th>
                    <th>Usuário</th>
                    <th>Total</th>
                    <th>Método de Pagamento</th>
                    <th>Status</th>
                    <th>Data do Pedido</th>
                    <th>Detalhes do Pedido</th>
                    <th>Dados do Cliente</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['pedido_id']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['usuario_nome']); ?></td>
                        <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($pedido['metodo_pagamento'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($pedido['status'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                        <td>
                            
                            <?php
                            $stmt_detalhes = $pdo->prepare("
                                SELECT dp.quantidade, dp.preco_unitario, pr.nome AS produto_nome
                                FROM detalhes_pedido dp
                                JOIN produtos pr ON dp.produto_id = pr.id
                                WHERE dp.pedido_id = :pedido_id
                            ");
                            $stmt_detalhes->execute([':pedido_id' => $pedido['pedido_id']]);
                            $detalhes = $stmt_detalhes->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <ul>
                                <?php foreach ($detalhes as $detalhe): ?>
                                    <li>
                                        <?php echo htmlspecialchars($detalhe['produto_nome']); ?> - 
                                        Quantidade: <?php echo htmlspecialchars($detalhe['quantidade']); ?>, 
                                        Preço Unitário: R$ <?php echo number_format($detalhe['preco_unitario'], 2, ',', '.'); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <strong>Nome Completo:<br></strong> <?php echo htmlspecialchars($pedido['nomecompleto']); ?><br>
                            <strong>Telefone:<br></strong> <?php echo htmlspecialchars($pedido['telefone']); ?><br>
                            
                            <strong>Endereço:<br></strong> <?php echo htmlspecialchars($pedido['endereco']); ?>, Nº <?php echo htmlspecialchars($pedido['numcasa']); ?><br>
                            <strong>Complemento:<br></strong> <?php echo htmlspecialchars($pedido['complemento']); ?><br>
                            <strong>Cidade:<br></strong> <?php echo htmlspecialchars($pedido['cidade']); ?> - <?php echo htmlspecialchars($pedido['estado']); ?><br>
                            <strong>CEP:<br></strong> <?php echo htmlspecialchars($pedido['cep']); ?>
                        </td>
                        <td>
                            
                            <form method="POST" action="">
                                <input type="hidden" name="pedido_id" value="<?php echo $pedido['pedido_id']; ?>">
                                <select name="novo_status">
                                    <option value="pendente" <?php echo ($pedido['status'] == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="processando" <?php echo ($pedido['status'] == 'processando') ? 'selected' : ''; ?>>Processando</option>
                                    <option value="enviado" <?php echo ($pedido['status'] == 'enviado') ? 'selected' : ''; ?>>Enviado</option>
                                    <option value="entregue" <?php echo ($pedido['status'] == 'entregue') ? 'selected' : ''; ?>>Entregue</option>
                                    <option value="cancelado" <?php echo ($pedido['status'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                                <button type="submit" name="alterar_status">Alterar Status</button>
                            </form>
                            
                            <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir este pedido?');">
                                <input type="hidden" name="pedido_id" value="<?php echo $pedido['pedido_id']; ?>">
                                <button type="submit" name="deletar_pedido">Deletar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
 <div class="voltar-container">
            <a href="admindex.php" class="voltar-btn">Voltar à Página Inicial</a>
        </div>
    </div>
</body>
</html>
