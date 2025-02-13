<?php
// Arquivo de conexão com o banco de dados
function conectarBanco($usuario, $senha) {
    try {
        $pdo = new PDO('pgsql:host=localhost;dbname=venda_db', $usuario, $senha);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        return null;
    }
}
?>
