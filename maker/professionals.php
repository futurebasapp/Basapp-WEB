<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Adicionar profissional
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_professional'])) {
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    // Upload da Foto do Profissional, se fornecida
    if ($_FILES['foto']['name']) {
        $foto = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_path = 'uploads/' . basename($foto);
        move_uploaded_file($foto_tmp, $foto_path);
    } else {
        $foto_path = '';
    }

    // Insere o profissional no banco de dados
    $sql = "INSERT INTO professionals (maker_id, nome, especialidade, telefone, email, foto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $maker_id, $nome, $especialidade, $telefone, $email, $foto_path);

    if ($stmt->execute()) {
        echo "Profissional adicionado com sucesso!";
        header("Location: professionals.php");
        exit();
    } else {
        echo "Erro ao adicionar profissional: " . $conn->error;
    }

    $stmt->close();
}

// Recupera os profissionais do banco de dados
$sql = "SELECT * FROM professionals WHERE maker_id = ? ORDER BY nome ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$professionals = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Profissionais - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="professionals.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Gerenciamento de Profissionais</h1>

        <!-- Formulário para Adicionar Novo Profissional -->
        <h2>Adicionar Novo Profissional</h2>
        <form method="post" action="professionals.php" enctype="multipart/form-data">
            <input type="hidden" name="add_professional" value="1">
            <label for="nome">Nome do Profissional:</label>
            <input type="text" name="nome" required>

            <label for="especialidade">Especialidade:</label>
            <input type="text" name="especialidade" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="foto">Foto do Profissional:</label>
            <input type="file" name="foto" accept="image/*">

            <button type="submit">Adicionar Profissional</button>
        </form>

        <!-- Listagem de Profissionais -->
        <h2>Seus Profissionais</h2>
        <div class="professionals-list">
            <?php if ($professionals): ?>
                <?php foreach ($professionals as $professional): ?>
                    <div class="professional-item">
                        <h3><?php echo htmlspecialchars($professional['nome']); ?></h3>
                        <p>Especialidade: <?php echo htmlspecialchars($professional['especialidade']); ?></p>
                        <p>Telefone: <?php echo htmlspecialchars($professional['telefone']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($professional['email']); ?></p>
                        <?php if ($professional['foto']): ?>
                            <img src="<?php echo $professional['foto']; ?>" alt="Foto do Profissional" style="max-width: 150px;">
                        <?php endif; ?>
                        <form method="post" action="edit_professional.php">
                            <input type="hidden" name="professional_id" value="<?php echo $professional['id']; ?>">
                            <button type="submit">Editar</button>
                        </form>
                        <form method="post" action="delete_professional.php" onsubmit="return confirm('Tem certeza que deseja remover este profissional?');">
                            <input type="hidden" name="professional_id" value="<?php echo $professional['id']; ?>">
                            <button type="submit">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Você ainda não adicionou nenhum profissional.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>