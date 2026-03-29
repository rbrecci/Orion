<?php

include (__DIR__ . "/../../../middleware/admin.php");

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT email, username, type FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if(!$user){
    $_SESSION['message'] = "Usuário não encontrado.";
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <header class="text-center border-bottom p-1">
        <h1>Gerenciamento de Usuários</h1>
    </header>
    <main class="d-flex flex-column align-items-center" style="height: calc(100vh - 65px);">
        <div class="d-flex align-items-center justify-content-center gap-2 mt-5">
            <a href="index.php" class="btn btn-danger">Voltar</a>
            <h2>Atualizar Usuario</h2>
        </div>
        <form action="../../../controllers/users/update.php?id=<?= $id ?>" method="POST" class="text-center border p-3 rounded mb-5 mt-3">
            <span><?= $_SESSION['message'] ?></span>
            <input class="form-control mb-3" type="email" name="email" value="<?= $user['email'] ?>" required>
            <input class="form-control mb-3" type="text" name="username" value="<?= $user['username'] ?>" required>
            <input class="form-control mb-3" type="password" name="pass" placeholder="Nova senha (opcional)" minlength="6">
            <select class="form-control mb-3" name="type" required>
                <option value="user" <?= $user['type'] == 'user' ? 'selected' : '' ?>>Usuário</option>
                <option value="admin" <?= $user['type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit" class="btn btn-primary form-control">Atualizar</button>
        </form>
    </main>
</body>
</html>