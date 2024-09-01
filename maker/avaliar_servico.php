<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nota'], $_POST['comentario'], $_POST['servico_id'])) {
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];
    $servico_id = $_POST['servico_id'];
    $user_id = $_SESSION['user_id']; // Presumindo que o ID do usuário está na sessão

    // Insere a avaliação no banco de dados
    $sql = "INSERT INTO avaliacoes (servico_id, user_id, nota, comentario) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $servico_id, $user_id, $nota, $comentario);

    if ($stmt->execute()) {
        echo "Avaliação enviada com sucesso!";
    } else {
        echo "Erro ao enviar avaliação: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: services_manager.php"); // Redireciona de volta para a página de serviços
    exit();
}
