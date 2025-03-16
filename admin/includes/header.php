<header class="header">
    <div class="header-left">
        <button id="toggle-sidebar" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h2>Dashboard</h2>
    </div>
    <div class="header-right">
        <ul class="header-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="../index.php" target="_blank">Visualizza Sito</a></li>
            <!-- altri link -->
        </ul>
        <div class="user-dropdown">
            <button class="dropdown-toggle">
                <span class="user-name"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="profile.php"><i class="fas fa-user"></i> Profilo</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Impostazioni</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</header>

