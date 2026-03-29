<?php

include (__DIR__ . "/../../middleware/admin.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT email, username, type FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if(!$user){
            $_SESSION['message'] = "Usuário não encontrado.";
            header("Location: ../../views/admin/users/");
            exit;
        }

        if($_POST){
            $email = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? '';
            $pass = $_POST['pass'] ?? '';
            $type = $_POST['type'] ?? '';

            if($type !== 'admin' && $type !== 'user'){
                $_SESSION['message'] = "Tipo inválido.";
                header("Location: ../../views/admin/users/update.php");
                exit;
            }

            if($id == $_SESSION['user_id'] && $type != 'admin'){
                $_SESSION['message'] = "Você não pode remover seu próprio acesso de admin.";
                header("Location: ../../views/admin/users/");
                exit;
            }

            if(empty($email) || empty($username) || empty($type)){
                $_SESSION['message'] = 'Por favor, preencha os campos corretamente.';
                header("Location: ../../views/admin/users/update.php");
            } else {
                $stmt = $conn->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
                $stmt->bind_param("ssi", $email, $username, $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0){
                    $_SESSION['message'] = 'Email ou nome de usuário indisponível.';
                    header("Location: ../../views/admin/users/update.php");
                } else {
                    if(!empty($pass)){
                        $hash = password_hash($pass, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE users SET email=?, username=?, pass=?, type=? WHERE id=?");
                        $stmt->bind_param("ssssi", $email, $username, $hash, $type, $id);
                    }else{
                        $stmt = $conn->prepare("UPDATE users SET email=?, username=?, type=? WHERE id=?");
                        $stmt->bind_param("sssi", $email, $username, $type, $id);
                    }
                    
                    if($stmt->execute()){
                        $stmt = $conn->prepare("INSERT INTO activity_logs (action_type, entity_type, entity_id, entity_name, admin_id, admin_username) VALUES ('update', 'user', ?, ?, ?, ?)");
                        $stmt->bind_param("isis", $id, $username, $_SESSION['user_id'], $_SESSION['username']);
                        $stmt->execute();

                        $_SESSION['message'] = 'Usuário atualizado com sucesso!';
                        header("Location: ../../views/admin/users/");
                        exit;
                    } else {
                        $_SESSION['message'] = 'Erro ao atualizar usuário.';
                        header("Location: ../../views/admin/users/");
                        exit;
                    }
                }
            }
        }
    }
}

?>