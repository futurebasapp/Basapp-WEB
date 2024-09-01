<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

// Consulta os pagamentos no banco de dados
$sql = "SELECT * FROM pagamentos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Pagamentos - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="finance.css"> <!-- Caso tenha um arquivo CSS específico para essa página -->
</head>

<body>
    <?php include('sidebar.php'); ?> <!-- Inclui a sidebar -->

    <div class="content">
        <h1>Histórico de Pagamentos</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Data do Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo 'R$ ' . number_format($row['valor'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['data_pagamento']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum pagamento encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>