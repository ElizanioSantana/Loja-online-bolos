<?php
session_start();
require 'db.php';

$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Loja de Bolos</title>
    <link rel="stylesheet" href="stylesindex.css">
</head>
<body>
    <header>
        <h1>Pagina Inicial do Administrador</h1>
        <div class="user-info">
            <?php if (isset($_SESSION['administrador'])): ?>
                <span>Bem-vindo Administrador, <?php echo $_SESSION['administrador']['nome']; ?>!</span>
                <a href="admlogout.php">Sair</a>
            <?php else: ?>
                <a href="admlogin.php">Entrar</a>
            <?php endif; ?>
            <a href="admpedidos.php" class="admpedidos.php-link">Gerenciar Pedidos</a>
            <a href="admprodutos.php" class="admprodutos-link">Gerenciar Produtos</a>
            
        </div>
    </header>

    <main>
        <div class="produtos">
            <?php foreach ($produtos as $produto): ?>
                <div class="produto">
                    <?php if (!empty($produto['imagem'])): ?>
                        <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="produto-imagem">
                    <?php endif; ?>
                    <h2><?php echo $produto['nome']; ?></h2>
                    <p><?php echo $produto['descricao']; ?></p>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    
                    </form>
                </div>
            <?php endforeach; ?>
        
    </body>
</html>
