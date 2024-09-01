<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['service_id'])) {
    $service_id = $_POST['service_id'];

    // Excluir as referências na tabela servico_categoria
    $sql = "DELETE FROM servico_categoria WHERE servico_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        // Excluir o serviço da tabela servicos
        $sql = "DELETE FROM servicos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $service_id);

        if ($stmt->execute()) {
            echo "Serviço excluído com sucesso!";
        } else {
            echo "Erro ao excluir serviço: " . $conn->error;
        }
    } else {
        echo "Erro ao excluir categorias do serviço: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: services_manager.php");
    exit();
}
