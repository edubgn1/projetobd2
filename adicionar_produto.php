<?php
session_start();

// Verifica se a sessão foi iniciada corretamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['senha'])) {
    die("Sessão não iniciada corretamente. Redirecionando para login...");
}

$mensagem = ""; // Variável para armazenar a mensagem
$tipo_mensagem = ""; // Classe CSS para diferenciar sucesso e erro

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $nome = trim($_POST['nome']);
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];

    // Conectar ao banco de dados
    require 'conexao.php';
    $pdo = conectarBanco($_SESSION['usuario'], $_SESSION['senha']);
    
    if ($pdo) {
        try {
            // Verificar se o produto já existe
            $query_check = "SELECT id, estoque, preco FROM produtos WHERE nome = ?";
            $stmt_check = $pdo->prepare($query_check);
            $stmt_check->execute([$nome]);
            $produto = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                // Produto encontrado, adiciona estoque e altera o preço (se fornecido)
                $novo_estoque = $produto['estoque'] + $estoque;
                $novo_preco = !empty($preco) ? $preco : $produto['preco']; // Atualiza preço somente se o valor for informado

                // Atualizar o estoque e preço do produto
                $query_update = "UPDATE produtos SET estoque = ?, preco = ? WHERE id = ?";
                $stmt_update = $pdo->prepare($query_update);
                $stmt_update->execute([$novo_estoque, $novo_preco, $produto['id']]);

                $mensagem = "Estoque e/ou preço do produto atualizado com sucesso!";
                $tipo_mensagem = "sucesso";
            } else {
                // Produto não existe, adiciona novo produto
                $query_insert = "INSERT INTO produtos (nome, preco, estoque) VALUES (?, ?, ?)";
                $stmt_insert = $pdo->prepare($query_insert);
                $stmt_insert->execute([$nome, $preco, $estoque]);

                $mensagem = "Produto adicionado com sucesso!";
                $tipo_mensagem = "sucesso";
            }
        } catch (Exception $e) {
            // Rollback em caso de erro
            $pdo->rollBack();
            $mensagem = "Erro ao adicionar ou atualizar o produto: " . $e->getMessage();
            $tipo_mensagem = "erro";
        }
    } else {
        $mensagem = "Erro ao conectar ao banco de dados.";
        $tipo_mensagem = "erro";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção de Produto</title>
    <link rel="stylesheet" href="./css/adicionar_produto.css">
</head>
<body>

<!-- Exibição da mensagem, se houver -->
<?php if (!empty($mensagem)) : ?>
    <div id="mensagem-container" class="mensagem <?php echo $tipo_mensagem; ?>">
        <?php echo $mensagem; ?>
    </div>
    <script>
        // Esconde a mensagem após 3 segundos
        setTimeout(function() {
            document.getElementById("mensagem-container").style.display = "none";
        }, 3000);
    </script>
<?php endif; ?>

<!-- Formulário de adição de produto -->
<div class="form-container">
    <h3>Cadastrar Novo Produto / Adicionar Estoque e Alterar Preço</h3>
    <form action="adicionar_produto.php" method="POST">
        <div class="input-group">
            <label for="nome">Nome do Produto:</label>
            <input type="text" name="nome" id="nome" required>
        </div>
        
        <div class="input-group">
            <label for="preco">Preço:</label>
            <input type="text" name="preco" id="preco">
        </div>
        
        <div class="input-group">
            <label for="estoque">Quantidade de Estoque:</label>
            <input type="number" name="estoque" id="estoque" required>
        </div>

        <button type="submit" class="btn-submit">Adicionar Produto / Atualizar Estoque e Preço</button>
    </form>
    <br>
    <a href="dashboard.php">
        <button class="btn-voltar">Voltar</button>
    </a>
</div>

</body>
</html>
