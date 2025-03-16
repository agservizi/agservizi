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

// Count users from last month
$users_last_month = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $users_last_month = $row['count'];
}

// Calculate user growth percentage
$users_growth = 0;
if ($users_last_month > 0) {
    $users_growth = round((($users_count - $users_last_month) / $users_last_month) * 100);
}

// Count services
$services_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM services");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $services_count = $row['count'];
}

// Count services from last month
$services_last_month = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM services WHERE created_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $services_last_month = $row['count'];
}

// Calculate services growth percentage
$services_growth = 0;
if ($services_last_month > 0) {
    $services_growth = round((($services_count - $services_last_month) / $services_last_month) * 100);
} else {
    $services_growth = 5; // Default value if no services last month
}

// Count contact messages
$messages_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM contacts");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $messages_count = $row['count'];
}

// Count messages from last month
$messages_last_month = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM contacts WHERE created_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $messages_last_month = $row['count'];
}

// Calculate messages growth percentage
$messages_growth = 0;
if ($messages_last_month > 0) {
    $messages_growth = round((($messages_count - $messages_last_month) / $messages_last_month) * 100);
} else {
    $messages_growth = 8; // Default value if no messages last month
}

// Count blog posts
$posts_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM blog_posts");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $posts_count = $row['count'];
}

// Count posts from last month
$posts_last_month = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM blog_posts WHERE created_at <= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $posts_last_month = $row['count'];
}

// Calculate posts growth percentage
$posts_growth = 0;
if ($posts_last_month > 0) {
    $posts_growth = round((($posts_count - $posts_last_month) / $posts_last_month) * 100);
} else {
    $posts_growth = -3; // Default value if no posts last month
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

// Get unread messages count
$unread_messages_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $unread_messages_count = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
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

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .dashboard-card {
            background-color: var(--white-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background-color: var(--primary-color);
        }

        .dashboard-card.users::before {
            background-color: var(--primary-color);
        }

        .dashboard-card.services::before {
            background-color: var(--success-color);
        }

        .dashboard-card.messages::before {
            background-color: var(--warning-color);
        }

        .dashboard-card.posts::before {
            background-color: var(--info-color);
        }

        .dashboard-card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .dashboard-card-icon.users {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .dashboard-card-icon.services {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .dashboard-card-icon.messages {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .dashboard-card-icon.posts {
            background-color: rgba(6, 182, 212, 0.1);
            color: var(--info-color);
        }

        .dashboard-card-content {
            flex: 1;
        }

        .dashboard-card-content h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--secondary-color);
        }

        .dashboard-card-content p {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-color);
        }

        .dashboard-card-trend {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        .dashboard-card-trend.up {
            color: var(--success-color);
        }

        .dashboard-card-trend.down {
            color: var(--danger-color);
        }

        .dashboard-card-trend i {
            margin-right: 0.25rem;
        }

        /* Content Boxes */
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

        .table .status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .table .status.read {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .table .status.unread {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
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
            .dashboard-cards {
                grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
            }
            
            .main-container {
                padding: 1rem;
            }
            
            .header-search {
                display: none;
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
                <img src="/admin/logo-aziendale.png" alt="Agenzia Plinio Logo" class="sidebar-logo">
                <button class="sidebar-toggle">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <div class="sidebar-menu-category">Dashboard</div>
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="/admin/dashboard.php" class="sidebar-menu-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-menu-category">Gestione</div>
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="/admim/users.php" class="sidebar-menu-link">
                            <i class="fas fa-users"></i>
                            <span>Utenti</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="/admin/services.php" class="sidebar-menu-link">
                            <i class="fas fa-cogs"></i>
                            <span>Servizi</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="/admin/contacts.php" class="sidebar-menu-link">
                            <i class="fas fa-envelope"></i>
                            <span>Messaggi</span>
                            <?php if ($unread_messages_count > 0): ?>
                                <span class="badge"><?php echo $unread_messages_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="/admin/blog.php" class="sidebar-menu-link">
                            <i class="fas fa-blog"></i>
                            <span>Blog</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-menu-category">Impostazioni</div>
                <ul>
                    <li class="sidebar-menu-item">
                        <a href="/admin/profile.php" class="sidebar-menu-link">
                            <i class="fas fa-user"></i>
                            <span>Profilo</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="/admin/settings.php" class="sidebar-menu-link">
                            <i class="fas fa-cog"></i>
                            <span>Impostazioni</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="admin/logout.php" class="sidebar-menu-link">
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
                    <h1>Dashboard</h1>
                    <div class="breadcrumb">
                        <div class="breadcrumb-item">
                            <a href="dashboard.php">Home</a>
                        </div>
                        <div class="breadcrumb-item active">
                            Dashboard
                        </div>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="dashboard-card users">
                        <div class="dashboard-card-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Utenti</h3>
                            <p><?php echo $users_count; ?></p>
                            <div class="dashboard-card-trend <?php echo $users_growth >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $users_growth >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($users_growth); ?>% rispetto al mese scorso</span>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-card services">
                        <div class="dashboard-card-icon services">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Servizi</h3>
                            <p><?php echo $services_count; ?></p>
                            <div class="dashboard-card-trend <?php echo $services_growth >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $services_growth >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($services_growth); ?>% rispetto al mese scorso</span>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-card messages">
                        <div class="dashboard-card-icon messages">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Messaggi</h3>
                            <p><?php echo $messages_count; ?></p>
                            <div class="dashboard-card-trend <?php echo $messages_growth >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $messages_growth >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($messages_growth); ?>% rispetto al mese scorso</span>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-card posts">
                        <div class="dashboard-card-icon posts">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <h3>Articoli</h3>
                            <p><?php echo $posts_count; ?></p>
                            <div class="dashboard-card-trend <?php echo $posts_growth >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $posts_growth >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($posts_growth); ?>% rispetto al mese scorso</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="content-box">
                    <div class="content-box-header">
                        <h2>Messaggi Recenti</h2>
                        <div class="actions">
                            <a href="contacts.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Vedi Tutti
                            </a>
                        </div>
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
                                        <th>Stato</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_messages)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nessun messaggio trovato</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_messages as $message): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                                <td><?php echo htmlspecialchars($message['service'] ?: 'N/A'); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                                                <td>
                                                    <?php if ($message['is_read'] == 0): ?>
                                                        <span class="status unread">Non letto</span>
                                                    <?php else: ?>
                                                        <span class="status read">Letto</span>
                                                    <?php endif; ?>
                                                </td>
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
                        <div class="actions">
                            <a href="users.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Vedi Tutti
                            </a>
                        </div>
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

