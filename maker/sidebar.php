<?php
// Verifica se a sessão já está ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o nome do usuário está definido
$user_name = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Usuário';
?>
<div class="sidebar">
    <div class="logo">
        <img src="BasApp white.png" alt="BasApp Logo">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        </head>
    </div>
    <ul>
        <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="services_manager.php"><i class="fas fa-tools"></i> Serviços</a></li>
        <li><a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
        <li><a href="finance.php"><i class="fas fa-money-bill-wave"></i> Finanças</a></li>
        <li><a href="professionals.php"><i class="fas fa-user-tie"></i> Profissionais</a></li>
        <li><a href="criar_cupom.php"><i class="fa fa-tags"></i> Criar Cupom</a></li>
        <li><a href="ver_servicos_marcados.php"><i class="fa fa-tags"></i> Ver servicos</a></li>
    </ul>
    <div class="user">
        <a href="alterar_info.php" style="color: #fff; text-decoration: none;">
            <span><?php echo $user_name; ?></span>
        </a>
        <a href="logout.php"><button class="logout-btn">Sair</button></a>
    </div>
</div>