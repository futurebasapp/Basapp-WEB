<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

// Inicializa a variável $maker
$maker = null;

// Recupera as informações atuais do usuário
$maker_id = $_SESSION['maker_id'];
$sql = "SELECT * FROM makers WHERE id = ?";
$stmt = $conn->prepare($sql);

// Verifica se a preparação da consulta foi bem-sucedida
if ($stmt) {
    $stmt->bind_param("i", $maker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o usuário foi encontrado
    if ($result->num_rows == 1) {
        $maker = $result->fetch_assoc();
    } else {
        echo "Usuário não encontrado.";
        exit();
    }

    $stmt->close();
} else {
    echo "Erro na consulta ao banco de dados.";
    exit();
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $categorias = $_POST['categorias'];
    $endereco = $_POST['endereco'];
    $descricao = $_POST['descricao'];

    // Atualiza a senha se uma nova senha for fornecida
    if (!empty($_POST['senha'])) {
        $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
        $sql = "UPDATE makers SET senha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $senha, $maker_id);
        $stmt->execute();
    }

    // Upload da nova Foto de Perfil, se fornecida
    if ($_FILES['foto_perfil']['name']) {
        $foto_perfil = $_FILES['foto_perfil']['name'];
        $foto_perfil_tmp = $_FILES['foto_perfil']['tmp_name'];
        $foto_perfil_path = 'uploads/' . basename($foto_perfil);
        move_uploaded_file($foto_perfil_tmp, $foto_perfil_path);
    } else {
        $foto_perfil_path = $maker['foto'];
    }

    // Upload da nova Foto de Capa, se fornecida
    if ($_FILES['foto_capa']['name']) {
        $foto_capa = $_FILES['foto_capa']['name'];
        $foto_capa_tmp = $_FILES['foto_capa']['tmp_name'];
        $foto_capa_path = 'uploads/' . basename($foto_capa);
        move_uploaded_file($foto_capa_tmp, $foto_capa_path);
    } else {
        $foto_capa_path = $maker['foto_banner'];
    }

    // Atualiza as informações no banco de dados
    $sql = "UPDATE makers SET nome = ?, telefone = ?, email = ?, categorias = ?, endereco = ?, descricao = ?, foto = ?, foto_banner = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $nome, $telefone, $email, $categorias, $endereco, $descricao, $foto_perfil_path, $foto_capa_path, $maker_id);

    if ($stmt->execute()) {
        echo "Informações atualizadas com sucesso!";
        // Atualizar a sessão para refletir o novo nome
        $_SESSION['nome'] = $nome;
        header("Location: alterar_info.php"); // Redirecionar após a atualização
        exit();
    } else {
        echo "Erro ao atualizar: " . $conn->error;
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
    <title>Alterar Informações - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="alterar_info.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Alterar Informações</h1>

        <!-- Verifica se $maker foi inicializado corretamente -->
        <?php if ($maker): ?>
            <form method="post" action="alterar_info.php" enctype="multipart/form-data">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($maker['nome']); ?>" required>

                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" value="<?php echo htmlspecialchars($maker['telefone']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($maker['email']); ?>" required>

                <label for="categorias">Categorias:</label>
                <input type="text" name="categorias" value="<?php echo htmlspecialchars($maker['categorias']); ?>" required>

                <label for="endereco">Endereço:</label>
                <input type="text" name="endereco" value="<?php echo htmlspecialchars($maker['endereco']); ?>" required>

                <label for="descricao">Descrição:</label>
                <textarea name="descricao"><?php echo htmlspecialchars($maker['descricao']); ?></textarea>

                <!-- Campos para Upload de Foto de Perfil e Foto de Capa -->
                <label for="foto_perfil">Alterar Foto de Perfil:</label>
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
                <img src="<?php echo htmlspecialchars($maker['foto']); ?>" alt="Foto de Perfil Atual">

                <label for="foto_capa">Alterar Foto de Capa:</label>
                <input type="file" name="foto_capa" id="foto_capa" accept="image/*">
                <img src="<?php echo htmlspecialchars($maker['foto_banner']); ?>" alt="Foto de Capa Atual">

                <!-- Campo para alterar senha -->
                <label for="senha">Nova Senha:</label>
                <input type="password" name="senha" placeholder="Deixe em branco se não quiser alterar">

                <button type="submit">Atualizar Informações</button>
            </form>
        <?php else: ?>
            <p>Erro ao carregar as informações do usuário.</p>
        <?php endif; ?>
    </div>
</body>

</html>