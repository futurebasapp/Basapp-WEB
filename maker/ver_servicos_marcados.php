<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Recupera as reservas dos serviços marcados para este maker
$sql = "SELECT r.id, u.nome AS cliente_nome, s.nome AS servico_nome, r.data, r.horario_inicial, r.horario_final
        FROM reservas r
        JOIN users u ON r.user_id = u.id
        JOIN servicos s ON r.servico_id = s.id
        WHERE s.maker_id = ?
        ORDER BY r.data ASC, r.horario_inicial ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maker_id);
$stmt->execute();
$reservas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços Marcados - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .content {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .reservas-list {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #a445b2;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-reservations {
            text-align: center;
            color: #666;
            font-size: 18px;
            margin-top: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            background-color: #d41872;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #c41765;
        }
    </style>
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Serviços Marcados</h1>
        <div class="reservas-list">
            <?php if ($reservas): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Data</th>
                            <th>Horário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva['cliente_nome']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['servico_nome']); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($reserva['data'])); ?></td>
                                <td><?php echo date("H:i", strtotime($reserva['horario_inicial'])) . ' - ' . date("H:i", strtotime($reserva['horario_final'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-reservations">Não há serviços marcados até o momento.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>