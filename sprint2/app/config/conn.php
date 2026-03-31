<?php

// Salvando as informações de acesso ao banco em variáveis
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'oriondb';

// Executando a conexão com o banco via mysqli e atribuindo à variável $conn
$conn = new mysqli($servername, $username, $password, $dbname, 3310)

?>