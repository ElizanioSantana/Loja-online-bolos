<?php
session_start();
require 'db.php'; 


if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$usuario_id = $_SESSION['usuario']['id'];


$nome = $senha = $nomecompleto = $telefone = $endereco = $numcasa = $complemento = $cidade = $estado = $cep = '';

try {
    
    $stmt = $pdo->prepare("SELECT nome, senha, nomecompleto, telefone, endereco, numcasa, complemento, cidade, estado, cep FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados) {
        $nome = $dados['nome'];
        $senha = $dados['senha'];
        $nomecompleto = $dados['nomecompleto'];
        $telefone = $dados['telefone'];
        $endereco = $dados['endereco'];
        $numcasa = $dados['numcasa'];
        $complemento = $dados['complemento'];
        $cidade = $dados['cidade'];
        $estado = $dados['estado'];
        $cep = $dados['cep'];
    }

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome = $_POST['nome'];
        $senha = $_POST['senha'];
        $nomecompleto = $_POST['nomecompleto'];
        $telefone = $_POST['telefone'];
        $endereco = $_POST['endereco'];
        $complemento = $_POST['complemento'];    
        $numcasa = $_POST['numcasa'];        
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];
        $cep = $_POST['cep'];

       
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nome = :nome, senha = :senha, nomecompleto = :nomecompleto, telefone = :telefone, endereco = :endereco, numcasa = :numcasa, complemento = :complemento, cidade = :cidade, estado = :estado, cep = :cep 
            WHERE id = :id
        ");
        $stmt->execute([
            'nome' => $nome,
            'senha' => $senha,
            'nomecompleto' => $nomecompleto,
            'telefone' => $telefone,
            'endereco' => $endereco,
            'numcasa' => $numcasa,
            'complemento' => $complemento,            
            'cidade' => $cidade,
            'estado' => $estado,
            'cep' => $cep,
            'id' => $usuario_id,
        ]);

        $mensagem = "Informações salvas com sucesso!";
    }
} catch (PDOException $e) {
    die("Erro ao acessar o banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Dados</title>
    <link rel="stylesheet" href="stylesdados.css">
</head>
<body>
    <div class="container">
        <h2>Meus Dados</h2>

        <?php if (!empty($mensagem)): ?>
            <p class="mensagem"><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome"> Alterar usúario:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>

            <label for="nome">Senha:</label>
            <input type="password" id="senha" name="senha" required="">

            <label for="nomecompleto">Nome completo:</label>
            <input type="text" id="nomecompleto" name="nomecompleto" value="<?php echo htmlspecialchars($nomecompleto); ?>" required>

            <label for="telefone">Telefone e DDD:</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($endereco); ?>" required>
            
           <label for="numcasa">Número da casa:</label>
           <input type="text" id="numcasa" name="numcasa" value="<?php echo htmlspecialchars($numcasa); ?>" required>

            <label for="complemento">Complemento/ Ponto de referência:</label>
            <input type="text" id="complemento" name="complemento" value="<?php echo htmlspecialchars($complemento); ?>" required>

            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($cidade); ?>" required>

            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($estado); ?>" required>

            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($cep); ?>" required>

            <button type="submit">Salvar</button>
        </form>

        <a href="index.php">Voltar</a>
    </div>
</body>
</html>
