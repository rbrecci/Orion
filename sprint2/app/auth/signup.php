<?php

session_start();
include(__DIR__ . "/../config/conn.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $pass = $_POST['pass'] ?? '';

    // Verifica se os campos estão vazios
    if(empty($email) || empty($username) || empty($pass)){
        $message = 'Por favor, preencha os campos corretamente.';
        header("Location: ../views/auth/signup.php");
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
            header("Location: ../views/auth/signup.php");
            exit;
        } else {
            // Cadastro do usuario no banco
            $stmt = $conn->prepare("INSERT INTO users (email, username, pass) VALUES ( ?, ?, ?)");
            $stmt->bind_param("sss", $email, $username, $hash);
            
            if($stmt->execute()){
                $_SESSION['message'] = 'Usuário cadastrado com sucesso! <br> Volte para a página de login e entre com seus dados.';
                header("Location: ../views/auth/signup.php");
                exit;
            } else {
                $_SESSION['message'] = 'Erro ao cadastrar usuário.';
                header("Location: ../views/auth/signup.php");
                exit;
            }
        }
    }
}

?>