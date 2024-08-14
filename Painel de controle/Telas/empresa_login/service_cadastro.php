<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre o seu serviço</title>
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

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #555;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container textarea,
        .form-container input[type="file"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-container input[type="submit"] {
            background-color: #6a0dad;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .form-container input[type="submit"]:hover {
            background-color: #4e0e8e;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Home</a>
        <a href="service_cadastro.php" class="active">Service</a>
        <a href="service_list.php">Serviços Cadastrados</a>
        <a href="agenda.php">Agenda</a>
        <a href="#finance">Finance</a>
    </div>

    <div class="main-content">
        <div class="header">
            Cadastre o seu serviço
        </div>

        <div class="form-container">
            <h2>Insira os detalhes do serviço</h2>
            <form action="service_cadastro.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Nome" required>
                <textarea name="descricao" placeholder="Descrição" required></textarea>
                <input type="file" name="foto" required>
                <select name="categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <option value="Corte de Cabelo">Corte de Cabelo</option>
                    <option value="Coloração de Cabelo">Coloração de Cabelo</option>
                    <option value="Hidratação Capilar">Hidratação Capilar</option>
                    <option value="Penteados">Penteados</option>
                    <option value="Manicure">Manicure</option>
                    <option value="Pedicure">Pedicure</option>
                    <option value="Massagem Relaxante">Massagem Relaxante</option>
                    <option value="Massagem Terapêutica">Massagem Terapêutica</option>
                    <option value="Depilação a Laser">Depilação a Laser</option>
                    <option value="Depilação com Cera">Depilação com Cera</option>
                    <option value="Maquiagem">Maquiagem</option>
                    <option value="Design de Sobrancelhas">Design de Sobrancelhas</option>
                    <option value="Micropigmentação">Micropigmentação</option>
                    <option value="Tratamento Facial">Tratamento Facial</option>
                    <option value="Limpeza de Pele">Limpeza de Pele</option>
                    <option value="Bronzeamento">Bronzeamento</option>
                    <option value="Alongamento de Unhas">Alongamento de Unhas</option>
                    <option value="Aplicação de Unhas de Gel">Aplicação de Unhas de Gel</option>
                    <option value="Barbearia">Barbearia</option>
                    <option value="Tatuagem">Tatuagem</option>
                    <option value="Piercing">Piercing</option>
                    <option value="Tratamento Corporal">Tratamento Corporal</option>
                    <option value="Escova Progressiva">Escova Progressiva</option>
                    <option value="Luzes">Luzes</option>
                    <option value="Mechas">Mechas</option>
                    <option value="Alisamento Capilar">Alisamento Capilar</option>
                    <option value="Reconstrução Capilar">Reconstrução Capilar</option>
                    <option value="Botox Capilar">Botox Capilar</option>
                    <option value="Extensão de Cílios">Extensão de Cílios</option>
                    <option value="Remoção de Tatuagem">Remoção de Tatuagem</option>
                </select>
                <input type="number" name="preco" placeholder="Preço" required>
                <input type="submit" value="Confirmar">
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Conectar ao banco de dados
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "empresa_db";
                
                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Falha na conexão: " . $conn->connect_error);
                }

                $nome = $_POST['nome'];
                $descricao = $_POST['descricao'];
                $categoria = $_POST['categoria'];
                $preco = $_POST['preco'];
                
                // Para simplicidade, salvando a imagem na pasta 'uploads'
                $foto = $_FILES['foto']['name'];
                $target = "uploads/" . basename($foto);

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                    // Inserir os dados no banco de dados
                    $sql = "INSERT INTO servicos (nome, descricao, foto, categoria, preco) VALUES ('$nome', '$descricao', '$foto', '$categoria', '$preco')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<p>Serviço cadastrado com sucesso!</p>";
                    } else {
                        echo "<p>Erro ao cadastrar o serviço: " . $conn->error . "</p>";
                    }
                } else {
                    echo "<p>Erro ao fazer upload da foto.</p>";
                }

                $conn->close();
            }
            ?>
        </div>
    </div>
</body>
</html>
