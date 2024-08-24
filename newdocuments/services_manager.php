<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Adiciona Serviço
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    // Upload da Foto do Serviço, se fornecida
    if ($_FILES['foto']['name']) {
        $foto = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_path = 'uploads/' . basename($foto);
        move_uploaded_file($foto_tmp, $foto_path);
    } else {
        $foto_path = '';
    }

    // Insere o serviço no banco de dados
    $sql = "INSERT INTO servicos (maker_id, nome, descricao, preco, foto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issds", $maker_id, $nome, $descricao, $preco, $foto_path);

    if ($stmt->execute()) {
        echo "Serviço adicionado com sucesso!";
        header("Location: services_manager.php");
        exit();
    } else {
        echo "Erro ao adicionar serviço: " . $conn->error;
    }

    $stmt->close();
}

// Recupera os serviços do banco de dados
$sql = "SELECT * FROM servicos WHERE maker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Serviços - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="services_manager.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Gerenciamento de Serviços</h1>

        <!-- Formulário para Adicionar Novo Serviço -->
        <h2>Adicionar Novo Serviço</h2>
        <form method="post" action="services_manager.php" enctype="multipart/form-data">
            <input type="hidden" name="add_service" value="1">
            <label for="nome">Nome do Serviço:</label>
            <input type="text" name="nome" required>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" required></textarea>

            <label for="preco">Preço:</label>
            <input type="text" name="preco" required>

            <label for="foto">Foto do Serviço:</label>
            <input type="file" name="foto" accept="image/*">

            <button type="submit">Adicionar Serviço</button>
        </form>

        <!-- Listagem de Serviços -->
        <h2>Seus Serviços</h2>
        <div class="services-list">
            <?php if ($services): ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-item">
                        <h3><?php echo htmlspecialchars($service['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($service['descricao']); ?></p>
                        <p>Preço: R$ <?php echo number_format($service['preco'], 2, ',', '.'); ?></p>
                        <?php if ($service['foto']): ?>
                            <img src="<?php echo $service['foto']; ?>" alt="Foto do Serviço" style="max-width: 200px;">
                        <?php endif; ?>
                        <form method="post" action="edit_service.php">
                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                            <button type="submit">Editar</button>
                        </form>
                        <form method="post" action="delete_service.php" onsubmit="return confirm('Tem certeza que deseja remover este serviço?');">
                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                            <button type="submit">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Você ainda não adicionou nenhum serviço.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>