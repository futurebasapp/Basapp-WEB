<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Verificar o token
    include 'db_connect.php';
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND created_at > NOW() - INTERVAL 1 HOUR");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();

    if ($email) {
        // Atualizar a senha do usuário
        $stmt = $conn->prepare("UPDATE users SET senha = ? WHERE email = ?");
        $stmt->bind_param('ss', $new_password, $email);
        $stmt->execute();

        // Excluir o token usado
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        echo "Sua senha foi redefinida com sucesso!";
    } else {
        echo "Token inválido ou expirado.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
</head>

<body>
    <h2>Redefinir Senha</h2>
    <form action="reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <label for="new_password">Nova Senha:</label>
        <input type="password" name="new_password" id="new_password" required>
        <button type="submit">Redefinir Senha</button>
    </form>
</body>

</html> 