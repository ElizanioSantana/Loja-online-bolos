<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}


if (isset($_SESSION['total_carrinho'])) {
    $total_carrinho = $_SESSION['total_carrinho'];
} else {
   
    header('Location: carrinho.php');
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['metodo_pagamento'])) {
        $metodo_pagamento = $_POST['metodo_pagamento'];

        try {
            $pdo->beginTransaction();

           
            $stmt = $pdo->prepare("
                INSERT INTO pedidos (usuario_id, total, metodo_pagamento, data_pedido)
                VALUES (:usuario_id, :total, :metodo_pagamento, NOW())
            ");
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':total' => $total_carrinho,
                ':metodo_pagamento' => $metodo_pagamento
            ]);

            
            $pedido_id = $pdo->lastInsertId();

           
            $stmt = $pdo->prepare("
                INSERT INTO detalhes_pedido (pedido_id, produto_id, quantidade, preco_unitario)
                SELECT :pedido_id, produto_id, quantidade, preco
                FROM carrinho
                JOIN produtos ON carrinho.produto_id = produtos.id
                WHERE carrinho.usuario_id = :usuario_id
            ");
            $stmt->execute([
                ':pedido_id' => $pedido_id,
                ':usuario_id' => $usuario_id
            ]);

            
            $stmt = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = :usuario_id");
            $stmt->execute([':usuario_id' => $usuario_id]);

            $pdo->commit();

            
            $_SESSION['numero_pedido'] = $pedido_id;
            header("Location: confirmacao.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            die("Erro ao processar o pedido: " . $e->getMessage());
        }
    } else {
        $erro = "Selecione um método de pagamento.";
    }
}
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pagamento</title>
    <link rel="stylesheet" href="stylespagamento.css">
</head>
<body>
    <div class="container">
        <h2>Escolha seu método de pagamento</h2>

        <?php if (!empty($erro)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>

        <form method="POST">
            
            <div class="metodo-pagamento">
                <label>
                    <input type="radio" name="metodo_pagamento" value="cartao" required onclick="mostrarOpcao('cartao')">
                    Cartão de Crédito
                </label>
                <label>
                    <input type="radio" name="metodo_pagamento" value="pix" required onclick="mostrarOpcao('pix')">
                    PIX
                </label>
                <label>
                    <input type="radio" name="metodo_pagamento" value="avista" required onclick="mostrarOpcao('avista')">
                    À Vista na Entrega
                </label>
            </div>

            
            <div id="opcao-cartao" class="opcao-pagamento" style="display: none;">
                <label for="nome_cartao">Nome no Cartão:</label>
                <input type="text" id="nome_cartao" name="nome_cartao">

                <label for="numero_cartao">Número do Cartão:</label>
                <input type="text" id="numero_cartao" name="numero_cartao">

                <label for="validade">Validade (MM/AA):</label>
                <input type="text" id="validade" name="validade">

                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv">
            </div>

            
            <div id="opcao-pix" class="opcao-pagamento" style="display: none;">
                <p>Escaneie o QR Code abaixo para pagar com PIX:</p>
                <img src="pix-qrcode.png" alt="QR Code para pagamento via PIX" class="pix-qrcode">
            </div>

           
            <div id="opcao-avista" class="opcao-pagamento" style="display: none;">
                <p>O pagamento será feito à vista no momento da entrega.</p>
            </div>

            
            <p class="total">Total: R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></p>

            
            <button type="submit">Confirmar Pedido</button>
        </form>
    </div>

    <script>
        function mostrarOpcao(opcao) {
            
            document.querySelectorAll('.opcao-pagamento').forEach(div => {
                div.style.display = 'none';
            });

            
            const divSelecionada = document.getElementById(`opcao-${opcao}`);
            if (divSelecionada) {
                divSelecionada.style.display = 'block';
            }
        }
    </script>
</body>
</html>
