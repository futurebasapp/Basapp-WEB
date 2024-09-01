<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Verifica se o ID do horário foi passado
if (!isset($_POST['horario_id'])) {
    echo "Horário não encontrado.";
    exit();
}

$horario_id = $_POST['horario_id'];

// Exclui o horário do banco de dados
$sql = "DELETE FROM agenda WHERE id = ? AND maker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $horario_id, $maker_id);

if ($stmt->execute()) {
    echo "Horário removido com sucesso!";
    header("Location: agenda.php");
    exit();
} else {
    echo "Erro ao remover horário: " . $conn->error;
}

$stmt->close();
$conn->close();
