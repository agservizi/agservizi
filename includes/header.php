<header class="header">
    <div class="container">
        <div class="logo">
            <a href="index.html">
                <img src="images/logo.svg" alt="Agenzia Plinio Logo">
            </a>
        </div>
        <nav class="main-nav">
            <button class="mobile-menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <ul class="nav-list">
                <li><a href="index.html" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'index.html') ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="chi-siamo.html" <?php echo (basename($_SERVER['PHP_SELF']) == 'chi-siamo.php' || basename($_SERVER['PHP_SELF']) == 'chi-siamo.html') ? 'class="active"' : ''; ?>>Chi Siamo</a></li>
                <li class="dropdown">
                    <a href="servizi.html" <?php echo (strpos($_SERVER['PHP_SELF'], 'servizi') !== false) ? 'class="active"' : ''; ?>>Servizi</a>
                    <ul class="dropdown-menu">
                        <li><a href="servizi-pagamenti.html">Pagamenti</a></li>
                        <li><a href="servizi-spedizioni.html">Spedizioni</a></li>
                        <li><a href="servizi-telefonia.html">Telefonia</a></li>
                        <li><a href="servizi-energia.html">Energia</a></li>
                        <li><a href="servizi-spid.html">SPID</a></li>
                        <li><a href="servizi-pec.html">PEC</a></li>
                        <li><a href="servizi-firma-digitale.html">Firma Digitale</a></li>
                        <li><a href="servizi-visure.html">Visure</a></li>
                    </ul>
                </li>
                <li><a href="contatti.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'contatti.php') ? 'class="active"' : ''; ?>>Contatti</a></li>
                <li><a href="blog.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php' || basename($_SERVER['PHP_SELF']) == 'blog-single.php') ? 'class="active"' : ''; ?>>Blog</a></li>
                <li><a href="login.php" class="btn btn-outline" <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'class="btn btn-outline active"' : 'class="btn btn-outline"'; ?>>Area Clienti</a></li>
            </ul>
        </nav>
    </div>
</header>

