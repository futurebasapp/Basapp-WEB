<?php
// Inicia a sessão para identificar o maker registrado
session_start();

// Inclui a conexão com o banco de dados
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura a senha inserida
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se a senha e a confirmação de senha são iguais
    if ($senha !== $confirmar_senha) {
        echo "As senhas não coincidem.";
    } else {
        // Hash da senha para segurança
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Captura o ID do maker da sessão
        $maker_id = $_SESSION['maker_id'];

        // Atualiza a senha no banco de dados
        $sql = "UPDATE makers SET senha = ? WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $senha_hash, $maker_id);

            if ($stmt->execute()) {
                // Redireciona para a página final ou dashboard após o sucesso
                header("Location: ../lockwood/maker/login.php");  // Ajuste o redirecionamento conforme a necessidade
                exit();
            } else {
                echo "Erro ao salvar a senha: " . $stmt->error;
            }

            // Fecha a declaração
            $stmt->close();
        }

        // Fecha a conexão com o banco de dados
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Senha</title>
    <link rel="stylesheet" href="css/select-senha.css">
</head>

<body>
    <div class="background">
        <div class="modal">
            <h2>Definir Senha</h2>
            <p>Insira uma senha para proteger sua conta</p>

            <!-- Formulário para capturar a senha -->
            <form action="select-senha.php" method="POST">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                <label for="confirmar_senha">Confirmar Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>

                <button type="submit" class="btn-next">Salvar Senha</button>
            </form>
        </div>
        
    </div>
</body>

</html>