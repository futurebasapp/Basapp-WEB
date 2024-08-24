<?php
// Conexão com o banco de dados
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
    $categorias = $_POST['categorias'];
    $endereco = $_POST['endereco'];
    $descricao = $_POST['descricao'];

    // Diretório para upload de arquivos
    $upload_dir = 'uploads/';

    // Upload da Foto de Perfil
    $foto_perfil = $_FILES['foto_perfil']['name'];
    $foto_perfil_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_perfil_path = $upload_dir . basename($foto_perfil);

    if (move_uploaded_file($foto_perfil_tmp, $foto_perfil_path)) {
        echo "Foto de perfil enviada com sucesso.<br>";
    } else {
        echo "Erro ao enviar a foto de perfil.<br>";
    }

    // Upload da Foto de Capa
    $foto_capa = $_FILES['foto_capa']['name'];
    $foto_capa_tmp = $_FILES['foto_capa']['tmp_name'];
    $foto_capa_path = $upload_dir . basename($foto_capa);

    if (move_uploaded_file($foto_capa_tmp, $foto_capa_path)) {
        echo "Foto de capa enviada com sucesso.<br>";
    } else {
        echo "Erro ao enviar a foto de capa.<br>";
    }

    // Inserir os dados no banco de dados
    $sql = "INSERT INTO makers (nome, telefone, email, senha, categorias, endereco, descricao, foto, foto_banner) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $nome, $telefone, $email, $senha, $categorias, $endereco, $descricao, $foto_perfil_path, $foto_capa_path);

    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
        header("Location: login.php");
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
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
    <title>Cadastro - BasApp</title>
    <link rel="stylesheet" href="login_cadastro.css">
</head>

<body>
    <div class="container">
        <div class="form-box">
            <div class="logo">
                <img src="BasApp_white.png" alt="BasApp Logo">
            </div>
            <h2>Cadastro</h2>
            <form method="post" action="cadastro.php" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="text" name="telefone" placeholder="Telefone" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <input type="text" name="categorias" placeholder="Categorias" required>
                <input type="text" name="endereco" placeholder="Endereço" required>
                <textarea name="descricao" placeholder="Descreva seu negócio"></textarea>

                <!-- Campos para Upload de Foto de Perfil e Foto de Capa -->
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" required>

                <label for="foto_capa">Foto de Capa:</label>
                <input type="file" name="foto_capa" id="foto_capa" accept="image/*">

                <button type="submit">Cadastrar</button>
            </form>
        </div>
    </div>
</body>

</html>