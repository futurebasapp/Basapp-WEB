<?php
$servername = "localhost";
$username = "root";  // ajuste conforme necessário
$password = "";
$dbname = "basapp_01";  // o nome do banco que você enviou

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
