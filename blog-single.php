<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Ottieni lo slug dell'articolo dall'URL
$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

// Query per ottenere l'articolo
$query = "SELECT bp.*, u.name as author_name 
          FROM blog_posts bp 
          LEFT JOIN users u ON bp.author_id = u.id 
          WHERE bp.slug = ? AND bp.status = 'published'";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: blog.php');
    exit;
}

$post = $result->fetch_assoc();

// Ottieni articoli correlati (stessa categoria o tag, da implementare in futuro)
// Per ora mostriamo semplicemente altri articoli recenti
$recent_posts = get_recent_posts($conn, 4);

// Incrementa il contatore di visualizzazioni
$update_query = "UPDATE blog_posts SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("i", $post['id']);
$stmt->execute();

$page_title = $post['title'] . " - Agenzia Plinio";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <?php if (!empty($post['featured_image'])): ?>
    <meta property="og:image" content="<?php echo "https://$_SERVER[HTTP_HOST]/uploads/blog/" . htmlspecialchars($post['featured_image']); ?>">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <?php if (!empty($post['featured_image'])): ?>
    <meta property="twitter:image" content="<?php echo "https://$_SERVER[HTTP_HOST]/uploads/blog/" . htmlspecialchars($post['featured_image']); ?>">
    <?php endif; ?>
    
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
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li class="active"><?php echo htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Blog Single Content -->
    <section class="blog-single-section">
        <div class="container">
            <div class="blog-container">
                <div class="blog-main">
                    <article class="blog-single" data-aos="fade-up">
                        <?php if (!empty($post['featured_image'])): ?>
                        <div class="blog-featured-image">
                            <img src="uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <?php endif; ?>
                        
                        <div class="blog-meta">
                            <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                            <span class="blog-author"><i class="far fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                            <span class="blog-views"><i class="far fa-eye"></i> <?php echo $post['views']; ?> visualizzazioni</span>
                        </div>
                        
                        <div class="blog-content">
                            <?php echo $post['content']; ?>
                        </div>
                        
                        <div class="blog-share">
                            <span>Condividi:</span>
                            <div class="social-share">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" aria-label="Condividi su Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" aria-label="Condividi su Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" aria-label="Condividi su LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($post['title'] . " - https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" aria-label="Condividi su WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </article>
                    
                    <!-- Post Navigation -->
                    <div class="post-navigation" data-aos="fade-up">
                        <?php
                        // Query per ottenere il post precedente
                        $prev_query = "SELECT id, title, slug FROM blog_posts WHERE created_at < ? AND status = 'published' ORDER BY created_at DESC LIMIT 1";
                        $stmt = $conn->prepare($prev_query);
                        $stmt->bind_param("s", $post['created_at']);
                        $stmt->execute();
                        $prev_result = $stmt->get_result();
                        $prev_post = $prev_result->fetch_assoc();
                        
                        // Query per ottenere il post successivo
                        $next_query = "SELECT id, title, slug FROM blog_posts WHERE created_at > ? AND status = 'published' ORDER BY created_at ASC LIMIT 1";
                        $stmt = $conn->prepare($next_query);
                        $stmt->bind_param("s", $post['created_at']);
                        $stmt->execute();
                        $next_result = $stmt->get_result();
                        $next_post = $next_result->fetch_assoc();
                        ?>
                        
                        <div class="post-nav prev">
                            <?php if ($prev_post): ?>
                            <a href="blog-single.php?slug=<?php echo htmlspecialchars($prev_post['slug']); ?>">
                                <span><i class="fas fa-chevron-left"></i> Articolo Precedente</span>
                                <h4><?php echo htmlspecialchars($prev_post['title']); ?></h4>
                            </a>
                            <?php else: ?>
                            <div class="nav-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-nav next">
                            <?php if ($next_post): ?>
                            <a href="blog-single.php?slug=<?php echo htmlspecialchars($next_post['slug']); ?>">
                                <span>Articolo Successivo <i class="fas fa-chevron-right"></i></span>
                                <h4><?php echo htmlspecialchars($next_post['title']); ?></h4>
                            </a>
                            <?php else: ?>
                            <div class="nav-placeholder"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Related Posts -->
                    <div class="related-posts" data-aos="fade-up">
                        <h3>Articoli Correlati</h3>
                        <div class="related-posts-grid">
                            <?php 
                            $count = 0;
                            foreach ($recent_posts as $recent): 
                                // Skip the current post
                                if ($recent['id'] == $post['id']) continue;
                                
                                // Only show 3 related posts
                                if ($count >= 3) break;
                                $count++;
                            ?>
                            <article class="related-post">
                                <a href="blog-single.php?slug=<?php echo htmlspecialchars($recent['slug']); ?>">
                                    <div class="related-post-image">
                                        <?php if (!empty($recent['featured_image'])): ?>
                                        <img src="uploads/blog/<?php echo htmlspecialchars($recent['featured_image']); ?>" alt="<?php echo htmlspecialchars($recent['title']); ?>">
                                        <?php else: ?>
                                        <img src="images/blog/default-post.jpg" alt="<?php echo htmlspecialchars($recent['title']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <h4><?php echo htmlspecialchars($recent['title']); ?></h4>
                                </a>
                                <span class="date"><?php echo date('d/m/Y', strtotime($recent['created_at'])); ?></span>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="blog-sidebar">
                    <!-- Recent Posts -->
                    <div class="sidebar-widget" data-aos="fade-up">
                        <h3>Articoli Recenti</h3>
                        <ul class="recent-posts">
                            <?php foreach ($recent_posts as $recent): ?>
                            <?php if ($recent['id'] == $post['id']) continue; ?>
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

