<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Rimuoviamo qualsiasi controllo di autenticazione da questa pagina pubblica
// La pagina blog-single.php deve essere accessibile a tutti i visitatori

// Verifica che l'ID dell'articolo sia presente
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: blog.php');
    exit;
}

$post_id = intval($_GET['id']);

// Ottieni i dettagli dell'articolo
$stmt = $conn->prepare("SELECT b.*, u.username as author_name 
                        FROM blog_posts b 
                        LEFT JOIN users u ON b.author_id = u.id 
                        WHERE b.id = ? AND b.status = 'published'");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

// Se l'articolo non esiste o non è pubblicato, reindirizza al blog
if ($result->num_rows === 0) {
    header('Location: blog.php');
    exit;
}

$post = $result->fetch_assoc();

// Incrementa il contatore visualizzazioni
$update_stmt = $conn->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
$update_stmt->bind_param("i", $post_id);
$update_stmt->execute();

// Articoli recenti per la sidebar
$recent_posts_query = "SELECT * FROM blog_posts WHERE status = 'published' AND id != ? ORDER BY created_at DESC LIMIT 5";
$recent_stmt = $conn->prepare($recent_posts_query);
$recent_stmt->bind_param("i", $post_id);
$recent_stmt->execute();
$recent_result = $recent_stmt->get_result();
$recent_posts = [];
if ($recent_result && $recent_result->num_rows > 0) {
    while ($row = $recent_result->fetch_assoc()) {
        $recent_posts[] = $row;
    }
}

// Articoli correlati
$related_query = "SELECT * FROM blog_posts WHERE status = 'published' AND id != ? ORDER BY RAND() LIMIT 3";
$related_stmt = $conn->prepare($related_query);
$related_stmt->bind_param("i", $post_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_posts = [];
if ($related_result && $related_result->num_rows > 0) {
    while ($row = $related_result->fetch_assoc()) {
        $related_posts[] = $row;
    }
}

// Articolo precedente e successivo
$prev_post = null;
$next_post = null;

$prev_query = "SELECT id, title, slug FROM blog_posts WHERE id < ? AND status = 'published' ORDER BY id DESC LIMIT 1";
$prev_stmt = $conn->prepare($prev_query);
$prev_stmt->bind_param("i", $post_id);
$prev_stmt->execute();
$prev_result = $prev_stmt->get_result();
if ($prev_result && $prev_result->num_rows > 0) {
    $prev_post = $prev_result->fetch_assoc();
}

$next_query = "SELECT id, title, slug FROM blog_posts WHERE id > ? AND status = 'published' ORDER BY id ASC LIMIT 1";
$next_stmt = $conn->prepare($next_query);
$next_stmt->bind_param("i", $post_id);
$next_stmt->execute();
$next_result = $next_stmt->get_result();
if ($next_result && $next_result->num_rows > 0) {
    $next_post = $next_result->fetch_assoc();
}

// Titolo della pagina
$page_title = htmlspecialchars($post['title']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - AG Servizi</title>
    
    <!-- Meta tags per SEO e condivisione social -->
    <meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($post['excerpt']), 0, 160)); ?>">
    <meta property="og:title" content="<?php echo $page_title; ?> - AG Servizi">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr(strip_tags($post['excerpt']), 0, 160)); ?>">
    <?php if (!empty($post['featured_image']) && file_exists("uploads/blog/" . $post['featured_image'])): ?>
        <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/blog/' . $post['featured_image']; ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="article">
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Blog Single Content -->
    <section class="blog-single">
        <div class="container">
            <div class="blog-container">
                <div class="blog-main">
                    <article class="blog-post" data-aos="fade-up">
                        <header class="post-header">
                            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                            <div class="post-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                <span><i class="fas fa-eye"></i> <?php echo $post['views']; ?> visualizzazioni</span>
                            </div>
                        </header>
                        
                        <?php if (!empty($post['featured_image']) && file_exists("uploads/blog/" . $post['featured_image'])): ?>
                            <div class="post-featured-image">
                                <img src="uploads/blog/<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <?php echo $post['content']; ?>
                        </div>
                        
                        <div class="post-share">
                            <span>Condividi:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </article>
                    
                    <!-- Post Navigation -->
                    <div class="post-navigation" data-aos="fade-up">
                        <?php if ($prev_post): ?>
                            <a href="blog-single.php?id=<?php echo $prev_post['id']; ?>&slug=<?php echo $prev_post['slug']; ?>" class="prev-post">
                                <i class="fas fa-chevron-left"></i>
                                <span>Articolo precedente</span>
                                <h4><?php echo htmlspecialchars($prev_post['title']); ?></h4>
                            </a>
                        <?php else: ?>
                            <div class="prev-post empty"></div>
                        <?php endif; ?>
                        
                        <?php if ($next_post): ?>
                            <a href="blog-single.php?id=<?php echo $next_post['id']; ?>&slug=<?php echo $next_post['slug']; ?>" class="next-post">
                                <span>Articolo successivo</span>
                                <h4><?php echo htmlspecialchars($next_post['title']); ?></h4>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <div class="next-post empty"></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Related Posts -->
                    <?php if (!empty($related_posts)): ?>
                        <div class="related-posts" data-aos="fade-up">
                            <h3>Articoli correlati</h3>
                            <div class="related-posts-grid">
                                <?php foreach ($related_posts as $related): ?>
                                    <article class="related-post">
                                        <div class="related-post-image">
                                            <?php if (!empty($related['featured_image']) && file_exists("uploads/blog/" . $related['featured_image'])): ?>
                                                <img src="uploads/blog/<?php echo $related['featured_image']; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                            <?php else: ?>
                                                <img src="img/placeholder-blog.jpg" alt="Placeholder">
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-post-content">
                                            <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                            <span class="post-date"><?php echo date('d/m/Y', strtotime($related['created_at'])); ?></span>
                                            <a href="blog-single.php?id=<?php echo $related['id']; ?>&slug=<?php echo $related['slug']; ?>" class="read-more">Leggi di più</a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <aside class="blog-sidebar" data-aos="fade-left">
                    <!-- Recent Posts -->
                    <div class="sidebar-widget">
                        <h3>Articoli Recenti</h3>
                        <ul class="recent-posts">
                            <?php foreach ($recent_posts as $recent): ?>
                                <li>
                                    <a href="blog-single.php?id=<?php echo $recent['id']; ?>&slug=<?php echo $recent['slug']; ?>">
                                        <?php echo htmlspecialchars($recent['title']); ?>
                                    </a>
                                    <span class="post-date"><?php echo date('d/m/Y', strtotime($recent['created_at'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- CTA Widget -->
                    <div class="sidebar-widget cta-widget">
                        <h3>Hai bisogno di aiuto?</h3>
                        <p>Contattaci per una consulenza gratuita sui nostri servizi.</p>
                        <a href="contact.php" class="btn btn-primary">Contattaci</a>
                    </div>
                    
                    <!-- Social Widget -->
                    <div class="sidebar-widget social-widget">
                        <h3>Seguici</h3>
                        <div class="social-icons">
                            <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

