<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Include database connection
// require_once '../includes/db.php';

// Dashboard statistics (simplified example)
$stats = [
    'users' => 150,
    'services' => 8,
    'blog_posts' => 6,
    'messages' => 25
];

$page_title = "Dashboard Admin";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AG Servizi Via Plinio 72</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/logo.png" alt="AG Servizi Via Plinio 72">
                <h3>Admin Panel</h3>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <i class="fas fa-users"></i>
                            <span>Utenti</span>
                        </a>
                    </li>
                    <li>
                        <a href="services.php">
                            <i class="fas fa-cogs"></i>
                            <span>Servizi</span>
                        </a>
                    </li>
                    <li>
                        <a href="blog.php">
                            <i class="fas fa-blog"></i>
                            <span>Blog</span>
                        </a>
                    </li>
                    <li>
                        <a href="messages.php">
                            <i class="fas fa-envelope"></i>
                            <span>Messaggi</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Impostazioni</span>
                        </a>
                    </li>
                    <li>
                        <a href="../includes/logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-search">
                    <input type="text" placeholder="Cerca...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                
                <div class="header-actions">
                    <div class="notifications">
                        <button class="notification-btn">
                            <i class="fas fa-bell"></i>
                            <span class="badge">3</span>
                        </button>
                    </div>
                    
                    <div class="user-profile">
                        <img src="../assets/images/admin/admin-avatar.jpg" alt="Admin">
                        <span>Admin</span>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="page-header">
                    <h1>Dashboard</h1>
                    <p>Benvenuto nel pannello di amministrazione</p>
                </div>
                
                <!-- Stats Cards -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3>Utenti</h3>
                            <p class="stat-number"><?php echo $stats['users']; ?></p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3>Servizi</h3>
                            <p class="stat-number"><?php echo $stats['services']; ?></p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3>Articoli Blog</h3>
                            <p class="stat-number"><?php echo $stats['blog_posts']; ?></p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-blog"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3>Messaggi</h3>
                            <p class="stat-number"><?php echo $stats['messages']; ?></p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="recent-activities">
                    <div class="card">
                        <div class="card-header">
                            <h2>Attività Recenti</h2>
                        </div>
                        <div class="card-body">
                            <ul class="activity-list">
                                <li>
                                    <div class="activity-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p>Nuovo utente registrato: <strong>Mario Rossi</strong></p>
                                        <span class="activity-time">2 ore fa</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p>Nuovo messaggio da: <strong>Laura Bianchi</strong></p>
                                        <span class="activity-time">3 ore fa</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p>Articolo blog modificato: <strong>Come attivare lo SPID</strong></p>
                                        <span class="activity-time">5 ore fa</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p>Servizio aggiornato: <strong>Firma Digitale</strong></p>
                                        <span class="activity-time">1 giorno fa</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity-icon">
                                        <i class="fas fa-user-edit"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p>Profilo utente aggiornato: <strong>Paolo Verdi</strong></p>
                                        <span class="activity-time">1 giorno fa</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Messages & Users -->
                <div class="dashboard-grid">
                    <!-- Recent Messages -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Messaggi Recenti</h2>
                            <a href="messages.php" class="view-all">Vedi tutti</a>
                        </div>
                        <div class="card-body">
                            <ul class="message-list">
                                <li>
                                    <div class="message-sender">
                                        <img src="../assets/images/testimonials/client1.jpg" alt="Laura Bianchi">
                                        <div>
                                            <h4>Laura Bianchi</h4>
                                            <span>laura.bianchi@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="message-preview">
                                        <p>Vorrei informazioni riguardo l'attivazione dello SPID...</p>
                                        <span class="message-time">3 ore fa</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="message-sender">
                                        <img src="../assets/images/testimonials/client2.jpg" alt="Marco Rossi">
                                        <div>
                                            <h4>Marco Rossi</h4>
                                            <span>marco.rossi@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="message-preview">
                                        <p>Salve, vorrei sapere quali sono le offerte di telefonia...</p>
                                        <span class="message-time">5 ore fa</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="message-sender">
                                        <img src="../assets/images/testimonials/client3.jpg" alt="Giulia Verdi">
                                        <div>
                                            <h4>Giulia Verdi</h4>
                                            <span>giulia.verdi@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="message-preview">
                                        <p>Buongiorno, vorrei attivare una PEC per la mia azienda...</p>
                                        <span class="message-time">1 giorno fa</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Recent Users -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Utenti Recenti</h2>
                            <a href="users.php" class="view-all">Vedi tutti</a>
                        </div>
                        <div class="card-body">
                            <ul class="user-list">
                                <li>
                                    <div class="user-info">
                                        <img src="../assets/images/testimonials/client1.jpg" alt="Mario Rossi">
                                        <div>
                                            <h4>Mario Rossi</h4>
                                            <span>mario.rossi@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="user-actions">
                                        <a href="#" class="btn-icon"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="btn-icon"><i class="fas fa-edit"></i></a>
                                    </div>
                                </li>
                                <li>
                                    <div class="user-info">
                                        <img src="../assets/images/testimonials/client2.jpg" alt="Laura Bianchi">
                                        <div>
                                            <h4>Laura Bianchi</h4>
                                            <span>laura.bianchi@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="user-actions">
                                        <a href="#" class="btn-icon"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="btn-icon"><i class="fas fa-edit"></i></a>
                                    </div>
                                </li>
                                <li>
                                    <div class="user-info">
                                        <img src="../assets/images/testimonials/client3.jpg" alt="Paolo Verdi">
                                        <div>
                                            <h4>Paolo Verdi</h4>
                                            <span>paolo.verdi@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="user-actions">
                                        <a href="#" class="btn-icon"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="btn-icon"><i class="fas fa-edit"></i></a>
                                    </div>
                                </li>
                                <li>
                                    <div class="user-info">
                                        <img src="../assets/images/testimonials/client1.jpg" alt="Giulia Neri">
                                        <div>
                                            <h4>Giulia Neri</h4>
                                            <span>giulia.neri@esempio.it</span>
                                        </div>
                                    </div>
                                    <div class="user-actions">
                                        <a href="#" class="btn-icon"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="btn-icon"><i class="fas fa-edit"></i></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

