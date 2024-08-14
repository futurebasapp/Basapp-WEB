<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços Cadastrados</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .sidebar {
            height: 100%;
            width: 200px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ccc;
            padding-top: 20px;
            box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #000;
            display: block;
        }

        .sidebar a:hover {
            background-color: #6a0dad;
            color: #fff;
        }

        .sidebar a.active {
            background-color: #6a0dad;
            color: white;
        }

        .main-content {
            margin-left: 200px;
            padding: 20px;
        }

        .header {
            background-color: #6a0dad;
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 24px;
        }

        .service-list {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .service-list th, .service-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .service-list th {
            background-color: #6a0dad;
            color: white;
        }

        .service-list tr:hover {
            background-color: #f2f2f2;
        }

        .service-list td img {
            max-width: 50px;
            height: auto;
        }

        .action-icons {
            display: flex;
            gap: 10px;
        }

        .action-icons img {
            width: 20px;
            height: auto;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Home</a>
        <a href="service_cadastro.php">Service</a>
        <a href="service_list.php" class="active">Serviços Cadastrados</a>
        <a href="agenda.php">Agenda</a>
        <a href="#finance">Finance</a>
    </div>

    <div class="main-content">
        <div class="header">
            Serviços Cadastrados
        </div>

        <table class="service-list">
            <tr>
                <th>Image</th>
                <th>Service Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php
            // Conectar ao banco de dados
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "empresa_db";
            
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM servicos";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='uploads/" . $row['foto'] . "' alt='Service Image'></td>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['categoria'] . "</td>";
                    echo "<td>$" . number_format($row['preco'], 2) . "</td>";
                    echo "<td>" . $row['descricao'] . "</td>";
                    echo "<td class='action-icons'>
                            <a href='service_edit.php?id=" . $row['id'] . "'><img src='edit-icon.png' alt='Edit'></a>
                            <a href='service_delete.php?id=" . $row['id'] . "'><img src='delete-icon.png' alt='Delete'></a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum serviço cadastrado.</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
