<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$maker_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($maker_id > 0) {
    // Consultando o maker no banco de dados
    $sql = "SELECT * FROM makers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $maker_id);
    $stmt->execute();
    $maker = $stmt->get_result()->fetch_assoc();

    // Verificando se o maker foi encontrado
    if (!$maker) {
        header("Location: search_results.php");
        exit();
    }

    // Consultando os serviços oferecidos pelo maker
    $sql_services = "SELECT id, nome, foto, preco, descricao FROM servicos WHERE maker_id = ?";
    $stmt_services = $conn->prepare($sql_services);
    $stmt_services->bind_param('i', $maker_id);
    $stmt_services->execute();
    $services = $stmt_services->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: search_results.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($maker['nome']); ?> - BasApp</title>
    <link rel="stylesheet" href="usuario/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .banner {
            width: 100%;
            height: 250px;
            background-size: cover;
            background-position: center;
        }

        .maker-details {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #ffffff;
            flex-direction: column; /* Alinha os itens verticalmente */
        }

        .maker-details img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px; /* Margem entre a imagem e o texto */
        }

        .maker-details h1 {
            font-size: 24px;
            margin: 0;
        }

        .maker-details p {
            font-size: 16px;
            color: #555;
            margin: 5px 0 0 0;
        }

        .services-list-container {
            padding: 20px;
            background-color: #f9f9f9;
        }

        .services-title {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .services-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            width: 46%;
            /* Usar 46% para permitir dois serviços lado a lado com uma margem */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-decoration: none;
            /* Remove sublinhado dos links */
            color: inherit;
            /* Garante que o texto não mude de cor ao ser clicado */
        }

        .service-item img {
            width: 80px;
            /* Tamanho da imagem no lado direito */
            height: 80px;
            border-radius: 10px;
            margin-left: 20px;
        }

        .service-info {
            flex: 1;
            margin-right: 20px;
        }

        .service-info h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .service-info p {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        .service-info .price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .service-item img {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
        }

        /* Estilo do botão de voltar */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #d41872;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .back-button i {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <!-- Botão de voltar -->
    <button class="back-button" onclick="history.back()">
        <i class="fa-solid fa-arrow-left-long"></i> Voltar
    </button>

    <!-- Banner -->
    <div class="banner" style="background-image: url('<?php echo htmlspecialchars($maker['foto_banner']); ?>');"></div>

    <!-- Detalhes do Maker -->
    <div class="maker-details">
        <img src="<?php echo htmlspecialchars($maker['foto']); ?>" alt="<?php echo htmlspecialchars($maker['nome']); ?>">
        <h1><?php echo htmlspecialchars($maker['nome']); ?></h1>
        <p><?php echo htmlspecialchars($maker['endereco']); ?></p>
    </div>

    <!-- Serviços Disponíveis -->
    <div class="services-list-container">
        <h2 class="services-title">Serviços Disponíveis</h2>
        <div class="services-list">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <a href="service_details.php?id=<?php echo $service['id']; ?>" class="service-item">
                        <div class="service-info">
                            <h3><?php echo htmlspecialchars($service['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($service['descricao']); ?></p>
                            <p class="price">A partir de R$ <?php echo number_format($service['preco'], 2, ',', '.'); ?></p>
                        </div>
                        <img src="<?php echo htmlspecialchars(!empty($service['foto']) ? $service['foto'] : 'default-image.png'); ?>" alt="<?php echo htmlspecialchars($service['nome']); ?>">
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum serviço encontrado para este maker.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/e847f0cdba.js" crossorigin="anonymous"></script>
</body>

</html>
