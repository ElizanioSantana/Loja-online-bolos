<?php
require 'db.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nome = ?");
    $stmt->execute([$nome]);
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        $erro = "O nome de usuário já existe. Por favor, escolha outro.";
    } else {
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, senha) VALUES (?, ?)");
        if ($stmt->execute([$nome, $senha])) {
            $mensagem = "Cadastro realizado com sucesso! Redirecionando para a página de login...";
            echo "<meta http-equiv='refresh' content='1;url=login.php'>"; 
        } else {
            $mensagem = "Erro ao criar conta. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Loja de Bolos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Cadastrar-se </h2>
        
        <?php if (!empty($mensagem)): ?>
            <p class="sucesso"><?php echo $mensagem; ?></p>
        <?php elseif (isset($erro)): ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label for="nome">Usúario</label>
            <input type="text" id="nome" name="nome" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Cadastrar</button>
        </form>
        <p class="link-login">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </div>
</body>
</html>
