<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header('Location: index.php');
    exit;
}

// Get admin username
$admin_username = $_SESSION['admin_username'];

// Get statistics
// Count users
$users_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $users_count = $row['count'];
}

// Count services
$services_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM services");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $services_count = $row['count'];
}

// Count contact messages
$messages_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM contacts");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $messages_count = $row['count'];
}

// Count blog posts
$posts_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM blog_posts");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $posts_count = $row['count'];
}

// Get recent contact messages
$recent_messages = [];
$result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recent_messages[] = $row;
    }
}

// Get recent users
$recent_users = [];
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recent_users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../images/logo-white.svg" alt="Agenzia Plinio Logo" class="sidebar-logo">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="dashboard.php" class="sidebar-menu-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="users.php" class="sidebar-menu-link">
                            <i class="fas fa-users"></i>
                            <span>Utenti</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="services.php" class="sidebar-menu-link">
                            <i class="fas fa-cogs"></i>
                            <span>Servizi</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="contacts.php" class="sidebar-menu-link">
                            <i class="fas fa-envelope"></i>
                            <span>Messaggi</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="blog.php" class="sidebar-menu-link">
                            <i class="fas fa-blog"></i>
                            <span>Blog</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="settings.php" class="sidebar-menu-link">
                            <i class="fas fa-cog"></i>
                            <span>Impostazioni</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="logout.php" class="sidebar-menu-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-title">
                    <h1>Dashboard</h1>
                </div>
                <div class="header-actions">
                    <div class="user-dropdown">
                        <div class="user-dropdown-toggle">
                            <img src="../images/admin-avatar.jpg" alt="Admin Avatar">
                            <span><?php echo htmlspecialchars($admin_username); ?></span>
                        </div>
                        <div class="user-dropdown-menu">
                            <a href="profile.php" class="user-dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Profilo</span>
                            </a>
                            <a href="settings.php" class="user-dropdown-item">
                                <i class="fas fa-cog"></i>
                                <span>Impostazioni</span>
                            </a>
                            <a href="logout.php" class="user-dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="main-container">
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <div class="dashboard-card-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Utenti</h3>
                            <p><?php echo $users_count; ?></p>
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <div class="dashboard-card-icon services">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Servizi</h3>
                            <p><?php echo $services_count; ?></p>
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <div class="dashboard-card-icon messages">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Messaggi</h3>
                            <p><?php echo $messages_count; ?></p>
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <div class="dashboard-card-icon posts">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Articoli</h3>
                            <p><?php echo $posts_count; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Messaggi Recenti</h2>
                        <a href="contacts.php" class="btn btn-primary btn-sm">Vedi Tutti</a>
                    </div>
                    <div class="content-box-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Servizio</th>
                                        <th>Data</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_messages)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Nessun messaggio trovato</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_messages as $message): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                                <td><?php echo htmlspecialchars($message['service'] ?: 'N/A'); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                                                <td class="actions">
                                                    <a href="view-message.php?id=<?php echo $message['id']; ?>" class="btn btn-primary btn-icon" title="Visualizza">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="delete-message.php?id=<?php echo $message['id']; ?>" class="btn btn-danger btn-icon" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo messaggio?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Utenti Recenti</h2>
                        <a href="users.php" class="btn btn-primary btn-sm">Vedi Tutti</a>
                    </div>
                    <div class="content-box-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Data Registrazione</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_users)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Nessun utente trovato</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                                <td class="actions">
                                                    <a href="view-user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-icon" title="Visualizza">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-icon" title="Modifica">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete-user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-icon" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
        
        // User dropdown
        const userDropdownToggle = document.querySelector('.user-dropdown-toggle');
        const userDropdownMenu = document.querySelector('.user-dropdown-menu');
        
        if (userDropdownToggle) {
            userDropdownToggle.addEventListener('click', function() {
                userDropdownMenu.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!userDropdownToggle.contains(event.target) && !userDropdownMenu.contains(event.target)) {
                    userDropdownMenu.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>

