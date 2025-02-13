<?php
session_start();

// Verifica se a sessão foi iniciada corretamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['senha'])) {
    die("Sessão não iniciada corretamente. Redirecionando para login...");
}

require 'conexao.php';

// Obter o ID do produto da URL
if (isset($_GET['id_produto'])) {
    $id_produto = $_GET['id_produto'];
} else {
    die("Produto não especificado.");
}

// Buscar detalhes do produto
$pdo = conectarBanco($_SESSION['usuario'], $_SESSION['senha']);
$query = "SELECT * FROM produtos WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_produto]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado.");
}

// Processar a venda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantidade = $_POST['quantidade'];

    if ($quantidade <= 0 || $quantidade > $produto['estoque']) {
        echo "Quantidade inválida!";
    } else {
        // Inicia a transação
        try {
            $pdo->beginTransaction();

            // Subtrair do estoque
            $query = "UPDATE produtos SET estoque = estoque - ? WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$quantidade, $id_produto]);

            // Registrar a venda
            $query = "INSERT INTO vendas (id_produto, quantidade, total, usuario_nome, data_venda) 
                      VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
            $total = $produto['preco'] * $quantidade; // Calcular o total da venda
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id_produto, $quantidade, $total, $_SESSION['usuario']]);

            // Commit a transação
            $pdo->commit();
            echo "Venda realizada com sucesso!";
        } catch (Exception $e) {
            // Rollback em caso de erro
            $pdo->rollBack();
            echo "Erro ao processar a venda: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fazer Venda</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h2>Fazer Venda - <?php echo $produto['nome']; ?></h2>

    <form action="fazer_venda.php?id_produto=<?php echo $produto['id']; ?>" method="POST">
        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" min="1" max="<?php echo $produto['estoque']; ?>" required>
        <button type="submit">Vender</button>
    </form>

    <!-- Botão de Voltar para o Dashboard -->
    <br>
    <a href="dashboard.php">
        <button>Voltar para o Dashboard</button>
    </a>
</body>
</html>
