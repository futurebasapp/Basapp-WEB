<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';
$startTime = isset($_GET['startTime']) ? $_GET['startTime'] : '';
$endTime = isset($_GET['endTime']) ? $_GET['endTime'] : '';
$user_id = $_SESSION['user']['id'];

if ($service_id > 0 && $date && $startTime && $endTime) {
    // Atualizar o status do horário para "marcado"
    $sql_update = "UPDATE agenda SET status = 'marcado' WHERE maker_id = (SELECT maker_id FROM servicos WHERE id = ?) AND data = ? AND horario_inicial = ? AND horario_final = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('isss', $service_id, $date, $startTime, $endTime);
    $stmt_update->execute();

    // Inserir novo registro na tabela de reservas
    $sql_insert = "INSERT INTO reservas (user_id, servico_id, data, horario_inicial, horario_final) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param('iisss', $user_id, $service_id, $date, $startTime, $endTime);
    $stmt_insert->execute();

    // Redirecionar de volta para service_details.php com o horário selecionado
    header("Location: service_details.php?id=$service_id&selected_date=$date&start_time=$startTime&end_time=$endTime");
    exit();
} else {
    header("Location: home.php");
    exit();
}
