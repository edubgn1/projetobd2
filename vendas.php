<?php
session_start();
require 'conexao.php';

// Conexão ao banco de dados
$pdo = conectarBanco($_SESSION['usuario'], $_SESSION['senha']);

// Definir o número de itens por página
$itens_por_pagina = 9; // Exemplo de 9 produtos por página

// Pegando o número da página atual
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Pesquisa de produtos
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Consultar produtos com a pesquisa, se houver
$query = "SELECT * FROM produtos WHERE nome LIKE ? LIMIT $itens_por_pagina OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute(['%' . $pesquisa . '%']);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar o total de produtos para calcular o número de páginas
$query_count = "SELECT COUNT(*) FROM produtos WHERE nome LIKE ?";
$stmt_count = $pdo->prepare($query_count);
$stmt_count->execute(['%' . $pesquisa . '%']);
$total_produtos = $stmt_count->fetchColumn();
$total_paginas = ceil($total_produtos / $itens_por_pagina);

// Processar a venda dos produtos selecionados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['produtos_selecionados']) && isset($_POST['quantidade'])) {
    // Loop pelos produtos selecionados e suas quantidades
    foreach ($_POST['produtos_selecionados'] as $id_produto => $valor) {
        if (isset($_POST['quantidade'][$id_produto])) {
            $quantidade = $_POST['quantidade'][$id_produto];

            // Verificar o estoque do produto
            $query_estoque = "SELECT estoque FROM produtos WHERE id = ?";
            $stmt_estoque = $pdo->prepare($query_estoque);
            $stmt_estoque->execute([$id_produto]);
            $produto_estoque = $stmt_estoque->fetch(PDO::FETCH_ASSOC);

            // Verificar se o estoque é suficiente
            if ($produto_estoque && $produto_estoque['estoque'] >= $quantidade) {
                // Atualizar o estoque após a venda
                $query_venda = "UPDATE produtos SET estoque = estoque - ? WHERE id = ?";
                $stmt_venda = $pdo->prepare($query_venda);
                $stmt_venda->execute([$quantidade, $id_produto]);
            } else {
                // Se o estoque não for suficiente, mostrar erro
                echo "<p style='color: red;'>Não há estoque suficiente para o produto: " . htmlspecialchars($produto_estoque['nome']) . "</p>";
            }
        }
    }

    // Redirecionar para a página de vendas após processar
    header("Location: vendas.php?sucesso=1");
    exit();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas</title>
    <link rel="stylesheet" href="./css/vendas.css">
</head>
<body>
    <h2>Produtos Disponíveis</h2>

    <!-- Formulário de Pesquisa -->
    <form method="GET" action="vendas.php" class="form-pesquisa">
        <input type="text" name="pesquisa" placeholder="Buscar por produto..." 
               value="<?php echo htmlspecialchars($pesquisa); ?>">
    </form>

    <!-- Exibindo os produtos em Cards -->
    <form method="POST" action="vendas.php">
        <div class="produtos-container">
            <?php foreach ($produtos as $produto): ?>
                <div class="produto-card">
                    <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                    <p>Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p>Estoque: <?php echo $produto['estoque']; ?></p>

                    <!-- Mensagem para produtos sem estoque -->
                    <?php if ($produto['estoque'] == 0): ?>
                        <p style="color: red;">Produto sem estoque</p>
                    <?php endif; ?>

                    <!-- Checkbox para selecionar o produto, só se houver estoque -->
                    <label>
                        <input type="checkbox" name="produtos_selecionados[<?php echo $produto['id']; ?>]" 
                               value="1" <?php echo ($produto['estoque'] == 0) ? 'disabled' : ''; ?>>
                        Selecionar para venda
                    </label>

                    <!-- Campo de quantidade, só aparece se o produto for selecionado e com estoque disponível -->
                    <label>
                        Quantidade:
                        <input type="number" name="quantidade[<?php echo $produto['id']; ?>]" 
                               value="1" min="1" 
                               max="<?php echo $produto['estoque']; ?>" 
                               <?php echo ($produto['estoque'] == 0) ? 'disabled' : ''; ?> required>
                    </label>
                    <br>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Botão para fazer a venda -->
        <button type="submit">Fazer Venda</button>
    </form>

    <!-- Botão Voltar -->
    <a href="dashboard.php">
        <button type="button">Voltar</button>
    </a>

    <!-- Paginação -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="vendas.php?pagina=<?php echo $i; ?>&pesquisa=<?php echo urlencode($pesquisa); ?>" 
               class="<?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <!-- Mensagem de sucesso -->
    <?php if (isset($_GET['sucesso'])): ?>
        <p style="color: green;">Venda(s) realizada(s) com sucesso!</p>
    <?php endif; ?>
</body>
</html>
