<?php
// Inclui a conexão com o banco de dados
include 'db_connect.php';

// Inicializa a sessão para identificar o maker registrado
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura o tipo de negócio selecionado
    $tipo_negocio = $_POST['tipo_negocio'];

    // Identifica o ID do último Maker registrado (dependendo de como a sessão é gerenciada)
    $maker_id = $_SESSION['maker_id'];  // Certifique-se de que o ID está sendo armazenado na sessão após o cadastro inicial

    // Atualiza o tipo de negócio no banco de dados
    $sql = "UPDATE makers SET tipo_negocio = ? WHERE id = ?";

    // Preparar e executar a query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $tipo_negocio, $maker_id);

        if ($stmt->execute()) {
            // Redireciona para a próxima página após o sucesso
            header("Location: select-seg.php");
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();
    }

    // Fecha a conexão com o banco
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações Extras</title>
    <link rel="stylesheet" href="css/select-type.css">
</head>

<body>
    <div class="background">
        <div class="modal">
            <h2>Informações extras</h2>
            <p>Que tipo de Maker's você se enquadra?</p>

            <!-- Formulário para selecionar o tipo de negócio -->
            <form action="select-type.php" method="POST">
                <div class="options">
                    <label class="option">
                        <input type="radio" name="tipo_negocio" value="Estabelecimento" required>
                        <img src="files/estabelecimento.jfif" alt="Estabelecimento">
                        <p>Estabelecimento</p>
                    </label>

                    <label class="option">
                        <input type="radio" name="tipo_negocio" value="Autônomo/Profissional" required>
                        <img src="files/autonomo.jfif" alt="Autônomo/Profissional">
                        <p>Autônomo/Profissional</p>
                    </label>
                </div>

                <button type="submit" class="btn-next">Próximo</button>
            </form>
        </div>
    </div>
</body>

</html>