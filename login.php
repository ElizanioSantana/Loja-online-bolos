<?php
session_start();
require 'db.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = ? AND senha = ?");
    $stmt->execute([$nome, $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
        $mensagem = "Login realizado com sucesso! Redirecionando para a página inicial...";
        echo "<meta http-equiv='refresh' content='1;url=index.php'>"; 
    } else {
        $erro = "Nome ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Loja de Bolos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Entrar</h2>
        
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

            <button type="submit">Entrar</button>
        </form>
        <p class="link-cadastro">Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
</body>
</html>
