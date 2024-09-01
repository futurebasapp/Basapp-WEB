<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($service_id > 0) {
    $sql = "SELECT s.*, m.nome AS maker_nome, m.foto AS maker_foto, GROUP_CONCAT(c.nome SEPARATOR ', ') AS categorias
            FROM servicos s
            JOIN makers m ON s.maker_id = m.id
            JOIN servico_categoria sc ON s.id = sc.servico_id
            JOIN categorias c ON sc.categoria_id = c.id
            WHERE s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $service_id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}

// Verifique se um horário foi selecionado anteriormente
$selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : '';
$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['nome']); ?> - BasApp</title>
    <link rel="stylesheet" href="usuario/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .image-section {
            flex: 1;
            max-width: 400px;
        }

        .image-section img {
            max-width: 100%;
            border-radius: 10px;
        }

        .details-section {
            flex: 2;
            margin-left: 40px;
        }

        .details-section h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .details-section p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
        }

        .details-section .price {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .details-section .categories {
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .payment-section {
            margin-top: 20px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .payment-method img {
            margin-right: 10px;
        }

        .checkout-button {
            background-color: #a445b2;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
        }

        .datetime-btn {
            background-color: #d41872;
            border: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        #ler-mais-btn {
            display: none;
            cursor: pointer;
            color: #a445b2;
            border: none;
            background: none;
            font-size: 16px;
        }

        /* Estilos para o modal de seleção de horário */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Estilos para os horários */
        .timeslot {
            display: inline-block;
            margin: 10px;
            padding: 10px;
            background-color: #dcdcdc;
            cursor: pointer;
            border-radius: 5px;
        }

        .timeslot.selected {
            background-color: #a445b2;
            color: white;
        }
    </style>
</head>

<body>
    <div class="back-button">
        <i class="fa-solid fa-arrow-left-long" onclick="goBack()"></i>
    </div>

    <div class="container">
        <div class="image-section">
            <img src="<?php echo $service['foto']; ?>" alt="Imagem do Serviço">
        </div>
        <div class="details-section">
            <h1><?php echo htmlspecialchars($service['nome']); ?></h1>
            <p>Feito por <strong><?php echo htmlspecialchars($service['maker_nome']); ?></strong></p>
            <p id="descricao-text"><?php echo htmlspecialchars($service['descricao']); ?></p>
            <button id="ler-mais-btn" onclick="toggleDescricao()">Ler mais</button>
            <div>
                <span class="categories"><?php echo htmlspecialchars($service['categorias']); ?></span>
            </div>

            <?php if ($selected_date && $start_time && $end_time): ?>
                <div class="selected-timeslot">
                    <p><strong>Horário Selecionado:</strong> <?php echo "$selected_date - $start_time até $end_time"; ?></p>
                    <button class="datetime-btn" onclick="openEditModal()">Editar</button>
                    <button class="datetime-btn" onclick="removeTimeslot()">Remover</button>
                </div>
            <?php else: ?>
                <div class="datetime-selection">
                    <button class="datetime-btn" onclick="openModal()">Selecionar data e horas</button>
                </div>
            <?php endif; ?>

            <div class="payment-section">
                <div class="payment-method">
                    <i class="fa-solid fa-money-bill" alt="Dinheiro"></i>
                    <span>Dinheiro</span>
                </div>
                <button class="checkout-button">Checkout</button>
            </div>
        </div>
    </div>

    <!-- O Modal para Editar ou Selecionar Horários -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Selecione um horário</h2>
            <div id="timeslots">
                <!-- Horários serão gerados aqui -->
            </div>
            <button onclick="confirmDatetime()">Confirmar</button>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function openEditModal() {
            // Reabrir o modal para editar o horário selecionado
            openModal();
        }

        function removeTimeslot() {
            // Redireciona para a mesma página removendo os parâmetros de horário
            window.location.href = `service_details.php?id=<?php echo $service['id']; ?>`;
        }

        function confirmDatetime() {
            var selectedDate = sessionStorage.getItem('selectedDate');
            var selectedTime = sessionStorage.getItem('selectedTime');
            if (selectedDate && selectedTime) {
                // Redireciona para a página de checkout com os parâmetros de data e horário
                window.location.href = `checkout.php?service_id=<?php echo $service['id']; ?>&date=${selectedDate}&startTime=${selectedTime.split(' - ')[0]}&endTime=${selectedTime.split(' - ')[1]}`;
            } else {
                alert("Por favor, selecione um horário.");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var descricaoText = document.getElementById("descricao-text");
            var lerMaisBtn = document.getElementById("ler-mais-btn");

            if (descricaoText.scrollHeight > descricaoText.clientHeight) {
                lerMaisBtn.style.display = "inline";
            }
        });

        function toggleDescricao() {
            var descricaoText = document.getElementById("descricao-text");
            var lerMaisBtn = document.getElementById("ler-mais-btn");

            if (lerMaisBtn.textContent === "Ler mais") {
                descricaoText.style.maxHeight = "none";
                descricaoText.style.overflow = "visible";
                lerMaisBtn.textContent = "Ler menos";
            } else {
                descricaoText.style.maxHeight = "80px";
                descricaoText.style.overflow = "hidden";
                lerMaisBtn.textContent = "Ler mais";
            }
        }

        function openModal() {
            document.getElementById("myModal").style.display = "block";
            loadAvailableTimeslots();
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        function loadAvailableTimeslots() {
            var makerId = <?php echo $service['maker_id']; ?>;

            fetch('get_timeslots.php?maker_id=' + makerId)
                .then(response => response.json())
                .then(data => {
                    var timeslotsDiv = document.getElementById("timeslots");
                    timeslotsDiv.innerHTML = "";
                    data.forEach(slot => {
                        var timeslot = document.createElement("div");
                        timeslot.className = "timeslot";
                        timeslot.textContent = slot.data + " - " + slot.horario_inicial + " até " + slot.horario_final;
                        timeslot.onclick = function() {
                            selectTimeslot(slot);
                        };
                        timeslotsDiv.appendChild(timeslot);
                    });
                });
        }

        function selectTimeslot(slot) {
            var selectedTimeSlot = document.querySelector('.timeslot.selected');
            if (selectedTimeSlot) {
                selectedTimeSlot.classList.remove('selected');
            }
            event.target.classList.add('selected');
            sessionStorage.setItem('selectedDate', slot.data);
            sessionStorage.setItem('selectedTime', slot.horario_inicial + ' - ' + slot.horario_final);
        }
    </script>
    <script src="https://kit.fontawesome.com/e847f0cdba.js" crossorigin="anonymous"></script>

</body>

</html>