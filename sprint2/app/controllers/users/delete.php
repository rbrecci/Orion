<?php

include (__DIR__ . "/../../middleware/admin.php");

if(isset($_GET['id'])){
    // Garanto que o id é um número
    $id = intval($_GET['id']);

    // Impedindo que o admin exclua seu próprio acesso
    if($id == $_SESSION['user_id']){
        $_SESSION['message'] = "Você não pode remover seu próprio usuário.";
        header("Location: ../../views/admin/users/");
        exit;
    } else{
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        $sql = "SELECT * FROM users WHERE id = $id";
        $result = $conn->query($sql);
        $data = $result->fetch_assoc();
        
        // Executa (ou não) o sql e mostra mensagem
        if($stmt->execute()){
            $stmt = $conn->prepare("INSERT INTO activity_logs (action_type, entity_type, entity_id, entity_name, admin_id, admin_username) VALUES ('delete', 'user', ?, ?, ?, ?)");
            $stmt->bind_param("isis", $id, $data['username'], $_SESSION['user_id'], $_SESSION['username']);
            $stmt->execute();

            $_SESSION['message'] = 'Usuário removido com sucesso!';
            header("Location: ../../views/admin/users/");
            exit;
        } else{
            $_SESSION['message'] = 'Erro ao remover usuário.';
            header("Location: ../../views/admin/users/");
            exit;
        }
    }  
}

?>