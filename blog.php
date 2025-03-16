<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Gestione paginazione
$posts_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Gestione ricerca
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$search_condition = '';
$search_params = [];

if (!empty($search)) {
    $search_condition = "AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ?)";
    $search_params = ["%$search%", "%$search%", "%$search%"];
}

// Query per contare il totale degli articoli
$count_query = "SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published' $search_condition";
$stmt = $conn->prepare($count_query);

if (!empty($search_params)) {
    $types = str_repeat('s', count($search_params));
    $stmt->bind_param($types, ...$search_params);
}

$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_posts = $total_row['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Query per ottenere gli articoli
$query = "SELECT bp.*, u.name as author_name 
          FROM blog_posts bp 
          LEFT JOIN users u ON bp.author_id = u.id 
          WHERE bp.status = 'published' $search_condition 
          ORDER BY bp.created_at DESC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

if (!empty($search_params)) {
    $types = str_repeat('s', count($search_params)) . 'ii';
    $params = array_merge($search_params, [$posts_per_page, $offset]);
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $posts_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$posts = [];

while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

// Ottieni articoli recenti per la sidebar
$recent_posts = get_recent_posts($conn, 4);

$page_title = "Blog - Agenzia Plinio";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header -->
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
                    <li><a href="index.html">Home</a></li>
                    <li><a href="chi-siamo.html">Chi Siamo</a></li>
                    <li class="dropdown">
                        <a href="servizi.html">Servizi</a>
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
                    <li><a href="contatti.html">Contatti</a></li>
                    <li><a href="blog.php" class="active">Blog</a></li>
                    <li><a href="login.php" class="btn btn-outline">Area Clienti</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Blog</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Blog</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Blog Content -->
    <section class="blog-section">
        <div class="container">
            <div class="blog-container">
                <div class="blog-main">
                    <!-- Search Form -->
                    <div class="blog-search" data-aos="fade-up">
                        <form action="blog.php" method="GET">
                            <input type="text" name="search" placeholder="Cerca nel blog..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <?php if (!empty($search)): ?>
                    <div class="search-results" data-aos="fade-up">
                        <p>Risultati per: <strong><?php echo htmlspecialchars($search); ?></strong> (<?php echo $total_posts; ?> articoli trovati)</p>
                        <a href="blog.php" class="btn-link">Cancella ricerca</a>
                    </div>
                    <?php endif; ?>

                    <?php if (empty($posts)): ?>
                    <div class="no-posts" data-aos="fade-up">
                        <div class="no-posts-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h2>Nessun articolo trovato</h2>
                        <?php if (!empty($search)): ?>
                            <p>La tua ricerca non ha prodotto risultati. Prova con termini diversi.</p>
                        <?php else: ?>
                            <p>Non ci sono ancora articoli pubblicati. Torna presto per nuovi contenuti!</p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="blog-grid">
                        <?php foreach ($posts as $post): ?>
                        <article class="blog-card" data-aos="fade-up">
                            <div class="blog-image">
                                <?php if (!empty($post['featured_image'])): ?>
                                <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php else: ?>
                                <img src="images/blog/default-post.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                    <span class="blog-author"><i class="far fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                <a href="blog-single.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="btn-link">Leggi l'articolo</a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination" data-aos="fade-up">
                        <ul>
                            <?php if ($page > 1): ?>
                            <li><a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-left"></i></a></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li><a href="?page=1' . (!empty($search) ? '&search=' . urlencode($search) : '') . '">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="pagination-dots">...</li>';
                                }
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                echo '<li' . ($i == $page ? ' class="active"' : '') . '><a href="?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '') . '">' . $i . '</a></li>';
                            }
                            
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="pagination-dots">...</li>';
                                }
                                echo '<li><a href="?page=' . $total_pages . (!empty($search) ? '&search=' . urlencode($search) : '') . '">' . $total_pages . '</a></li>';
                            }
                            ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li><a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-right"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="blog-sidebar">
                    <!-- Recent Posts -->
                    <div class="sidebar-widget" data-aos="fade-up">
                        <h3>Articoli Recenti</h3>
                        <ul class="recent-posts">
                            <?php foreach ($recent_posts as $recent): ?>
                            <li>
                                <a href="blog-single.php?slug=<?php echo htmlspecialchars($recent['slug']); ?>">
                                    <div class="recent-post-image">
                                        <?php if (!empty($recent['featured_image'])): ?>
                                        <img src="uploads/blog/<?php echo htmlspecialchars($recent['featured_image']); ?>" alt="<?php echo htmlspecialchars($recent['title']); ?>">
                                        <?php else: ?>
                                        <img src="images/blog/default-post.jpg" alt="<?php echo htmlspecialchars($recent['title']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="recent-post-info">
                                        <h4><?php echo htmlspecialchars($recent['title']); ?></h4>
                                        <span class="date"><?php echo date('d/m/Y', strtotime($recent['created_at'])); ?></span>
                                    </div>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- CTA Widget -->
                    <div class="sidebar-widget cta-widget" data-aos="fade-up">
                        <h3>Hai bisogno di assistenza?</h3>
                        <p>Contattaci per una consulenza gratuita sui nostri servizi</p>
                        <a href="contatti.html" class="btn btn-primary">Contattaci Ora</a>
                    </div>

                    <!-- Social Widget -->
                    <div class="sidebar-widget social-widget" data-aos="fade-up">
                        <h3>Seguici sui Social</h3>
                        <div class="social-links">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="container">
            <div class="cta-content" data-aos="zoom-in">
                <h2>Hai bisogno di assistenza?</h2>
                <p>Contattaci per una consulenza gratuita sui nostri servizi</p>
                <a href="contatti.html" class="btn btn-light">Contattaci Ora</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">
                        <img src="images/logo-white.svg" alt="Agenzia Plinio Logo">
                    </div>
                    <p>Agenzia Plinio - Soluzioni semplici per la tua vita digitale. Tutti i servizi di cui hai bisogno in un unico posto.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Link Rapidi</h3>
                    <ul class="footer-links">
                        <li><a href="index.html">Home</a></li>
                        <li><a href="chi-siamo.html">Chi Siamo</a></li>
                        <li><a href="servizi.html">Servizi</a></li>
                        <li><a href="contatti.html">Contatti</a></li>
                        <li><a href="blog.php">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Servizi</h3>
                    <ul class="footer-links">
                        <li><a href="servizi-pagamenti.html">Pagamenti</a></li>
                        <li><a href="servizi-spedizioni.html">Spedizioni</a></li>
                        <li><a href="servizi-telefonia.html">Telefonia</a></li>
                        <li><a href="servizi-energia.html">Energia</a></li>
                        <li><a href="servizi-spid.html">SPID</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contatti</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> Via Plinio 72, Milano</li>
                        <li><i class="fas fa-phone"></i> +39 02 1234567</li>
                        <li><i class="fas fa-envelope"></i> info@agenziaplinio.it</li>
                        <li><i class="fas fa-clock"></i> Lun-Ven: 9:00-18:00, Sab: 9:00-12:30</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 Agenzia Plinio. Tutti i diritti riservati.</p>
                <div class="footer-legal">
                    <a href="privacy-policy.html">Privacy Policy</a>
                    <a href="termini-condizioni.html">Termini e Condizioni</a>
                    <a href="cookie-policy.html">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

