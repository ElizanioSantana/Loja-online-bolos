<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];


$stmt = $pdo->prepare("
    SELECT c.id AS carrinho_id, p.id AS produto_id, p.nome, p.preco, c.quantidade 
    FROM carrinho c
    JOIN produtos p ON c.produto_id = p.id
    WHERE c.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total = 0;
foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}


$_SESSION['total_carrinho'] = $total;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $carrinho_id = $_POST['carrinho_id'];
    $stmt = $pdo->prepare("DELETE FROM carrinho WHERE id = ?");
    $stmt->execute([$carrinho_id]);
    header("Location: carrinho.php"); 
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    
    
    
    header("Location: endereco.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="stylescar.css">
</head>
<body>
    <header>
        <h1>Seu Carrinho</h1>
        <a href="index.php">Voltar ao Site</a>
        <a href="logout.php">Sair</a>
    </header>

    <main>
        <?php if (count($carrinho) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrinho as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nome']); ?></td>
                            <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="carrinho_id" value="<?php echo $item['carrinho_id']; ?>">
                                    <button type="submit" name="excluir">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p><strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></p>

            <form method="POST">
                <button type="submit" name="finalizar">Finalizar Compra</button>
            </form>
        <?php else: ?>
            <p>Seu carrinho está vazio. <a href="index.php">Voltar para a loja</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
