<?php
// Conexão com o banco de dados
include('db_connect.php');
session_start();

// Variável para armazenar mensagens de erro
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verificar se o email existe no banco de dados
    $sql = "SELECT * FROM makers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verificar se a senha está correta
        if (password_verify($senha, $user['senha'])) {
            // Iniciar sessão e redirecionar para a home
            $_SESSION['maker_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            header("Location: home.php");
            exit();
        } else {
            // Senha incorreta
            $error_message = "Senha incorreta!";
        }
    } else {
        // Email não encontrado
        $error_message = "Usuário não encontrado!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BasApp</title>
    <link rel="stylesheet" href="login_cadastro.css">
</head>

<body>
    <div class="container">
        <div class="form-box">
            <div class="logo">
                <img src="BasApp_white.png" alt="BasApp Logo">
            </div>
            <h2>Login</h2>
            <form method="post" action="login.php">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>

            <!-- Exibir mensagem de erro se houver -->
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Botão para redirecionar para a página de cadastro -->
            <div style="text-align: center; margin-top: 20px;">
                <p>Não tem uma conta?</p>
                <a href="cadastro.php">
                    <button class="cadastro-btn">Criar Conta</button>
                </a>
            </div>
        </div>
    </div>
</body>

</html>