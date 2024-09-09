<?php
// Inicia a sessão para armazenar o ID do maker
session_start();

// Inclui a conexão com o banco de dados
include 'db_connect.php';  // Certifique-se de que o arquivo db_connect.php esteja configurado corretamente

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar dados do formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['celular'];  // Nome do campo no formulário é "celular", mapeado para "telefone" no banco
    $email = $_POST['email'];
    $negocio = $_POST['negocio'];
    $aceitou_termos = isset($_POST['termos']) ? 1 : 0;  // Checkbox de aceite dos termos (1 = sim, 0 = não)

    // Inserir dados no banco de dados
    $sql = "INSERT INTO makers (nome, telefone, email, negocio, aceitou_termos) 
            VALUES (?, ?, ?, ?, ?)";

    // Preparar a declaração
    if ($stmt = $conn->prepare($sql)) {
        // Bind dos parâmetros
        $stmt->bind_param("ssssi", $nome, $telefone, $email, $negocio, $aceitou_termos);

        // Executar a query
        if ($stmt->execute()) {
            // Armazena o ID do maker recém-cadastrado na sessão
            $_SESSION['maker_id'] = $conn->insert_id;

            // Redireciona para a página de seleção do tipo de negócio
            header("Location: select-type.php");
            exit();  // Certifique-se de usar exit() para garantir que o redirecionamento aconteça imediatamente
        } else {
            echo "Erro: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $conn->error;
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seja um Maker</title>
    <link rel="stylesheet" href="css/cad-info-ini.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="files/lock.png" alt="Logo BasApp">
        </div>
    </header>

    <main class="container">
        <section class="form-section">
            <h1>Seja um Maker!</h1>
            <form action="cad-info-ini.php" method="POST">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" required>

                <label for="celular">Celular</label>
                <input type="tel" id="celular" name="celular" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="negocio">Nome do negócio</label>
                <input type="text" id="negocio" name="negocio" required>

                <div class="checkbox-container">
                    <input type="checkbox" id="termos" name="termos" required>
                    <label for="termos">Li e aceito o termo de uso e política de privacidade</label>
                </div>

                <button type="submit" class="btn-submit">Elevando o nível do meu negócio</button>
            </form>

            <div class="links">
                <p>Já sou um <a href="login_maker.php" class="link-maker">Maker</a></p>
                <p>Agende sendo um <a href="login_user.php" class="link-user">User</a></p>
            </div>
        </section>

        <section class="image-section">
            <img src="files/salon.jpg" alt="Salão de beleza">
        </section>
    </main>
</body>

</html>