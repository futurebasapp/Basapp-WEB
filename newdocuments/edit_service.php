<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Verifica se o ID do serviço foi passado
if (!isset($_POST['service_id'])) {
    echo "Serviço não encontrado.";
    exit();
}

$service_id = $_POST['service_id'];

// Recupera as informações do serviço
$sql = "SELECT * FROM servicos WHERE id = ? AND maker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $service_id, $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

if (!$service) {
    echo "Serviço não encontrado.";
    exit();
}

// Atualiza o serviço
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    // Upload da nova Foto do Serviço, se fornecida
    if ($_FILES['foto']['name']) {
        $foto = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_path = 'uploads/' . basename($foto);
        move_uploaded_file($foto_tmp, $foto_path);
    } else {
        $foto_path = $service['foto']; // Mantém a foto anterior
    }

    // Atualiza as informações no banco de dados
    $sql = "UPDATE servicos SET nome = ?, descricao = ?, preco = ?, foto = ? WHERE id = ? AND maker_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdis", $nome, $descricao, $preco, $foto_path, $service_id, $maker_id);

    if ($stmt->execute()) {
        echo "Serviço atualizado com sucesso!";
        header("Location: services_manager.php");
        exit();
    } else {
        echo "Erro ao atualizar serviço: " . $conn->error;
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
    <title>Editar Serviço - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="services_manager.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Editar Serviço</h1>
        <form method="post" action="edit_service.php" enctype="multipart/form-data">
            <input type="hidden" name="update_service" value="1">
            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">

            <label for="nome">Nome do Serviço:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($service['nome']); ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" required><?php echo htmlspecialchars($service['descricao']); ?></textarea>

            <label for="preco">Preço:</label>
            <input type="text" name="preco" value="<?php echo htmlspecialchars($service['preco']); ?>" required>

            <label for="foto">Alterar Foto do Serviço:</label>
            <input type="file" name="foto" accept="image/*">
            <img src="<?php echo $service['foto']; ?>" alt="Foto do Serviço Atual" style="max-width: 200px;">

            <button type="submit">Atualizar Serviço</button>
        </form>
    </div>
</body>

</html>