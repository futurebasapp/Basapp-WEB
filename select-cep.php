<?php
// Inicia a sessão para identificar o maker registrado
session_start();

// Inclui a conexão com o banco de dados
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura o CEP informado
    $cep = $_POST['cep'];

    // Identifica o ID do maker registrado na sessão
    $maker_id = $_SESSION['maker_id'];  // Certifique-se de que o ID está sendo armazenado na sessão após o cadastro inicial

    // Atualiza o CEP no banco de dados
    $sql = "UPDATE makers SET cep = ? WHERE id = ?";

    // Preparar e executar a query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $cep, $maker_id);

        if ($stmt->execute()) {
            // Redireciona para a próxima página após o sucesso
            header("Location: select-end.php");
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações Extras</title>
    <link rel="stylesheet" href="css/select-cep.css">
</head>

<body>
    <div class="background">
        <div class="modal">
            <h2>Informações extras</h2>
            <p>Informe seu endereço para ser encontrado pelos seus usuários</p>

            <!-- Formulário para captura do CEP -->
            <form action="select-cep.php" method="POST">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" placeholder="Digite seu CEP" required>

                <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" class="find-cep" target="_blank">Encontre seu CEP</a>

                <button type="submit" class="btn-next">Próximo</button>
            </form>
        </div>
    </div>
</body>

</html>