<?php
// Inicia a sessão para identificar o maker registrado
session_start();

// Inclui a conexão com o banco de dados
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do formulário
    $cep = $_POST['cep'];
    $bairro = $_POST['bairro'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];

    // Concatena logradouro e número para armazenar em "endereco"
    $endereco_completo = $logradouro . ", " . $numero;

    // Captura o ID do maker da sessão
    $maker_id = $_SESSION['maker_id'];

    // Atualiza o CEP, bairro e endereço no banco de dados
    $sql = "UPDATE makers SET cep = ?, bairro = ?, endereco = ? WHERE id = ?";

    // Preparar e executar a query
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $cep, $bairro, $endereco_completo, $maker_id);

        if ($stmt->execute()) {
            // Redireciona para a próxima página após o sucesso
            header("Location: select-senha.php");
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }

        // Fecha a declaração
        $stmt->close();
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}

// Variáveis para armazenar os dados do endereço
$cep = "";
$bairro = "";
$logradouro = "";

// Captura o ID do maker da sessão
$maker_id = $_SESSION['maker_id'];

// Busca o CEP do maker no banco de dados
$sql = "SELECT cep FROM makers WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $maker_id);
    $stmt->execute();
    $stmt->bind_result($cep);
    $stmt->fetch();
    $stmt->close();
}

// Se o CEP foi encontrado, buscar os detalhes do endereço na API ViaCEP
if (!empty($cep)) {
    // Remove caracteres especiais do CEP
    $cep = preg_replace("/\D/", "", $cep);

    // Faz a requisição à API ViaCEP
    $url = "https://viacep.com.br/ws/$cep/json/";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Se os dados forem encontrados, preencher as variáveis de bairro e logradouro
    if (!isset($data['erro'])) {
        $bairro = $data['bairro'];
        $logradouro = $data['logradouro'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações Extras</title>
    <link rel="stylesheet" href="css/select-end.css">
</head>

<body>
    <div class="background">
        <div class="modal">
            <h2>Informações extras</h2>
            <p>Informe seu endereço para ser encontrado pelos seus usuários</p>

            <!-- Formulário para capturar o endereço -->
            <form action="select-end.php" method="POST">
                <div class="row">
                    <div class="column">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" value="<?php echo $cep; ?>" readonly required>
                    </div>

                    <div class="column">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro" value="<?php echo $bairro; ?>" placeholder="Digite seu bairro" required>
                    </div>
                </div>

                <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" class="find-cep" target="_blank">Encontre seu CEP</a>

                <div class="row">
                    <div class="column">
                        <label for="logradouro">Logradouro</label>
                        <input type="text" id="logradouro" name="logradouro" value="<?php echo $logradouro; ?>" placeholder="Digite seu logradouro" required>
                    </div>

                    <div class="column">
                        <label for="numero">Número</label>
                        <input type="text" id="numero" name="numero" placeholder="Digite o número" required>
                    </div>
                </div>

                <button type="submit" class="btn-next">Próximo</button>
            </form>
        </div>
    </div>
</body>

</html>