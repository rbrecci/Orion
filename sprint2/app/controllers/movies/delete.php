<?php

include (__DIR__ . "/../../middleware/admin.php");

// atualizar logica por causa dos arquivos que sao armazenados agora

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);

    $sql = "SELECT * FROM movies WHERE id = $id";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();

    if($stmt->execute()){
        $stmt = $conn->prepare("INSERT INTO activity_logs (action_type, entity_type, entity_id, entity_name, admin_id, admin_username) VALUES ('delete', 'movie', ?, ?, ?, ?)");
        $stmt->bind_param("isis", $id, $data['title'], $_SESSION['user_id'], $_SESSION['username']);
        $stmt->execute();

        $_SESSION['message'] = 'Filme removido com sucesso!';
        header("Location: ../../views/admin/movies/");
        exit;
    } else{
        $_SESSION['message'] = 'Erro ao remover filme.';
        header("Location: ../../views/admin/movies/");
        exit;
    }
}

?>