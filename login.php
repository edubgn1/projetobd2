<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Conecta ao banco usando os dados de login
    $pdo = conectarBanco($usuario, $senha);

    if ($pdo) {
        // Armazena os dados do usuário na sessão
        $_SESSION['usuario'] = $usuario;
        $_SESSION['senha'] = $senha; // Armazenando na sessão
        
        // Redireciona para o dashboard após login
        header("Location: dashboard.php");
        exit();
    } else {
        // Redireciona de volta para o login com erro
        header("Location: index.php?erro=1");
        exit();
    }
}
?>
