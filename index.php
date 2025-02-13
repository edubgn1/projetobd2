<?php
// Arquivo para login
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: vendas.php"); // Redireciona para vendas.php caso já tenha feito login
    exit();
}

$erro = isset($_GET['erro']) ? $_GET['erro'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/login.css"> <!-- Certifique-se de que o arquivo CSS esteja no mesmo diretório -->
</head>
<body>
    <!-- Container de login -->
    <div class="login-container">
        <h2>Login</h2>

        <!-- Exibe mensagem de erro, caso haja -->
        <?php if ($erro) { echo "<p class='error-message'>Erro ao autenticar. Tente novamente.</p>"; } ?>
        
        <!-- Formulário de login -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="usuario">Usuário:</label>
                <input type="text" name="usuario" id="usuario" required><br><br>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required><br><br>
            </div>
            <button type="submit">Entrar</button> <!-- Botão para login -->
        </form>

        <!-- Botão para redirecionar ao Dashboard (caso não esteja logado) -->
        
    </div>
</body>
</html>
