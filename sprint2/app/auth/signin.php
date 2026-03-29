<?php

session_start();
include(__DIR__ . "/../config/conn.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';

    // Verifica se os campos estão vazios, se estiver mostra a mensagem de erro
    if(empty($login) || empty($pass)){
        $message = 'Por favor, preencha os campos corretamente.';
        header("Location: ../views/auth/signin.php");
        exit;
    } else {
        // Procuro o usuário inserido nos usuários cadastrados no banco (protegido com prepared statements)
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se houver um usuário, verifico a senha criptografada e faço o login
        if($result->num_rows > 0){
            $user = $result->fetch_assoc();

            // Uso password_verify pra fazer match dos dois hashs passados como argumento, verificando se a senha tá correta
            if(password_verify($pass, $user['pass'])){

                // Gero o id da sessão novamente
                session_regenerate_id(true);
                
                // Salvo as informações do usuário na sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['type'] = $user['type'];

                // Atualizo a data de login no banco
                $stmt = $conn->prepare("UPDATE users SET last_login_date = NOW() WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();

                // De acordo com o tipo do usuário, redireciono ele para o index de seu nível de acesso
                if($user['type'] === 'admin'){
                    header("Location: ../views/admin/");
                    exit;
                } else {
                    header("Location: ../views/user/");
                    exit;
                }
            } else {
                $_SESSION['message'] = "Senha incorreta.";
                header("Location: ../views/auth/signin.php");
                exit;
            }
        } else{
            $_SESSION['message'] = "Usuário inexistente.";
            header("Location: ../views/auth/signin.php");
            exit;
        }
    }
} else {
    header("Location: ../views/auth/signin.php");
    exit;
}

?>