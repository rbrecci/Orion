<?php

include (__DIR__ . "/../../middleware/admin.php");

$sql = 'SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10';
$result = $conn->query($sql);

$actions = [
    'create' => 'registrado',
    'update' => 'editado',
    'delete' => 'removido'
];

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header class="text-center border-bottom p-1">
        <h1>Bem vindo <?= $_SESSION['username'] ?>!</h1>
    </header>
    <main class="d-flex flex-column align-items-center" style="height: calc(100vh - 65px);">
        <div class="d-flex align-items-center gap-2 mt-5">
            <h2>Gerenciamento</h2>
            <a href="../../auth/logout.php" class="btn btn-danger m-1">Sair</a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="users/" class="btn btn-info m-1">Gerenciar Usuários e Admins</a>
            <a href="movies/" class="btn btn-info m-1">Gerenciar Filmes</a>
        </div>
        <div class="d-flex align-items-center gap-2 mt-5">
            <h2>Log de Atividades</h2>
            <a href="../../controllers/logClean.php" class="btn btn-danger">Limpar Log</a>
        </div>
        <table class="table table-striped w-50">
            <?php while($row = $result->fetch_assoc()): ?>
                <?php $entity = $row['entity_type'] == 'user' ? 'usuário' : 'filme'; ?>
                <tr>
                    <td><?= "O {$entity} {$row['entity_name']} foi {$actions[$row['action_type']]} em {$row['created_at']} por {$row['admin_username']}." ?></td>
                </tr>
            <?php endwhile ?>
        </table>
    </main>
</body>
</html>