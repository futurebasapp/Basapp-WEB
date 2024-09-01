<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Incluindo a conexão com o banco de dados
include 'db_connect.php';

// Consultando os makers no banco de dados
$sql = "SELECT id, nome, foto FROM makers"; // Adicionei a coluna 'id'
$result = $conn->query($sql);
$makers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $makers[] = $row;
    }
}

// Consultando os serviços no banco de dados
$sql = "SELECT id, nome, foto, preco FROM servicos";
$result = $conn->query($sql);
$servicos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - BasApp</title>
    <link rel="stylesheet" href="usuario/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #ffffff;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            z-index: 1000;
        }

        .logo img {
            width: 100px;
        }

        .search-bar {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-bar input[type="text"] {
            width: 100%;
            max-width: 800px;
            border: none;
            outline: none;
            background: linear-gradient(to right, #a445b2, #d41872);
            color: #fff;
            font-size: 16px;
            padding: 10px;
            border-radius: 25px;
            text-align: center;
        }

        .search-bar input[type="text"]::placeholder {
            color: #fff;
        }

        .profile-icon img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        body {
            padding-top: 60px;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: center;
            padding: 20px 0;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ccc;
        }

        .category-item {
            text-align: center;
            flex: 1;
            max-width: 100px;
            margin-bottom: 20px;
        }

        .category-item img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .category-item p {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }

        .banner-container {
            display: flex;
            overflow-x: auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ccc;
        }

        .banner-container img {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .makers-container,
        .services-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ccc;
        }

        .makers-header,
        .services-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .makers-header h2,
        .services-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .makers-header a,
        .services-header a {
            color: #d41872;
            text-decoration: none;
            font-size: 14px;
        }

        .makers-list,
        .services-list {
            display: flex;
            overflow-x: auto;
        }

        .maker-item,
        .service-item {
            flex-shrink: 0;
            width: 150px;
            margin-right: 15px;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .maker-item img,
        .service-item img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .maker-item p,
        .service-item p {
            font-size: 14px;
            color: #333;
            margin: 0;
        }

        .service-item .price {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="file.png" alt="BasApp Logo">
        </div>

        <div class="search-bar">
            <form action="search_results.php" method="GET">
                <input type="text" name="query" placeholder="Pesquisar...">
            </form>
        </div>

        <div class="profile-icon">
            <i class="fa-solid fa-user"></i>
        </div>
    </div>

    <!-- Barra de Categorias -->
    <div class="categories">
        <div class="category-item">
            <a href="search_results.php?query=Cabelos&filter=services">
                <img src="./categorias_photos/cabelos.jfif" alt="Cabelos">
                <p>Cabelos</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Depilação&filter=services">
                <img src="./categorias_photos/depilacao.jfif" alt="Depilação">
                <p>Depilação</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Maquiagem&filter=services">
                <img src="./categorias_photos/maquiagem.jfif" alt="Maquiagem">
                <p>Maquiagem</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Massagens&filter=services">
                <img src="./categorias_photos/massagens.jfif" alt="Massagens">
                <p>Massagens</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Unhas&filter=services">
                <img src="./categorias_photos/unhas.jfif" alt="Unhas">
                <p>Unhas</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Cabelos&filter=services">
                <img src="./categorias_photos/cabelos.jfif" alt="Cabelos">
                <p>Cabelos</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Depilação&filter=services">
                <img src="./categorias_photos/depilacao.jfif" alt="Depilação">
                <p>Depilação</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Maquiagem&filter=services">
                <img src="./categorias_photos/maquiagem.jfif" alt="Maquiagem">
                <p>Maquiagem</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Massagens&filter=services">
                <img src="./categorias_photos/massagens.jfif" alt="Massagens">
                <p>Massagens</p>
            </a>
        </div>
        <div class="category-item">
            <a href="search_results.php?query=Unhas&filter=services">
                <img src="./categorias_photos/unhas.jfif" alt="Unhas">
                <p>Unhas</p>
            </a>
        </div>
    </div>

    <!-- Faixa de Banners -->
    <div class="banner-container">
        <img src="BANNER.png" alt="Banner 1">
        <img src="BANNER.png" alt="Banner 2">
        <img src="BANNER.png" alt="Banner 3">
        <img src="BANNER.png" alt="Banner 4">
        <img src="BANNER.png" alt="Banner 1">
        <img src="BANNER.png" alt="Banner 2">
        <img src="BANNER.png" alt="Banner 3">
        <img src="BANNER.png" alt="Banner 4">
    </div>

    <div class="makers-container">
        <div class="makers-header">
            <h2>Maker's da BasApp</h2>
            <a href="#">Ver mais</a>
        </div>
        <div class="makers-list">
            <?php foreach ($makers as $maker): ?>
                <a href="maker_details.php?id=<?php echo $maker['id']; ?>" style="text-decoration: none; color: inherit;">
                    <div class="maker-item">
                        <img src="<?php echo htmlspecialchars($maker['foto']); ?>" alt="<?php echo htmlspecialchars($maker['nome']); ?>">
                        <p><?php echo htmlspecialchars($maker['nome']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Lista de Serviços -->
    <div class="services-container">
        <div class="services-header">
            <h2>Serviços </h2>
            <a href="#">Ver mais</a>
        </div>
        <div class="services-list">
            <?php foreach ($servicos as $servico): ?>
                <a href="service_details.php?id=<?php echo $servico['id']; ?>" style="text-decoration: none; color: inherit;">
                    <div class="service-item">
                        <img src="<?php echo htmlspecialchars($servico['foto']); ?>" alt="<?php echo htmlspecialchars($servico['nome']); ?>">
                        <p><?php echo htmlspecialchars($servico['nome']); ?></p>
                        <p class="price">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/e847f0cdba.js" crossorigin="anonymous"></script>
</body>

</html>