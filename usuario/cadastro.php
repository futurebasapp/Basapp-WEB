<?php
include 'db_connect.php';  // Conexão com o banco de dados

$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);  // Assumindo que as senhas estão criptografadas com MD5

    // Verifica se o email já existe
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Este email já está registrado.";
    } else {
        $sql = "INSERT INTO users (nome, email, senha, criado_em) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $nome, $email, $senha);

        if ($stmt->execute()) {
            $success = "Cadastro realizado com sucesso!";
        } else {
            $error = "Erro ao cadastrar. Por favor, tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - BasApp</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <form method="POST" action="cadastro.php">
            <h1>Cadastro</h1>
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="email" name="email" placeholder="Endereço de email" required>
            <?php if ($error) {
                echo "<p class='error'>$error</p>";
            } ?>
            <?php if ($success) {
                echo "<p class='success'>$success</p>";
            } ?>
            <button type="submit">Continuar</button>
            <a href="login.php">Já tem uma conta? Login</a>
        </form>
    </div>
</body>

</html>