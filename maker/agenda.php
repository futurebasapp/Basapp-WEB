<?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Adicionar horário disponível
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_horario'])) {
    $data = $_POST['data'];
    $horario_inicial = $_POST['horario_inicial'];
    $horario_final = $_POST['horario_final'];

    // Verifica se o horário final é maior que o horário inicial
    if (strtotime($horario_inicial) >= strtotime($horario_final)) {
        echo "Erro: O horário final deve ser maior que o horário inicial.";
    } else {
        $sql = "INSERT INTO agenda (maker_id, data, horario_inicial, horario_final, status) VALUES (?, ?, ?, ?, 'liberado')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $maker_id, $data, $horario_inicial, $horario_final);

        if ($stmt->execute()) {
            echo "Horário adicionado com sucesso!";
            header("Location: agenda.php");
            exit();
        } else {
            echo "Erro ao adicionar horário: " . $conn->error;
        }

        $stmt->close();
    }
}

// Recupera os horários da agenda
$sql = "SELECT * FROM agenda WHERE maker_id = ? ORDER BY data ASC, horario_inicial ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$horarios = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="agenda.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Gerenciamento de Agenda</h1>

        <!-- Formulário para Adicionar Novo Horário -->
        <h2>Adicionar Novo Horário</h2>
        <form method="post" action="agenda.php">
            <input type="hidden" name="add_horario" value="1">
            <label for="data">Data:</label>
            <input type="date" name="data" required>

            <label for="horario_inicial">Horário Inicial:</label>
            <input type="time" name="horario_inicial" required>

            <label for="horario_final">Horário Final:</label>
            <input type="time" name="horario_final" required>

            <button type="submit">Adicionar Horário</button>
        </form>

        <!-- Listagem de Horários -->
        <h2>Seus Horários</h2>
        <div class="horarios-list">
            <?php if ($horarios): ?>
                <?php foreach ($horarios as $horario): ?>
                    <div class="horario-item">
                        <h3><?php echo date("d/m/Y", strtotime($horario['data'])); ?>:
                            <?php echo date("H:i", strtotime($horario['horario_inicial'])); ?> às
                            <?php echo date("H:i", strtotime($horario['horario_final'])); ?></h3>
                        <p>Status: <?php echo $horario['status'] === 'liberado' ? 'Disponível' : 'Ocupado'; ?></p>
                        <form method="post" action="delete_horario.php" onsubmit="return confirm('Tem certeza que deseja remover este horário?');">
                            <input type="hidden" name="horario_id" value="<?php echo $horario['id']; ?>">
                            <button type="submit">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Você ainda não adicionou nenhum horário.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html><?php
session_start();
include('db_connect.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['maker_id'])) {
    header("Location: login.php");
    exit();
}

$maker_id = $_SESSION['maker_id'];

// Adicionar horário disponível
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_horario'])) {
    $data = $_POST['data'];
    $horario_inicial = $_POST['horario_inicial'];
    $horario_final = $_POST['horario_final'];

    // Verifica se o horário final é maior que o horário inicial
    if (strtotime($horario_inicial) >= strtotime($horario_final)) {
        echo "Erro: O horário final deve ser maior que o horário inicial.";
    } else {
        $sql = "INSERT INTO agenda (maker_id, data, horario_inicial, horario_final, status) VALUES (?, ?, ?, ?, 'liberado')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $maker_id, $data, $horario_inicial, $horario_final);

        if ($stmt->execute()) {
            echo "Horário adicionado com sucesso!";
            header("Location: agenda.php");
            exit();
        } else {
            echo "Erro ao adicionar horário: " . $conn->error;
        }

        $stmt->close();
    }
}

// Recupera os horários da agenda
$sql = "SELECT * FROM agenda WHERE maker_id = ? ORDER BY data ASC, horario_inicial ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maker_id);
$stmt->execute();
$result = $stmt->get_result();
$horarios = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - BasApp</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="agenda.css">
</head>

<body>
    <?php include('sidebar.php'); ?>
    <div class="content">
        <h1>Gerenciamento de Agenda</h1>

        <!-- Formulário para Adicionar Novo Horário -->
        <h2>Adicionar Novo Horário</h2>
        <form method="post" action="agenda.php">
            <input type="hidden" name="add_horario" value="1">
            <label for="data">Data:</label>
            <input type="date" name="data" required>

            <label for="horario_inicial">Horário Inicial:</label>
            <input type="time" name="horario_inicial" required>

            <label for="horario_final">Horário Final:</label>
            <input type="time" name="horario_final" required>

            <button type="submit">Adicionar Horário</button>
        </form>

        <!-- Listagem de Horários -->
        <h2>Seus Horários</h2>
        <div class="horarios-list">
            <?php if ($horarios): ?>
                <?php foreach ($horarios as $horario): ?>
                    <div class="horario-item">
                        <h3><?php echo date("d/m/Y", strtotime($horario['data'])); ?>:
                            <?php echo date("H:i", strtotime($horario['horario_inicial'])); ?> às
                            <?php echo date("H:i", strtotime($horario['horario_final'])); ?></h3>
                        <p>Status: <?php echo $horario['status'] === 'liberado' ? 'Disponível' : 'Ocupado'; ?></p>
                        <form method="post" action="delete_horario.php" onsubmit="return confirm('Tem certeza que deseja remover este horário?');">
                            <input type="hidden" name="horario_id" value="<?php echo $horario['id']; ?>">
                            <button type="submit">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Você ainda não adicionou nenhum horário.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>