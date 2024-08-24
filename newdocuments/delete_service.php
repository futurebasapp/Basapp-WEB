<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Verifica se o ID do serviço foi passado
if (!isset($_POST['service_id'])) {
    echo "Serviço não encontrado.";
    exit();
}

$service_id = $_POST['service_id'];

// Exclui o serviço do banco de dados
$sql = "DELETE FROM servicos WHERE id = ? AND maker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $service_id, $maker_id);

if ($stmt->execute()) {
    echo "Serviço removido com sucesso!";
    header("Location: services_manager.php");
    exit();
} else {
    echo "Erro ao remover serviço: " . $conn->error;
}

$stmt->close();
$conn->close();
