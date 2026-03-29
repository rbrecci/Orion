<?php

session_start();

// Se o usuário não estiver logado, ele não pode efetuar o logout
if(!isset($_SESSION['user_id'])){
    $_SESSION['message'] = 'Requisição inválida. Faça login novamente.';
    header("Location: signin.php");
    exit;
} else {
    // Lima a sessão melhor que session_unset()
    $_SESSION = [];

    // Destrói a sessão e redireciona para o login
    session_destroy();
    header("Location: signin.php");
    exit;
}

?>