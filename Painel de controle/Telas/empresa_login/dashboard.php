<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Empresa</title>
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
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#home" class="active">Home</a>
        <a href="service_cadastro.php">Service</a>
        <a href="service_list.php">Serviços Cadastrados</a>
        <a href="agenda.php">Agenda</a>
        <a href="#finance">Finance</a>
    </div>

    <div class="main-content">
        <div class="header">
            Bem-vindo ao Painel de Controle!
        </div>
        <!-- Conteúdo adicional pode ser adicionado aqui -->
    </div>
</body>
</html>
