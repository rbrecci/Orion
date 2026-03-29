<?php

session_start();
include(__DIR__ . "/../config/conn.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/signin.php");
    exit;
}

if($_SESSION['type'] !== 'admin'){
    header("Location: ../../auth/signin.php");
    exit;
}

if(!isset($_SESSION['message'])){
    $_SESSION['message'] = '';
}

?>