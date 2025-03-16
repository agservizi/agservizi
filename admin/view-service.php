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

// Get unread messages count for notifications
$unread_messages_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $unread_messages_count = $row['count'];
}

// Check if service ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to services list
    header('Location: services.php');
    exit;
}

$service_id = (int)$_GET['id'];

// Get service details
$service = null;
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $service = $result->fetch_assoc();
} else {
    // Service not found, redirect to services list
    header('Location: services.php');
    exit;
}
$stmt->close();

// Handle service deletion if requested
$delete_message = '';
if (isset($_POST['delete']) && $_POST['delete'] === 'true') {
    // Delete service
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    
    if ($stmt->execute()) {
        // Redirect to services list with success message
        header("Location: services.php?deleted=true&message=" . urlencode("Servizio '{$service['name']}' eliminato con successo."));
        exit;
    } else {
        $delete_message = "Errore durante l'eliminazione del servizio.";
    }
    $stmt->close();
}

// Handle service activation/deactivation
$status_message = '';
if (isset($_POST['toggle_status']) && $_POST['toggle_status'] === 'true') {
    $new_status = $service['is_active'] ? 0 : 1;
    
    // Update service status
    $stmt = $conn->prepare("UPDATE services SET is_active = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $service_id);
    
    if ($stmt->execute()) {
        $status_text = $new_status ? "attivato" : "disattivato";
        $status_message = "Servizio '{$service['name']}' $status_text con successo.";
        
        // Update service data
        $service['is_active'] = $new_status;
    } else {
        $status_message = "Errore durante l'aggiornamento dello stato del servizio.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Servizio - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --white-color: #ffffff;
            --body-bg: #f1f5f9;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --sidebar-bg: #1e293b;
            --sidebar-width: 260px;
            --header-height: 70px;
            --box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --border-radius: 0.5rem;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--body-bg);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark-color);
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
            transition: var(--transition);
        }

        a:hover {
            color: var(--primary-dark);
        }

        ul {
            list-style: none;
        }

        /* Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--light-color);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--light-color);
            font-size: 1.25rem;
            cursor: pointer;
        }

        .sidebar-menu {
            padding: 1.5rem 0;
        }

        .sidebar-menu-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            padding: 0.75rem 1.5rem;
            margin-top: 1rem;
        }

        .sidebar-menu-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .sidebar-menu-link:hover,
        .sidebar-menu-link.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--white-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-menu-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-menu-link .badge {
            margin-left: auto;
            background-color: var(--danger-color);
            color: var(--white-color);
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
        }

        /* Header */
        .main-header {
            background-color: var(--white-color);
            box-shadow: var(--box-shadow);
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            height: var(--header-height);
        }

        .header-search {
            flex: 1;
            max-width: 400px;
            margin-right: 1rem;
            position: relative;
        }

        .header-search input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 9999px;
            font-size: 0.95rem;
            background-color: var(--light-color);
            transition: var(--transition);
        }

        .header-search input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .header-search i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .header-actions {
            display: flex;
            align-items: center;
        }

        .header-action-item {
            margin-left: 1.5rem;
            position: relative;
        }

        .header-action-button {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .header-action-button:hover {
            color: var(--primary-color);
        }

        .header-action-button .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: var(--white-color);
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .user-dropdown-toggle:hover {
            background-color: var(--light-color);
        }

        .user-dropdown-toggle img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.75rem;
            border: 2px solid var(--primary-light);
        }

        .user-dropdown-toggle .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-dropdown-toggle .user-name {
            font-weight: 500;
            color: var(--dark-color);
        }

        .user-dropdown-toggle .user-role {
            font-size: 0.8rem;
            color: var(--secondary-color);
        }

        .user-dropdown-toggle i {
            margin-left: 0.5rem;
            color: var(--secondary-color);
            transition: var(--transition);
        }

        .user-dropdown-toggle.active i {
            transform: rotate(180deg);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--white-color);
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            min-width: 200px;
            z-index: 100;
            display: none;
            overflow: hidden;
        }

        .user-dropdown-menu.active {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        .user-dropdown-item {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            color: var(--dark-color);
            transition: var(--transition);
        }

        .user-dropdown-item:hover {
            background-color: var(--light-color);
        }

        .user-dropdown-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
            color: var(--secondary-color);
        }

        .user-dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 0.5rem 0;
        }

        /* Main Container */
        .main-container {
            padding: 1.5rem;
        }

        /* Page Title */
        .page-title {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 1.75rem;
            color: var(--dark-color);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 0.5rem;
            color: var(--secondary-color);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--secondary-color);
        }

        /* Content Box */
        .content-box {
            background-color: var(--white-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .content-box-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-box-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: var(--dark-color);
        }

        .content-box-header .actions {
            display: flex;
            gap: 0.5rem;
        }

        .content-box-body {
            padding: 1.5rem;
        }

        /* Service Details */
        .service-details {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .service-icon-container {
            flex: 0 0 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .service-icon {
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .service-status {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .service-status.active {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .service-status.inactive {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .service-info {
            flex: 1;
            min-width: 300px;
        }

        .service-info-item {
            margin-bottom: 1.5rem;
        }

        .service-info-item:last-child {
            margin-bottom: 0;
        }

        .service-info-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .service-info-value {
            font-size: 1rem;
        }

        .service-description {
            background-color: var(--light-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            white-space: pre-line;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 0.95rem;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--white-color);
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .btn-success {
            background-color: var(--success-color);
            color: var(--white-color);
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: var(--white-color);
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: var(--white-color);
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .alert i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            border-left: 4px solid var(--warning-color);
            color: var(--warning-color);
        }

        .alert-info {
            background-color: rgba(6, 182, 212, 0.1);
            border-left: 4px solid var(--info-color);
            color: var(--info-color);
        }

        /* Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: var(--white-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: var(--dark-color);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--danger-color);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .user-dropdown-toggle .user-info {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .header-search {
                display: none;
            }
            
            .service-details {
                flex-direction: column;
            }
            
            .service-icon-container {
                flex: none;
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .page-title {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .breadcrumb {
                margin-top: 0.5rem;
            }
            
            .content-box-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .content-box-header .actions {
                margin-top: 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../images/logo.svg" alt="Agenzia Plinio Logo" class="sidebar-logo">
                <button class="sidebar-toggle">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <div class="sidebar-menu-category">Dashboard</div>
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="dashboard.php" class="sidebar-menu-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-menu-category">Gestione</div>
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="users.php" class="sidebar-menu-link">
                            <i class="fas fa-users"></i>
                            <span>Utenti</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="services.php" class="sidebar-menu-link active">
                            <i class="fas fa-cogs"></i>
                            <span>Servizi</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="contacts.php" class="sidebar-menu-link">
                            <i class="fas fa-envelope"></i>
                            <span>Messaggi</span>
                            <?php if ($unread_messages_count > 0): ?>
                                <span class="badge"><?php echo $unread_messages_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="blog.php" class="sidebar-menu-link">
                            <i class="fas fa-blog"></i>
                            <span>Blog</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-menu-category">Impostazioni</div>
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="profile.php" class="sidebar-menu-link">
                            <i class="fas fa-user"></i>
                            <span>Profilo</span>
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
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cerca...">
                </div>
                
                <div class="header-actions">
                    <div class="header-action-item">
                        <button class="header-action-button">
                            <i class="fas fa-bell"></i>
                            <?php if ($unread_messages_count > 0): ?>
                                <span class="badge"><?php echo $unread_messages_count; ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                    
                    <div class="header-action-item user-dropdown">
                        <div class="user-dropdown-toggle">
                            <img src="../images/admin-avatar.jpg" alt="Admin Avatar">
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($admin_username); ?></span>
                                <span class="user-role">Amministratore</span>
                            </div>
                            <i class="fas fa-chevron-down"></i>
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
                            <div class="user-dropdown-divider"></div>
                            <a href="logout.php" class="user-dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="main-container">
                <div class="page-title">
                    <h1>Visualizza Servizio</h1>
                    <div class="breadcrumb">
                        <div class="breadcrumb-item">
                            <a href="dashboard.php">Home</a>
                        </div>
                        <div class="breadcrumb-item">
                            <a href="services.php">Servizi</a>
                        </div>
                        <div class="breadcrumb-item active">
                            <?php echo htmlspecialchars($service['name']); ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($status_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $status_message; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($delete_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $delete_message; ?></span>
                    </div>
                <?php endif; ?>

                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Dettagli Servizio</h2>
                        <div class="actions">
                            <a href="services.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Torna alla lista
                            </a>
                        </div>
                    </div>
                    <div class="content-box-body">
                        <div class="service-details">
                            <div class="service-icon-container">
                                <div class="service-icon">
                                    <i class="fas <?php echo htmlspecialchars($service['icon']); ?>"></i>
                                </div>
                                <div class="service-status <?php echo $service['is_active'] ? 'active' : 'inactive'; ?>">
                                    <i class="fas <?php echo $service['is_active'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                    <?php echo $service['is_active'] ? 'Attivo' : 'Inattivo'; ?>
                                </div>
                            </div>
                            <div class="service-info">
                                <div class="service-info-item">
                                    <div class="service-info-label">Nome</div>
                                    <div class="service-info-value"><?php echo htmlspecialchars($service['name']); ?></div>
                                </div>
                                <div class="service-info-item">
                                    <div class="service-info-label">Slug</div>
                                    <div class="service-info-value"><?php echo htmlspecialchars($service['slug']); ?></div>
                                </div>
                                <div class="service-info-item">
                                    <div class="service-info-label">Descrizione</div>
                                    <div class="service-description"><?php echo nl2br(htmlspecialchars($service['description'])); ?></div>
                                </div>
                                <div class="service-info-item">
                                    <div class="service-info-label">Data Creazione</div>
                                    <div class="service-info-value"><?php echo date('d/m/Y H:i', strtotime($service['created_at'])); ?></div>
                                </div>
                                <div class="service-info-item">
                                    <div class="service-info-label">Ultimo Aggiornamento</div>
                                    <div class="service-info-value"><?php echo date('d/m/Y H:i', strtotime($service['updated_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="edit-service.php?id=<?php echo $service['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <form method="post" style="display: inline;" onsubmit="return false;">
                                <input type="hidden" name="toggle_status" value="true">
                                <button type="button" class="btn <?php echo $service['is_active'] ? 'btn-warning' : 'btn-success'; ?>" id="toggleStatusBtn">
                                    <i class="fas <?php echo $service['is_active'] ? 'fa-toggle-off' : 'fa-toggle-on'; ?>"></i>
                                    <?php echo $service['is_active'] ? 'Disattiva' : 'Attiva'; ?>
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" id="deleteBtn">
                                <i class="fas fa-trash"></i> Elimina
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Conferma Eliminazione</h3>
                <button type="button" class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare il servizio <strong><?php echo htmlspecialchars($service['name']); ?></strong>?</p>
                <p>Questa azione non può essere annullata.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDelete">Annulla</button>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="delete" value="true">
                    <button type="submit" class="btn btn-danger">Elimina</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toggle Status Confirmation Modal -->
    <div class="modal" id="toggleStatusModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Conferma Modifica Stato</h3>
                <button type="button" class="modal-close" id="closeToggleModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler <?php echo $service['is_active'] ? 'disattivare' : 'attivare'; ?> il servizio <strong><?php echo htmlspecialchars($service['name']); ?></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelToggle">Annulla</button>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="toggle_status" value="true">
                    <button type="submit" class="btn <?php echo $service['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                        <?php echo $service['is_active'] ? 'Disattiva' : 'Attiva'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            
            sidebarToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
            });
            
            // Close sidebar when clicking on overlay
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
            
            // User dropdown
            const userDropdownToggle = document.querySelector('.user-dropdown-toggle');
            const userDropdownMenu = document.querySelector('.user-dropdown-menu');
            
            if (userDropdownToggle) {
                userDropdownToggle.addEventListener('click', function() {
                    userDropdownMenu.classList.toggle('active');
                    this.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userDropdownToggle.contains(event.target) && !userDropdownMenu.contains(event.target)) {
                        userDropdownMenu.classList.remove('active');
                        userDropdownToggle.classList.remove('active');
                    }
                });
            }
            
            // Delete confirmation modal
            const deleteBtn = document.getElementById('deleteBtn');
            const deleteModal = document.getElementById('deleteModal');
            const closeModal = document.getElementById('closeModal');
            const cancelDelete = document.getElementById('cancelDelete');
            
            deleteBtn.addEventListener('click', function() {
                deleteModal.classList.add('active');
            });
            
            closeModal.addEventListener('click', function() {
                deleteModal.classList.remove('active');
            });
            
            cancelDelete.addEventListener('click', function() {
                deleteModal.classList.remove('active');
            });
            
            // Toggle status confirmation modal
            const toggleStatusBtn = document.getElementById('toggleStatusBtn');
            const toggleStatusModal = document.getElementById('toggleStatusModal');
            const closeToggleModal = document.getElementById('closeToggleModal');
            const cancelToggle = document.getElementById('cancelToggle');
            
            toggleStatusBtn.addEventListener('click', function() {
                toggleStatusModal.classList.add('active');
            });
            
            closeToggleModal.addEventListener('click', function() {
                toggleStatusModal.classList.remove('active');
            });
            
            cancelToggle.addEventListener('click', function() {
                toggleStatusModal.classList.remove('active');
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    deleteModal.classList.remove('active');
                }
                if (event.target === toggleStatusModal) {
                    toggleStatusModal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>

