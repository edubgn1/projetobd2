<?php
session_start();

// Verifica se a sessão foi iniciada corretamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['senha'])) {
    die("Sessão não iniciada corretamente. Redirecionando para login...");
}

require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $id_produto = $_POST['id_produto'];
    $quantidade = $_POST['quantidade'];

    // Conectar ao banco de dados
    $pdo = conectarBanco($_SESSION['usuario'], $_SESSION['senha']);
    
    if ($pdo) {
        try {
            // Inicia a transação
            $pdo->beginTransaction();

            // Verificar o estoque
            $query = "SELECT estoque, preco FROM produtos WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id_produto]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto && $produto['estoque'] >= $quantidade) {
                // Subtrair o estoque
                $query = "UPDATE produtos SET estoque = estoque - ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$quantidade, $id_produto]);

                // Registrar a venda
                $total = $produto['preco'] * $quantidade; // Calcular o total da venda
                $query = "INSERT INTO vendas (id_produto, quantidade, total, usuario_nome, data_venda) 
                          VALUES (?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$id_produto, $quantidade, $total, $_SESSION['usuario']]);

                // Commit a transação
                $pdo->commit();
                echo "Venda realizada com sucesso!";
            } else {
                echo "Estoque insuficiente!";
            }
        } catch (Exception $e) {
            // Rollback em caso de erro
            $pdo->rollBack();
            echo "Erro ao processar a venda: " . $e->getMessage();
        }
    } else {
        echo "Erro ao conectar ao banco de dados.";
    }
}
?>
