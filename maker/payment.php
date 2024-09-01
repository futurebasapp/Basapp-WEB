<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Pagamento - PayPal</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AVcRj2vV4dc1jOpS-bExZx0uIS6IPqUMcm7cdSmZpqESEG7ST3A2NRsMTiYX2LeGxm3F1I-nb03A_vtt&currency=BRL"></script>
</head>

<body>
    <h1>Pagar pelo Serviço</h1>

    <!-- Botão de PayPal -->
    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                // Criação do pedido
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '50.00' // Valor do serviço em BRL, substitua conforme necessário
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                // Quando o pagamento for aprovado
                return actions.order.capture().then(function(details) {
                    // Exibe mensagem de sucesso
                    alert('Pagamento concluído por ' + details.payer.name.given_name);

                    // Aqui você pode redirecionar para uma página de sucesso ou processar o pagamento
                    window.location.href = "success.php?orderID=" + data.orderID;
                });
            }
        }).render('#paypal-button-container'); // Renderiza o botão de pagamento
    </script>
</body>

</html>