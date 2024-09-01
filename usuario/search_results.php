<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Incluindo a conexão com o banco de dados
include 'db_connect.php';

// Obtenha o termo de pesquisa
$search_term = $_GET['query'] ?? '';
$filter = $_GET['filter'] ?? 'services';  // Filtro de pesquisa, por padrão 'services'

// Consultando os serviços ou makers no banco de dados dependendo do filtro
if ($filter === 'services') {
    $sql = "SELECT id, nome, foto, preco FROM servicos WHERE nome LIKE ?";
} elseif ($filter === 'makers') {
    $sql = "SELECT id, nome, foto, descricao FROM makers WHERE nome LIKE ? OR categorias LIKE ?";
}
$stmt = $conn->prepare($sql);
$search_query = '%' . $search_term . '%';
if ($filter === 'makers') {
    $stmt->bind_param('ss', $search_query, $search_query);
} else {
    $stmt->bind_param('s', $search_query);
}
$stmt->execute();
$result = $stmt->get_result();
$results = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Pesquisa - BasApp</title>
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

        .search-results-header {
            padding: 20px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ccc;
        }

        .search-results-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .search-results-header span {
            color: #d41872;
            font-weight: bold;
        }

        .tab-navigation {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .tab-navigation a {
            padding: 10px 20px;
            text-decoration: none;
            color: #555;
            border-bottom: 2px solid transparent;
            font-size: 16px;
            margin: 0 10px;
            transition: border-color 0.3s;
        }

        .tab-navigation a.active {
            color: #d41872;
            border-color: #d41872;
        }

        .results-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
            background-color: #fff;
        }

        .result-item {
            flex-shrink: 0;
            width: 250px;
            margin-bottom: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 10px;
            text-align: left;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .result-item img {
            width: 100px;
            height: 100px;
            border-radius: 10%;
            margin-bottom: 10px;
            float: left;
            margin-right: 10px;
        }

        .result-item h3 {
            font-size: 18px;
            color: #333;
            margin: 0;
            margin-bottom: 10px;
        }

        .result-item p {
            font-size: 14px;
            color: #555;
            margin: 0;
        }

        .result-item .price {
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
            <a href="home.php">
                <img src="file.png" alt="BasApp Logo">
            </a>
        </div>

        <div class="search-bar">
            <form action="search_results.php" method="GET">
                <input type="text" name="query" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($search_term); ?>">
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            </form>
        </div>

        <div class="profile-icon">
            <i class="fa-solid fa-user"></i>
        </div>
    </div>

    <!-- Cabeçalho da Pesquisa -->
    <div class="search-results-header">
        <h2>Buscando por <span><?php echo htmlspecialchars($search_term); ?></span></h2>
    </div>

    <!-- Navegação por Lojas e Itens -->
    <div class="tab-navigation">
        <a href="search_results.php?query=<?php echo htmlspecialchars($search_term); ?>&filter=services" class="<?php echo $filter === 'services' ? 'active' : ''; ?>">Itens</a>
        <a href="search_results.php?query=<?php echo htmlspecialchars($search_term); ?>&filter=makers" class="<?php echo $filter === 'makers' ? 'active' : ''; ?>">Makers</a>
    </div>

    <!-- Resultados da Pesquisa -->
    <div class="results-list">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $result): ?>
                <?php if ($filter === 'services'): ?>
                    <a href="service_details.php?id=<?php echo $result['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="result-item">
                            <img src="<?php echo htmlspecialchars($result['foto']); ?>" alt="<?php echo htmlspecialchars($result['nome']); ?>">
                            <h3><?php echo htmlspecialchars($result['nome']); ?></h3>
                            <p class="price">R$ <?php echo number_format($result['preco'], 2, ',', '.'); ?></p>
                        </div>
                    </a>
                <?php elseif ($filter === 'makers'): ?>
                    <a href="maker_details.php?id=<?php echo $result['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="result-item">
                            <img src="<?php echo htmlspecialchars($result['foto']); ?>" alt="<?php echo htmlspecialchars($result['nome']); ?>">
                            <h3><?php echo htmlspecialchars($result['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($result['descricao']); ?></p>
                        </div>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum resultado encontrado.</p>
        <?php endif; ?>
    </div>

    <script src="https://kit.fontawesome.com/e847f0cdba.js" crossorigin="anonymous"></script>
</body>

</html>