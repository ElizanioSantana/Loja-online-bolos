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
        <h1>Loja de Bolos</h1>
        <div class="user-info">
            <?php if (isset($_SESSION['usuario'])): ?>
                <span>Bem-vindo, <?php echo $_SESSION['usuario']['nome']; ?>!</span>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
            <?php endif; ?>
            <a href="meusdados.php" class="meusdados-link">Minha conta</a>
            <a href="status_pedido.php" class="status_pedido-link">Meus pedidos</a>
            <a href="carrinho.php" class="carrinho-link">Ver carrinho</a>
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
                    <form onsubmit="return adicionarAoCarrinho(event, <?php echo $produto['id']; ?>);">
                        <button type="submit">Adicionar ao Carrinho</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    
    <div id="confirmacao" class="confirmacao-popup" style="display: none;">
        <p>Produto adicionado ao carrinho com sucesso!</p>
        <button onclick="continuarComprando()">Continuar Comprando</button>
        <a href="carrinho.php" class="botao-carrinho">Ir para o Carrinho</a>
    </div>

    <script>
        function adicionarAoCarrinho(event, produtoId) {
            event.preventDefault();

            
            fetch('adicionar_ao_carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `produto_id=${produtoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarConfirmacao();
                } else {
                    window.location.href = 'login.php';
                }
            })
            .catch(error => console.error('Erro ao adicionar ao carrinho:', error));
        }

        function mostrarConfirmacao() {
            document.getElementById('confirmacao').style.display = 'block';
            
        }

        function continuarComprando() {
            document.getElementById('confirmacao').style.display = 'none';
        }
    </script>
</body>
</html>
