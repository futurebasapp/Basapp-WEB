<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Inicializa variáveis de erro e sucesso
$error_message = '';
$success_message = '';

// Adiciona Serviço
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categorias = $_POST['categorias']; // Array de categorias selecionadas

    // Valida o formulário
    if (empty($nome) || empty($descricao) || empty($preco) || empty($categorias) || count($categorias) > 3) {
        $error_message = "Por favor, preencha todos os campos corretamente e selecione até 3 categorias.";
    } else {
        // Upload da Foto do Serviço, se fornecida
        if (!empty($_FILES['foto']['name'])) {
            $foto = $_FILES['foto']['name'];
            $foto_tmp = $_FILES['foto']['tmp_name'];
            $foto_path = 'uploads/' . basename($foto);

            if (!move_uploaded_file($foto_tmp, $foto_path)) {
                $error_message = "Erro ao fazer upload da imagem.";
            }
        } else {
            $foto_path = ''; // Caso a foto não seja enviada
        }

        // Insere o serviço no banco de dados
        if (empty($error_message)) {
            $sql = "INSERT INTO servicos (maker_id, nome, descricao, preco, foto) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issds", $maker_id, $nome, $descricao, $preco, $foto_path);

            if ($stmt->execute()) {
                $servico_id = $stmt->insert_id;

                // Insere as categorias selecionadas
                foreach ($categorias as $categoria_id) {
                    $sql = "INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $servico_id, $categoria_id);
                    $stmt->execute();
                }

                $success_message = "Serviço adicionado com sucesso!";
            } else {
                $error_message = "Erro ao adicionar serviço: " . $conn->error;
            }

            $stmt->close();
        }
    }
}

// Recupera os serviços do banco de dados
$sql = "SELECT * FROM servicos WHERE maker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
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

        <!-- Exibe mensagens de sucesso e erro -->
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Formulário para Adicionar Serviço -->
        <form method="post" enctype="multipart/form-data">
            <label for="nome">Nome do Serviço:</label>
            <input type="text" name="nome" required>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" required></textarea>

            <label for="preco">Preço:</label>
            <input type="text" name="preco" required>

            <label for="categorias">Selecione até 3 Categorias:</label>
            <select name="categorias[]" multiple required>
                <?php
                // Recupera todas as categorias do banco de dados
                $result = $conn->query("SELECT id, nome FROM categorias");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                }
                ?>
            </select>

            <label for="foto">Foto do Serviço:</label>
            <input type="file" name="foto" accept="image/*" id="foto" onchange="previewImage(event)">

            <!-- Espaço para a pré-visualização da imagem -->
            <img id="preview" src="#" alt="Prévia da Imagem" style="display: none; max-width: 200px; margin-top: 10px;">

            <button type="submit" name="add_service">Adicionar Serviço</button>
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

                        <!-- Mostrar categorias associadas -->
                        <p>Categorias:
                            <?php
                            $sql = "SELECT c.nome FROM categorias c
                                    JOIN servico_categoria sc ON c.id = sc.categoria_id
                                    WHERE sc.servico_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $service['id']);
                            $stmt->execute();
                            $categorias_result = $stmt->get_result();
                            $categorias = [];
                            while ($cat = $categorias_result->fetch_assoc()) {
                                $categorias[] = $cat['nome'];
                            }
                            echo implode(', ', $categorias);
                            ?>
                        </p>

                        <!-- Botões para editar e remover o serviço -->
                        <form method="post" action="edit_service.php" onsubmit="return confirm('Tem certeza que deseja editar este serviço?');">
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

    <!-- JavaScript para pré-visualização da imagem -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('preview');
                preview.src = reader.result;
                preview.style.display = 'block'; // Exibe a imagem
            };
            reader.readAsDataURL(event.target.files[0]); // Carrega a imagem selecionada
        }
    </script>
</body>

</html>