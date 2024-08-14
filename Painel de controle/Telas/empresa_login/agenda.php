<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <style>
        /* Estilos CSS */
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

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .calendar-header h2 {
            margin: 0;
            color: #555;
        }

        .calendar-header button {
            background-color: #6a0dad;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .calendar-header button:hover {
            background-color: #4e0e8e;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar .day {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            position: relative;
            height: 100px;
        }

        .calendar .day.blocked {
            color: red;
        }

        .calendar .day.available {
            color: green;
        }

        .calendar .day .events {
            font-size: 12px;
            margin-top: 5px;
        }

        .calendar .day .events span {
            display: block;
        }

        .calendar-header select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }

        .modal h2 {
            margin-top: 0;
        }

        .modal .time-select {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }

        .modal .time-select button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            flex: 1;
        }

        .modal .time-select button.active {
            background-color: #6a0dad;
            color: white;
        }

        .modal .status-select {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .modal .status-select button {
            flex: 1;
            margin: 0 5px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .modal .status-select button.active {
            background-color: #6a0dad;
            color: white;
        }

        .modal button.confirm {
            background-color: #6a0dad;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal button.confirm:hover {
            background-color: #4e0e8e;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php">Home</a>
        <a href="service_cadastro.php">Service</a>
        <a href="service_list.php">Serviços Cadastrados</a>
        <a href="agenda.php" class="active">Agenda</a>
        <a href="#finance">Finance</a>
    </div>

    <div class="main-content">
        <div class="header">
            Agenda
        </div>

        <div class="calendar-header">
            <?php
            // Define o mês e o ano atuais ou os obtém da URL
            $mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
            $ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

            // Configurações de data
            $primeiroDiaDoMes = date('Y-m-01', strtotime("$ano-$mes-01"));
            $primeiroDiaDaSemana = date('N', strtotime($primeiroDiaDoMes));
            $diasNoMes = date('t', strtotime($primeiroDiaDoMes));

            // Funções de navegação entre meses
            $mesAnterior = $mes == 1 ? 12 : $mes - 1;
            $anoAnterior = $mes == 1 ? $ano - 1 : $ano;
            $mesProximo = $mes == 12 ? 1 : $mes + 1;
            $anoProximo = $mes == 12 ? $ano + 1 : $ano;

            // Nome do mês
            $nomeDoMes = date('F Y', strtotime($primeiroDiaDoMes));

            echo "<button onclick=\"window.location.href='agenda.php?mes=$mesAnterior&ano=$anoAnterior'\">&lt;</button>";
            echo "<h2>$nomeDoMes</h2>";
            echo "<button onclick=\"window.location.href='agenda.php?mes=$mesProximo&ano=$anoProximo'\">&gt;</button>";
            ?>
        </div>

        <div class="calendar">
            <?php
            // Conexão com o banco de dados
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "empresa_db";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Conexão falhou: " . $conn->connect_error);
            }

            // Preenche os dias anteriores ao início do mês
            for ($i = 1; $i < $primeiroDiaDaSemana; $i++) {
                echo "<div class='day'></div>";
            }

            // Preenche os dias do mês
            for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                $data = date('Y-m-d', strtotime("$ano-$mes-$dia"));

                $sql = "SELECT status, horarios FROM agenda WHERE dia = '$data'";
                $result = $conn->query($sql);

                $status = '';
                $events = '';

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $status = $row['status'];
                    $horarios = $row['horarios'];
                    if ($status == 'bloqueado') {
                        $events = '<span>Bloqueado</span>';
                        $status = 'blocked';
                    } elseif ($status == 'livre') {
                        $events = "<span>Live: $horarios</span>";
                        $status = 'available';
                    }
                }

                echo "<div class='day $status' data-dia='$data'>";
                echo "<strong>$dia</strong>";
                echo "<div class='events'>$events</div>";
                echo "</div>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <!-- Modal Overlay -->
    <div class="overlay" id="modal">
        <div class="modal">
            <h2>Horários - <span id="modal-dia"></span></h2>
            <div class="status-select">
                <button id="status-livre" class="active">Livre</button>
                <button id="status-bloqueado">Bloqueado</button>
            </div>
            <div class="time-select">
                <button>00:00</button>
                <button>01:00</button>
                <button>02:00</button>
                <button>03:00</button>
                <button>04:00</button>
                <button>05:00</button>
                <button>06:00</button>
                <button>07:00</button>
                <button>08:00</button>
                <button>09:00</button>
                <button>10:00</button>
                <button>11:00</button>
                <button>12:00</button>
                <button>13:00</button>
                <button>14:00</button>
                <button>15:00</button>
                <button>16:00</button>
                <button>17:00</button>
                <button>18:00</button>
                <button>19:00</button>
                <button>20:00</button>
                <button>21:00</button>
                <button>22:00</button>
                <button>23:00</button>
            </div>
            <button class="confirm">Confirmar</button>
        </div>
    </div>

    <script>
        let selectedDay;

        // Abrir o modal ao clicar em um dia disponível
        document.querySelectorAll('.day').forEach(day => {
            day.addEventListener('click', function() {
                selectedDay = this.dataset.dia;
                document.getElementById('modal-dia').textContent = selectedDay;
                document.getElementById('modal').style.display = 'flex';
            });
        });

        // Fechar modal ao clicar no botão de confirmar
        document.querySelector('.modal .confirm').addEventListener('click', function() {
            let status = document.querySelector('.status-select button.active').id.replace('status-', '');
            let selectedTimes = [];
            document.querySelectorAll('.modal .time-select button.active').forEach(btn => {
                selectedTimes.push(btn.textContent);
            });
            let horarios = selectedTimes.join(',');

            // Fazer a chamada AJAX para salvar no banco de dados
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_agenda.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send(`dia=${selectedDay}&status=${status}&horarios=${horarios}`);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload(); // Recarregar a página para ver as mudanças
                }
            };

            document.getElementById('modal').style.display = 'none';
        });

        // Seleção de horários dentro do modal
        document.querySelectorAll('.modal .time-select button').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        });

        // Alternar entre status Livre e Bloqueado
        document.getElementById('status-livre').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('status-bloqueado').classList.remove('active');
        });

        document.getElementById('status-bloqueado').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('status-livre').classList.remove('active');
        });
    </script>
</body>
</html>
