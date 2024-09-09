<?php
// Inicia a sessão para identificar o maker registrado
session_start();

// Inclui a conexão com o banco de dados
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura o segmento selecionado
    $segmento = $_POST['segmento'];

    // Identifica o ID do maker registrado na sessão
    $maker_id = $_SESSION['maker_id'];  // Certifique-se de que o ID está sendo armazenado na sessão após o cadastro inicial

    // Atualiza o segmento no banco de dados
    $sql = "UPDATE makers SET segmento = ? WHERE id = ?";

    // Preparar e executar a query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $segmento, $maker_id);

        if ($stmt->execute()) {
            // Redireciona para a próxima página após o sucesso
            header("Location: select-cep.php");
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
    <title>Seleção de Segmento</title>
    <link rel="stylesheet" href="css/select-seg.css">
</head>

<body>
    <div class="background">
        <div class="modal">
            <h2>Informações extras</h2>
            <p>Selecione o seu tipo de segmento</p>

            <!-- Formulário para seleção do segmento -->
            <form action="select-seg.php" method="POST">
                <div class="options">
                    <label class="option">
                        <input type="radio" name="segmento" value="Saúde" required>
                        <img src="files/saude.webp" alt="Saúde">
                        <p>Saúde</p>
                    </label>

                    <label class="option">
                        <input type="radio" name="segmento" value="Estética" required>
                        <img src="files/estetica.jpg" alt="Estética">
                        <p>Estética</p>
                    </label>

                    <label class="option">
                        <input type="radio" name="segmento" value="Aesthetic" required>
                        <img src="files/aesthetic.jfif" alt="Aesthetic">
                        <p>Aesthetic</p>
                    </label>
                </div>

                <button type="submit" class="btn-next">Próximo</button>
            </form>
        </div>
    </div>
</body>

</html>