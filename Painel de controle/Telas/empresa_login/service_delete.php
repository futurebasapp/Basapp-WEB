<?php
// Verifique se o parâmetro 'id' foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Conectar ao banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "empresa_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Buscar o nome da imagem associada ao serviço
    $sql = "SELECT foto FROM servicos WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $foto = $row['foto'];

        // Excluir o serviço do banco de dados
        $sql = "DELETE FROM servicos WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            // Remover o arquivo da imagem se existir
            if (file_exists("uploads/" . $foto)) {
                unlink("uploads/" . $foto);
            }
            echo "<script>alert('Serviço excluído com sucesso!'); window.location.href='service_list.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir o serviço: " . $conn->error . "'); window.location.href='service_list.php';</script>";
        }
    } else {
        echo "<script>alert('Serviço não encontrado!'); window.location.href='service_list.php';</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('ID do serviço não fornecido!'); window.location.href='service_list.php';</script>";
}
?>
