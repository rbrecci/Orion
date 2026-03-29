<?php

include (__DIR__ . "/../../../middleware/admin.php");

// pagina de upload temporária pq ainda não terminei a lógica dos filmes

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT title FROM movies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if(!$movie){
    $_SESSION['message'] = "Filme não encontrado.";
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
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <a href="index.php">Voltar</a>
    <form action="../../../controllers/movies/update.php?id=<?= $id ?>" method="POST">
        <h2>Atualizar Usuario</h2>
        <span><?= $_SESSION['message'] ?></span>
        <input type="email" name="email" value="<?= $movie['email'] ?>" required>
        <input type="text" name="username" value="<?= $movie['username'] ?>" required>
        <input type="password" name="pass" placeholder="Nova senha (opcional)" minlength="6">
        <select name="type" required>
            <option value="user" <?= $movie['type'] == 'user' ? 'selected' : '' ?>>Usuário</option>
            <option value="admin" <?= $movie['type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <button type="submit">Atualizar</button>
    </form>
</body>
</html>