<?php
session_start();


if (!isset($_SESSION['administrador'])) {
    header('Location: admlogin.php');
    exit;
}


require 'db.php';

$mensagem = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_produto'])) {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];
    
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $diretorio = 'img/';
        $nome_arquivo = basename($_FILES['imagem']['name']);
        $caminho_arquivo = $diretorio . $nome_arquivo;

        
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $tipos_permitidos)) {
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_arquivo)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, descricao, imagem) VALUES (:nome, :preco, :descricao, :imagem)");
                    $stmt->execute([
                        ':nome' => $nome,
                        ':preco' => $preco,
                        ':descricao' => $descricao,
                        ':imagem' => $caminho_arquivo
                    ]);
                    $mensagem = "Produto adicionado com sucesso!";
                } catch (PDOException $e) {
                    $mensagem = "Erro ao adicionar o produto: " . $e->getMessage();
                }
            } else {
                $mensagem = "Erro ao fazer upload da imagem.";
            }
        } else {
            $mensagem = "Formato de imagem não suportado. Use JPG, JPEG, PNG ou GIF.";
        }
    } else {
        $mensagem = "Erro no upload da imagem.";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remover_produto'])) {
    $produto_id = $_POST['produto_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $produto_id]);
        $mensagem = "Produto removido com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro ao remover o produto: " . $e->getMessage();
    }
}


$stmt = $pdo->prepare("SELECT * FROM produtos");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Produtos</title>
    <link rel="stylesheet" href="stylesadmprodutos.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciamento de Produtos</h1>
        
        
        <?php if (!empty($mensagem)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        
        <h2>Adicionar Produto</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="preco">Preço:</label>
            <input type="number" step="0.01" id="preco" name="preco" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required></textarea>

            <label for="imagem">Imagem do Produto:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" required>

            <button type="submit" name="adicionar_produto">Adicionar Produto</button>
        </form>

       
        <h2>Produtos Existentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Imagem</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produto['id']); ?></td>
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($produto['descricao']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="Imagem do Produto" style="width: 50px; height: auto;"></td>
                        <td>
                            <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja remover este produto?');">
                                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                                <button type="submit" name="remover_produto">Remover</button>
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
