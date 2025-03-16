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

// Handle search
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitize_input($_GET['search']);
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total users count for pagination
$total_users = 0;
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE name LIKE ? OR email LIKE ?");
    $search_param = "%{$search}%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_users = $row['count'];
    }
    $stmt->close();
} else {
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_users = $row['count'];
    }
}

$total_pages = ceil($total_users / $per_page);

// Get users with pagination and search
$users = [];
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $search_param = "%{$search}%";
    $stmt->bind_param("ssii", $search_param, $search_param, $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    $stmt->close();
}

// Handle user deletion if requested
$delete_message = '';
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $delete_message = "Utente eliminato con successo.";
            // Redirect to remove the delete parameter from URL
            header("Location: users.php?deleted=true");
            exit;
        } else {
            $delete_message = "Errore durante l'eliminazione dell'utente.";
        }
    } else {
        $delete_message = "Utente non trovato.";
    }
    $stmt->close();
}

// Check for deleted success message
if (isset($_GET['deleted']) && $_GET['deleted'] === 'true') {
    $delete_message = "Utente eliminato con successo.";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti - Admin Panel</title>
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

        /* Search and Filter */
        .search-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .search-box button {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 0.25rem;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .table th {
            font-weight: 600;
            color: var(--dark-color);
            background-color: var(--light-color);
            position: sticky;
            top: 0;
        }

        .table tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table td:first-child,
        .table th:first-child {
            padding-left: 1.5rem;
        }

        .table td:last-child,
        .table th:last-child {
            padding-right: 1.5rem;
        }

        .table .actions {
            display: flex;
            gap: 0.5rem;
        }

        .table .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .table .btn-icon:hover {
            transform: translateY(-2px);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pagination-item {
            margin: 0 0.25rem;
        }

        .pagination-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--white-color);
            color: var(--dark-color);
            transition: var(--transition);
            box-shadow: var(--box-shadow);
        }

        .pagination-link:hover,
        .pagination-link.active {
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
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

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
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
            
            .search-filter {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: 100%;
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
                        <a href="users.php" class="sidebar-menu-link active">
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
                    <h1>Gestione Utenti</h1>
                    <div class="breadcrumb">
                        <div class="breadcrumb-item">
                            <a href="dashboard.php">Home</a>
                        </div>
                        <div class="breadcrumb-item active">
                            Utenti
                        </div>
                    </div>
                </div>

                <?php if (!empty($delete_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $delete_message; ?></span>
                    </div>
                <?php endif; ?>

                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Elenco Utenti</h2>
                        <div class="actions">
                            <a href="add-user.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Aggiungi Utente
                            </a>
                        </div>
                    </div>
                    <div class="content-box-body">
                        <div class="search-filter">
                            <form action="users.php" method="get" class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" placeholder="Cerca per nome o email..." value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" title="Cerca">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                            <?php if (!empty($search)): ?>
                                <a href="users.php" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Cancella Filtri
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Data Registrazione</th>
                                        <th>Ultimo Aggiornamento</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <h3>Nessun utente trovato</h3>
                                                    <?php if (!empty($search)): ?>
                                                        <p>Nessun risultato per "<?php echo htmlspecialchars($search); ?>". Prova con un'altra ricerca.</p>
                                                        <a href="users.php" class="btn btn-primary">
                                                            <i class="fas fa-arrow-left"></i> Torna a tutti gli utenti
                                                        </a>
                                                    <?php else: ?>
                                                        <p>Non ci sono ancora utenti registrati.</p>
                                                        <a href="add-user.php" class="btn btn-primary">
                                                            <i class="fas fa-plus"></i> Aggiungi il primo utente
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></td>
                                                <td class="actions">
                                                    <a href="view-user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-icon" title="Visualizza">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-icon" title="Modifica">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-icon" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <div class="pagination-item">
                                        <a href="users.php?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);

                                if ($start_page > 1) {
                                    echo '<div class="pagination-item">';
                                    echo '<a href="users.php?page=1' . (!empty($search) ? '&search=' . urlencode($search) : '') . '" class="pagination-link">1</a>';
                                    echo '</div>';
                                    if ($start_page > 2) {
                                        echo '<div class="pagination-item">...</div>';
                                    }
                                }

                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    echo '<div class="pagination-item">';
                                    echo '<a href="users.php?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '') . '" class="pagination-link' . ($i == $page ? ' active' : '') . '">' . $i . '</a>';
                                    echo '</div>';
                                }

                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<div class="pagination-item">...</div>';
                                    }
                                    echo '<div class="pagination-item">';
                                    echo '<a href="users.php?page=' . $total_pages . (!empty($search) ? '&search=' . urlencode($search) : '') . '" class="pagination-link">' . $total_pages . '</a>';
                                    echo '</div>';
                                }
                                ?>

                                <?php if ($page < $total_pages): ?>
                                    <div class="pagination-item">
                                        <a href="users.php?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="pagination-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
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
        });
    </script>
</body>
</html>

