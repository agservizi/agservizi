<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AG Servizi Via Plinio 72 - <?php echo isset($page_title) ? $page_title : 'Soluzioni Semplici per la Tua Vita Digitale'; ?></title>
    <meta name="description" content="Agenzia di servizi multifunzionale: pagamenti, spedizioni, telefonia, energia, SPID, PEC, Firma Digitale, Visure e molto altro.">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- AOS - Animate On Scroll Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">
                        <img src="/assets/images/logo.png" alt="AG Servizi Via Plinio 72">
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                            <a href="/">Home</a>
                        </li>
                        <li class="<?php echo $current_page == 'chi-siamo.php' ? 'active' : ''; ?>">
                            <a href="/chi-siamo.php">Chi Siamo</a>
                        </li>
                        <li class="<?php echo strpos($current_page, 'servizi') !== false ? 'active' : ''; ?> has-dropdown">
                            <a href="/servizi.php">Servizi</a>
                            <ul class="dropdown">
                                <li><a href="/servizi/pagamenti.php">Pagamenti</a></li>
                                <li><a href="/servizi/spedizioni.php">Spedizioni</a></li>
                                <li><a href="/servizi/telefonia.php">Telefonia</a></li>
                                <li><a href="/servizi/energia.php">Energia</a></li>
                                <li><a href="/servizi/spid.php">SPID</a></li>
                                <li><a href="/servizi/pec.php">PEC</a></li>
                                <li><a href="/servizi/firma-digitale.php">Firma Digitale</a></li>
                                <li><a href="/servizi/visure.php">Visure</a></li>
                            </ul>
                        </li>
                        <li class="<?php echo $current_page == 'blog.php' ? 'active' : ''; ?>">
                            <a href="/blog.php">Blog</a>
                        </li>
                        <li class="<?php echo $current_page == 'contatti.php' ? 'active' : ''; ?>">
                            <a href="/contatti.php">Contatti</a>
                        </li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/area-clienti/dashboard.php" class="btn btn-outline btn-sm">Area Clienti</a>
                    <?php else: ?>
                        <a href="/login.php" class="btn btn-outline btn-sm">Accedi</a>
                    <?php endif; ?>
                </div>
                
                <button class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="container">
            <ul class="mobile-nav-list">
                <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <a href="/">Home</a>
                </li>
                <li class="<?php echo $current_page == 'chi-siamo.php' ? 'active' : ''; ?>">
                    <a href="/chi-siamo.php">Chi Siamo</a>
                </li>
                <li class="<?php echo strpos($current_page, 'servizi') !== false ? 'active' : ''; ?>">
                    <a href="/servizi.php">Servizi</a>
                    <ul class="mobile-dropdown">
                        <li><a href="/servizi/pagamenti.php">Pagamenti</a></li>
                        <li><a href="/servizi/spedizioni.php">Spedizioni</a></li>
                        <li><a href="/servizi/telefonia.php">Telefonia</a></li>
                        <li><a href="/servizi/energia.php">Energia</a></li>
                        <li><a href="/servizi/spid.php">SPID</a></li>
                        <li><a href="/servizi/pec.php">PEC</a></li>
                        <li><a href="/servizi/firma-digitale.php">Firma Digitale</a></li>
                        <li><a href="/servizi/visure.php">Visure</a></li>
                    </ul>
                </li>
                <li class="<?php echo $current_page == 'blog.php' ? 'active' : ''; ?>">
                    <a href="/blog.php">Blog</a>
                </li>
                <li class="<?php echo $current_page == 'contatti.php' ? 'active' : ''; ?>">
                    <a href="/contatti.php">Contatti</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>
                        <a href="/area-clienti/dashboard.php">Area Clienti</a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="/login.php">Accedi</a>
                    </li>
                    <li>
                        <a href="/registrazione.php">Registrati</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <main>

