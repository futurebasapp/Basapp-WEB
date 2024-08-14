<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "empresa_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    $dia = $_POST['dia'];
    $status = $_POST['status'];
    $horarios = $_POST['horarios'];

    $sql = "INSERT INTO agenda (dia, status, horarios) VALUES ('$dia', '$status', '$horarios')
            ON DUPLICATE KEY UPDATE status='$status', horarios='$horarios'";

    if ($conn->query($sql) === TRUE) {
        echo "Dados atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar os dados: " . $conn->error;
    }

    $conn->close();
}
?>
