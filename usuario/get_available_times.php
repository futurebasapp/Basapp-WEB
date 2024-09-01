<?php
header('Content-Type: application/json');
include 'db_connect.php';

$maker_id = isset($_GET['maker_id']) ? intval($_GET['maker_id']) : 0;

if ($maker_id > 0) {
    $sql = "SELECT data, horario_inicial, horario_final
            FROM agenda
            WHERE maker_id = ? AND status = 'liberado'
            ORDER BY data, horario_inicial";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $maker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;
    }

    if (!empty($times)) {
        echo json_encode($times);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
