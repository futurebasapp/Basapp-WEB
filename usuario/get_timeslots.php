<?php
include 'db_connect.php';

$maker_id = isset($_GET['maker_id']) ? intval($_GET['maker_id']) : 0;

if ($maker_id > 0) {
    $sql = "SELECT * FROM agenda WHERE maker_id = ? AND status = 'liberado'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $maker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $timeslots = [];
    while ($row = $result->fetch_assoc()) {
        $timeslots[] = $row;
    }

    echo json_encode($timeslots);
} else {
    echo json_encode([]);
}
