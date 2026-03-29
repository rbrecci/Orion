<?php

session_start();

if(!isset($_SESSION['message'])){
    $_SESSION['message'] = '';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Orion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header class="text-center border-bottom p-1">
        <h1>Orion Login</h1>
    </header>
    <main class="d-flex justify-content-center align-items-center" style="height: calc(100vh - 65px);">
        <form action="../../auth/signin.php" method="POST" class="text-center border p-3 rounded">
            <span><?= $_SESSION['message'] ?></span>
            <input class="form-control mb-3" type="text" name="login" placeholder="Email ou Usuario" required>
            <input class="form-control mb-3" type="password" name="pass" placeholder="senha" minlength="6" required>
            <div>
                <button type="submit" class="btn btn-primary form-control">Entrar</button>
                <span>Nao tem conta?</span>
                <a href="signup.php" class="btn btn-info form-control">Cadastre-se</a>
            </div>
        </form>
    </main>
</body>
</html>