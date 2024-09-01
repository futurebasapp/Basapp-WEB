<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verificar se o e-mail existe no banco de dados
    include 'db_connect.php';
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Gerar um token único
        $token = bin2hex(random_bytes(50));

        // Salvar o token no banco de dados com uma validade de 1 hora
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param('ss', $email, $token);
        $stmt->execute();

        // Enviar o e-mail com o link de redefinição de senha
        $reset_link = "https://localhost/reset_password.php?token=" . $token;
        mail($email, "Redefinição de Senha", "Clique aqui para redefinir sua senha: $reset_link");

        echo "Um e-mail com o link para redefinir sua senha foi enviado.";
    } else {
        echo "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci Minha Senha</title>
</head>

<body>
    <h2>Recuperar Senha</h2>
    <form action="forgot_password.php" method="POST">
        <label for="email">Digite seu e-mail:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Enviar</button>
    </form>
</body>

</html>