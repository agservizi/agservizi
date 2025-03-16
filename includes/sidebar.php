<div class="sidebar-header p-3">
    <h5 class="mb-0">Menu</h5>
</div>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'dashboard' ? 'active' : ''; ?>" href="index.php?module=dashboard">
            <i class="bi bi-grid"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'pos' ? 'active' : ''; ?>" href="index.php?module=pos">
            <i class="bi bi-cart"></i> POS
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'listini' ? 'active' : ''; ?>" href="index.php?module=listini">
            <i class="bi bi-file-text"></i> Listini Operatori
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'compensi' ? 'active' : ''; ?>" href="index.php?module=compensi">
            <i class="bi bi-currency-euro"></i> Compensi Operatore
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'target' ? 'active' : ''; ?>" href="index.php?module=target">
            <i class="bi bi-bullseye"></i> Gestione Target
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'fatturazione' ? 'active' : ''; ?>" href="index.php?module=fatturazione">
            <i class="bi bi-receipt"></i> Fatturazione
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'cassa' ? 'active' : ''; ?>" href="index.php?module=cassa">
            <i class="bi bi-cash-register"></i> Gestione Cassa
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'imei' ? 'active' : ''; ?>" href="index.php?module=imei">
            <i class="bi bi-upc-scan"></i> Gestione IMEI/ICCID
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'personale' ? 'active' : ''; ?>" href="index.php?module=personale">
            <i class="bi bi-people"></i> Gestione Personale
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'messaggi' ? 'active' : ''; ?>" href="index.php?module=messaggi">
            <i class="bi bi-chat-dots"></i> Invio Messaggi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-white <?php echo $module == 'assistenza' ? 'active' : ''; ?>" href="index.php?module=assistenza">
            <i class="bi bi-tools"></i> Assistenza e Usato
        </a>
    </li>
</ul>

