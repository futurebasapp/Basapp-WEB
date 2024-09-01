<?php
session_start();
include 'db_connect.php';  // Conexão com o banco de dados

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);  // Assumindo que as senhas estão criptografadas com MD5

    $sql = "SELECT * FROM users WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        header("Location: home.php");  // Redirecionar para a página de dashboard
        exit();
    } else {
        $error = "Email ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BasApp</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="container">
        <form method="POST" action="login.php">
            <h1>Login</h1>
            <input type="email" name="email" placeholder="Endereço de email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <?php if ($error) {
                echo "<p class='error'>$error</p>";
            } ?>
            <button type="submit">Continuar</button>
            <a href="forgot_password.php">Esqueci minha senha</a>
        </form>
    </div>
    </div><img src="fotolado.png" alt="" class="fotolado">

</body>

</html>