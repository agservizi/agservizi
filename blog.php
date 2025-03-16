<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Rimuoviamo qualsiasi controllo di autenticazione da questa pagina pubblica
// La pagina blog.php deve essere accessibile a tutti i visitatori

// Impostazioni di paginazione
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 6;
$offset = ($current_page - 1) * $items_per_page;

// Ricerca
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Costruzione della query
$query = "SELECT b.*, u.username as author_name 
          FROM blog_posts b 
          LEFT JOIN users u ON b.author_id = u.id 
          WHERE b.status = 'published'";
$count_query = "SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published'";
$params = [];
$param_types = "";

if (!empty($search_query)) {
    $search_term = "%$search_query%";
    $query .= " AND (b.title LIKE ? OR b.content LIKE ?)";
    $count_query .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= "ss";
}

// Ordine e limite
$query .= " ORDER BY b.created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $items_per_page;
$param_types .= "ii";

// Esecuzione query per il conteggio totale
$count_stmt = $conn->prepare($count_query);
if (!empty($param_types) && !empty($params)) {
    // Rimuoviamo gli ultimi due parametri (offset e limit) per il conteggio
    $count_param_types = substr($param_types, 0, -2);
    $count_params = array_slice($params, 0, -2);
    if (!empty($count_param_types)) {
        $count_stmt->bind_param($count_param_types, ...$count_params);
    }
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_items = $count_row['total'];

// Calcolo delle pagine totali
$total_pages = ceil($total_items / $items_per_page);

// Esecuzione query per i dati
$stmt = $conn->prepare($query);
if (!empty($param_types) && !empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Articoli recenti per la sidebar
$recent_posts_query = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 5";
$recent_posts_result = $conn->query($recent_posts_query);
$recent_posts = [];
if ($recent_posts_result && $recent_posts_result->num_rows > 0) {
    while ($row = $recent_posts_result->fetch_assoc()) {
        $recent_posts[] = $row;
    }
}

// Titolo della pagina
$page_title = "Blog";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - AG Servizi</title>
    <meta name="description" content="Leggi gli ultimi articoli e novità dal nostro blog.">
    
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
    
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <h1 data-aos="fade-up"><?php echo $page_title; ?></h1>
            <p data-aos="fade-up" data-aos-delay="100">Scopri le ultime novità, consigli e approfondimenti</p>
        </div>
    </section>
    
    <!-- Blog Section -->
    <section class="blog-section">
        <div class="container">
            <div class="blog-container">
                <div class="blog-main">
                    <!-- Search Form -->
                    <div class="blog-search" data-aos="fade-up">
                        <form action="" method="GET">
                            <input type="text" name="search" placeholder="Cerca nel blog..." value="<?php echo htmlspecialchars($search_query); ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    
                    <?php if ($result->num_rows > 0): ?>
                        <div class="blog-grid">
                            <?php while ($post = $result->fetch_assoc()): ?>
                                <article class="blog-card" data-aos="fade-up">
                                    <div class="blog-card-image">
                                        <?php if (!empty($post['featured_image']) && file_exists("uploads/blog/" . $post['featured_image'])): ?>
                                            <img src="uploads/blog/<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                        <?php else: ?>
                                            <img src="img/placeholder-blog.jpg" alt="Placeholder">
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-card-content">
                                        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                                        <div class="blog-meta">
                                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                                            <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                        </div>
                                        <p><?php echo htmlspecialchars(substr(strip_tags($post['excerpt']), 0, 150)) . '...'; ?></p>
                                        <a href="blog-single.php?id=<?php echo $post['id']; ?>&slug=<?php echo $post['slug']; ?>" class="btn btn-primary">Leggi di più</a>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination" data-aos="fade-up">
                                <?php if ($current_page > 1): ?>
                                    <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="pagination-prev">
                                        <i class="fas fa-chevron-left"></i> Precedente
                                    </a>
                                <?php endif; ?>
                                
                                <div class="pagination-numbers">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="<?php echo $i === $current_page ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>
                                
                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="pagination-next">
                                        Successiva <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="blog-empty" data-aos="fade-up">
                            <i class="fas fa-newspaper"></i>
                            <h2>Nessun articolo trovato</h2>
                            <?php if (!empty($search_query)): ?>
                                <p>La tua ricerca non ha prodotto risultati. Prova con termini diversi.</p>
                                <a href="blog.php" class="btn btn-primary">Torna al blog</a>
                            <?php else: ?>
                                <p>Non ci sono ancora articoli pubblicati. Torna presto per novità!</p>
                            <?php endif; ?>
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

