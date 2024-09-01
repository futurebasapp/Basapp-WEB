<?php
include('db_connect.php');

// Pegando o Order ID da URL
$orderID = $_GET['orderID'];

// Aqui você pode usar a API do PayPal para obter mais detalhes sobre o pagamento, como o valor
$valor_pago = 50.00; // Substitua pelo valor real do serviço
$status = 'completo'; // Status do pagamento

// Salvando os dados do pagamento no banco de dados
$sql = "INSERT INTO pagamentos (order_id, valor, status) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sds", $orderID, $valor_pago, $status);
$stmt->execute();

echo "<h1>Pagamento Concluído!</h1>";
echo "<p>Order ID: " . htmlspecialchars($orderID) . "</p>";
echo "<p>Valor Pago: R$" . number_format($valor_pago, 2, ',', '.') . "</p>";
echo "<p>Status do Pagamento: " . htmlspecialchars($status) . "</p>";

// Fechando a conexão
$stmt->close();
$conn->close();
