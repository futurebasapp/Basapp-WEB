<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "empresa_db";

// Criando a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Login - Empresa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        .login-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #555;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-container input[type="submit"] {
            background-color: #6a0dad;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .login-container input[type="submit"]:hover {
            background-color: #4e0e8e;
        }

        .login-container p {
            color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="Login" required>
            <input type="password" name="password" placeholder="Senha" required>
            <input type="submit" value="Continuar">
        </form>
        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $username = $_POST['username'];
                $password = md5($_POST['password']); // Criptografando a senha antes da comparação

                $sql = "SELECT id FROM usuarios WHERE username='$username' AND password='$password'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    header("Location: dashboard.php");
                } else {
                    echo "<p>Login ou senha inválidos!</p>";
                }
            }

            $conn->close();
        ?>
    </div>
</body>
</html>
