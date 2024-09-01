<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

// Função para criar ou editar cupom
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se todos os campos obrigatórios estão preenchidos
    if (!empty($_POST['codigo']) && !empty($_POST['desconto']) && !empty($_POST['tipo_desconto'])) {
        $codigo = $_POST['codigo'];
        $desconto = $_POST['desconto'];
        $tipo_desconto = $_POST['tipo_desconto'];
        $valor_minimo = $_POST['valor_minimo'] ?? 0;
        $data_validade = $_POST['data_validade'];

        // Verifica se estamos criando ou editando
        if (isset($_POST['cupom_id']) && !empty($_POST['cupom_id'])) {
            // Editar Cupom
            $cupom_id = $_POST['cupom_id'];
            $sql = "UPDATE cupons SET codigo = ?, desconto = ?, tipo_desconto = ?, valor_minimo = ?, data_validade = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddsi", $codigo, $desconto, $tipo_desconto, $valor_minimo, $data_validade, $cupom_id);

            if ($stmt->execute()) {
                echo "Cupom atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar cupom: " . $stmt->error;
            }
        } else {
            // Criar Novo Cupom
            $sql = "INSERT INTO cupons (codigo, desconto, tipo_desconto, valor_minimo, data_validade) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdds", $codigo, $desconto, $tipo_desconto, $valor_minimo, $data_validade);

            if ($stmt->execute()) {
                echo "Cupom criado com sucesso!";
            } else {
                echo "Erro ao criar cupom: " . $stmt->error;
            }
        }

        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos obrigatórios.";
    }
}

// Função para deletar cupom
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM cupons WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "Cupom deletado com sucesso!";
    } else {
        echo "Erro ao deletar cupom: " . $stmt->error;
    }

    $stmt->close();
}

// Função para listar cupons
$sql = "SELECT * FROM cupons ORDER BY data_validade DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar e Gerenciar Cupons</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="criar_cupom.css"> <!-- Adicionando o CSS personalizado -->
</head>

<body>
    <?php include('sidebar.php'); ?> <!-- Inclui a sidebar -->

    <div class="content">
        <h1>Criar Novo Cupom</h1>
        <form method="POST">
            <input type="hidden" name="cupom_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">

            <label for="codigo">Código do Cupom:</label>
            <input type="text" name="codigo" required value="<?php echo isset($_GET['codigo']) ? $_GET['codigo'] : ''; ?>">

            <label for="desconto">Valor do Desconto:</label>
            <input type="number" name="desconto" step="0.01" required value="<?php echo isset($_GET['desconto']) ? $_GET['desconto'] : ''; ?>">

            <label for="tipo_desconto">Tipo de Desconto:</label>
            <select name="tipo_desconto" required>
                <option value="percentual" <?php echo isset($_GET['tipo_desconto']) && $_GET['tipo_desconto'] == 'percentual' ? 'selected' : ''; ?>>Percentual</option>
                <option value="fixo" <?php echo isset($_GET['tipo_desconto']) && $_GET['tipo_desconto'] == 'fixo' ? 'selected' : ''; ?>>Fixo</option>
            </select>

            <label for="valor_minimo">Valor Mínimo de Compra:</label>
            <input type="number" name="valor_minimo" step="0.01" value="<?php echo isset($_GET['valor_minimo']) ? $_GET['valor_minimo'] : ''; ?>">

            <label for="data_validade">Data de Validade:</label>
            <input type="date" name="data_validade" value="<?php echo isset($_GET['data_validade']) ? $_GET['data_validade'] : ''; ?>">

            <button type="submit"><?php echo isset($_GET['id']) ? 'Atualizar Cupom' : 'Criar Cupom'; ?></button>
        </form>

        <h2>Cupons Criados</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Desconto</th>
                    <th>Tipo</th>
                    <th>Valor Mínimo</th>
                    <th>Data de Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($row['desconto']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_desconto']); ?></td>
                        <td><?php echo htmlspecialchars($row['valor_minimo']); ?></td>
                        <td><?php echo htmlspecialchars($row['data_validade']); ?></td>
                        <td>
                            <a href="criar_cupom.php?id=<?php echo $row['id']; ?>" class="btn">Editar</a>
                            <a href="criar_cupom.php?delete_id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Tem certeza que deseja deletar este cupom?');">Deletar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>