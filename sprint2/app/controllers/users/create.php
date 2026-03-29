<?php

include (__DIR__ . "/../../middleware/admin.php");

// Caso o formulário seja enviado, executo a lógica de cadastro
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $type = $_POST['type'] ?? '';

    // Verifica se os campos estão vazios
    if(empty($email) || empty($username) || empty($pass) || empty($type)){
        $_SESSION['message'] = 'Por favor, preencha os campos corretamente.';
        header("Location: ../../views/admin/users/");
        exit;
    } else {
        // Transformo a senha em hash pra ficar seguro
        $hash = password_hash($pass,  PASSWORD_DEFAULT);
        
        // Select pra verificar se tem usuario igual cadastrado
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            $_SESSION['message'] = 'Email ou nome de usuário indisponível.';
            header("Location: ../../views/admin/users/");
            exit;
        } else {
            // Cadastro do usuario no banco
            $stmt = $conn->prepare("INSERT INTO users (email, username, pass, type) VALUES ( ?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $username, $hash, $type);
            
            // Se executar, ele puxa o id do
            if($stmt->execute()){
                $_SESSION['message'] = 'Usuário cadastrado com sucesso!';
                $id = $stmt->insert_id;

                
                $stmt = $conn->prepare("INSERT INTO activity_logs (action_type, entity_type, entity_id, entity_name, admin_id, admin_username) VALUES ('create', 'user', ?, ?, ?, ?)");
                $stmt->bind_param("isis", $id, $username, $_SESSION['user_id'], $_SESSION['username']);
                $stmt->execute();
                header("Location: ../../views/admin/users/");
                exit;
            } else {
                $_SESSION['message'] = 'Erro ao cadastrar usuário.';
                header("Location: ../../views/admin/users/");
                exit;
            }
        }
    }
}

?>