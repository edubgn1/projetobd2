<?php
session_start();

// Verificar se a sessão foi iniciada corretamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['senha'])) {
    die("Sessão não iniciada corretamente. Redirecionando para login...");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            text-align: center;
            color: #333;
        }
        p {
            text-align: center;
            color: #666;
        }
        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }
        li {
            display: inline-block;
            margin: 10px 20px;
        }
        li a {
            display: block;
            padding: 15px 30px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        li a:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #dc3545;
            color: #fff;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <div class="container">
        <h3>Bem-vindo, <?php echo $_SESSION['usuario']; ?>!</h3>
        <p>Escolha uma das opções abaixo:</p>
        <ul>
            <li><a href="vendas.php">Fazer Venda</a></li>
            <li><a href="adicionar_produto.php">Manutenção estoque</a></li>
            <li><a href="logout.php" class="logout-btn">Sair</a></li>
        </ul>
    </div>

</body>
</html>
