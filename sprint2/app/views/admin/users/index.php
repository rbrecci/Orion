<?php

include (__DIR__ . "/../../../middleware/admin.php");

$sql = 'SELECT * FROM users';
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../assets/css/style.css">

</head>
<body>
    <header class="text-center border-bottom p-1">
        <h1>Gerenciamento de Usuários</h1>
    </header>
    <main class="d-flex flex-column align-items-center" style="height: calc(100vh - 65px);">
        <div class="d-flex align-items-center justify-content-center gap-2 mt-5">
            <a href="../../../views/admin/" class="btn btn-danger">Voltar</a>
            <h2>Cadastrar Usuario</h2>
        </div>
        <form action="../../../controllers/users/create.php" method="POST" class="text-center border p-3 rounded mb-5 mt-3">
            <span><?= $_SESSION['message'] ?></span>
            <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
            <input class="form-control mb-3" type="text" name="username" placeholder="Nome de usuário" required>
            <input class="form-control mb-3" type="password" name="pass" placeholder="Senha" required minlength="6">
            <select class="form-control mb-3" name="type" required class="select">
                <option value="user">Usuário</option>
                <option value="admin">Admin</option>
            </select>
            <button class="form-control btn btn-primary" type="submit">Cadastrar</button>
        </form>
        <h3>Usuarios Cadastrados</h3>
        <table class="table table-striped w-50">
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Username</th>
                <th>Senha</th>
                <th>Tipo</th>
                <th>Logou por ultimo em</th>
                <th>Ações</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td>******</td>
                    <td><?= $row['type'] ?></td>
                    <td><?= $row['last_login_date'] ?></td>
                    <td>
                        <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-warning">Editar</a>
                        <a href="../../../controllers/users/delete.php?id=<?= $row['id'] ?>" class="btn btn-danger">Excluir</a>
                    </td>
                </tr>
            <?php endwhile ?>
        </table>
    </main>
</body>
</html>