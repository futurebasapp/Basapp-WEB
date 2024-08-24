<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Verifica se o ID do profissional foi passado
if (!isset($_POST['professional_id'])) {
    echo "Profissional não encontrado.";
    exit();
}

$professional_id = $_POST['professional_id'];

// Recupera as informações do profissional
$sql = "SELECT * FROM professionals WHERE id = ? AND maker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $professional_id, $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$professional = $result->fetch_assoc();

if (!$professional) {
    echo "Profissional não encontrado.";
    exit();
}

// Atualiza o profissional
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_professional'])) {
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    // Upload da nova Foto do Profissional, se fornecida
    if ($_FILES['foto']['name']) {
        $foto = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_path = 'uploads/' . basename($foto);
        move_uploaded_file($foto_tmp, $foto_path);
    } else {
        $foto_path = $professional['foto']; // Mantém a foto anterior
    }

    // Atualiza as informações no banco de dados
    $sql = "UPDATE professionals SET nome = ?, especialidade = ?, telefone = ?, email = ?, foto = ? WHERE id = ? AND maker_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $nome, $especialidade, $telefone, $email, $foto_path, $professional_id, $maker_id);

    if ($stmt->execute()) {
        echo "Profissional atualizado com sucesso!";
        header("Location: professionals.php");
        exit();
    } else {
        echo "Erro ao atualizar profissional: " . $conn->error;
    }

    $stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profissional - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="professionals.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Editar Profissional</h1>
        <form method="post" action="edit_professional.php" enctype="multipart/form-data">
            <input type="hidden" name="update_professional" value="1">
            <input type="hidden" name="professional_id" value="<?php echo $professional['id']; ?>">

            <label for="nome">Nome do Profissional:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($professional['nome']); ?>" required>

            <label for="especialidade">Especialidade:</label>
            <input type="text" name="especialidade" value="<?php echo htmlspecialchars($professional['especialidade']); ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" value="<?php echo htmlspecialchars($professional['telefone']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($professional['email']); ?>" required>

            <label for="foto">Alterar Foto do Profissional:</label>
            <input type="file" name="foto" accept="image/*">
            <img src="<?php echo $professional['foto']; ?>" alt="Foto do Profissional Atual" style="max-width: 150px;">

            <button type="submit">Atualizar Profissional</button>
        </form>
    </div>
</body>

</html>