<?php
$page_title = "Blog";
include 'includes/header.php';

// Get blog posts (simplified example)
// In a real application, you would fetch this from a database
$blog_posts = [
    [
        'id' => 1,
        'title' => 'Come attivare lo SPID in 5 semplici passi',
        'date' => '15 Marzo 2023',
        'author' => 'Laura Bianchi',
        'image' => 'assets/images/blog/spid.jpg',
        'excerpt' => 'Scopri come attivare facilmente la tua identità digitale SPID seguendo questa semplice guida passo-passo.',
        'categories' => ['SPID', 'Guide'],
        'link' => 'blog/come-attivare-spid.php'
    ],
    [
        'id' => 2,
        'title' => 'Risparmiare sulla bolletta con le nuove offerte luce e gas',
        'date' => '10 Marzo 2023',
        'author' => 'Paolo Verdi',
        'image' => 'assets/images/blog/energia.jpg',
        'excerpt' => 'Consigli pratici per risparmiare sulle bollette di luce e gas approfittando delle nuove offerte sul mercato.',
        'categories' => ['Energia', 'Risparmio'],
        'link' => 'blog/risparmiare-bollette.php'
    ],
    [
        'id' => 3,
        'title' => 'Vantaggi della firma digitale per professionisti e aziende',
        'date' => '5 Marzo 2023',
        'author' => 'Marco Rossi',
        'image' => 'assets/images/blog/firma-digitale.jpg',
        'excerpt' => 'Scopri tutti i vantaggi della firma digitale per la tua attività professionale o aziendale.',
        'categories' => ['Firma Digitale', 'Business'],
        'link' => 'blog/vantaggi-firma-digitale.php'
    ],
    [
        'id' => 4,
        'title' => 'Le migliori offerte di telefonia mobile del mese',
        'date' => '28 Febbraio 2023',
        'author' => 'Paolo Verdi',
        'image' => 'assets/images/blog/telefonia.jpg',
        'excerpt' => 'Confronto delle migliori offerte di telefonia mobile disponibili questo mese: minuti, SMS e GB a confronto.',
        'categories' => ['Telefonia', 'Offerte'],
        'link' => 'blog/offerte-telefonia-mobile.php'
    ],
    [
        'id' => 5,
        'title' => 'PEC: cos\'è e perché è importante per professionisti e aziende',
        'date' => '20 Febbraio 2023',
        'author' => 'Laura Bianchi',
        'image' => 'assets/images/blog/pec.jpg',
        'excerpt' => 'Tutto quello che devi sapere sulla Posta Elettronica Certificata: funzionamento, vantaggi e obblighi legali.',
        'categories' => ['PEC', 'Business'],
        'link' => 'blog/importanza-pec.php'
    ],
    [
        'id' => 6,
        'title' => 'Come scegliere il corriere giusto per le tue spedizioni',
        'date' => '15 Febbraio 2023',
        'author' => 'Marco Rossi',
        'image' => 'assets/images/blog/spedizioni.jpg',
        'excerpt' => 'Guida alla scelta del corriere più adatto alle tue esigenze di spedizione: tempi, costi e servizi a confronto.',
        'categories' => ['Spedizioni', 'Guide'],
        'link' => 'blog/scegliere-corriere.php'
    ]
];

// Get categories (simplified example)
$categories = [];
foreach ($blog_posts as $post) {
    foreach ($post['categories'] as $category) {
        if (!in_array($category, $categories)) {
            $categories[] = $category;
        }
    }
}
sort($categories);
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Blog</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section">
    <div class="container">
        <div class="blog-container">
            <div class="blog-main">
                <div class="blog-posts">
                    <?php foreach ($blog_posts as $post): ?>
                        <div class="blog-card" data-aos="fade-up">
                            <div class="blog-image">
                                <a href="<?php echo $post['link']; ?>">
                                    <img src="<?php echo $post['image']; ?>" alt="<?php echo $post['title']; ?>">
                                </a>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo $post['date']; ?></span>
                                    <span class="blog-author"><i class="far fa-user"></i> <?php echo $post['author']; ?></span>
                                </div>
                                <h3><a href="<?php echo $post['link']; ?>"><?php echo $post['title']; ?></a></h3>
                                <p><?php echo $post['excerpt']; ?></p>
                                <div class="blog-categories">
                                    <?php foreach ($post['categories'] as $category): ?>
                                        <a href="blog.php?category=<?php echo urlencode($category); ?>" class="blog-category"><?php echo $category; ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <a href="<?php echo $post['link']; ?>" class="btn-link">Leggi di più <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="pagination" data-aos="fade-up">
                    <a href="#" class="active">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#" class="next">Successivo <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="blog-sidebar">
                <div class="sidebar-widget search-widget" data-aos="fade-up">
                    <h3>Cerca</h3>
                    <form action="blog.php" method="get" class="search-form">
                        <input type="text" name="search" placeholder="Cerca articoli...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="sidebar-widget categories-widget" data-aos="fade-up">
                    <h3>Categorie</h3>
                    <ul>
                        <?php foreach ($categories as $category): ?>
                            <li><a href="blog.php?category=<?php echo urlencode($category); ?>"><?php echo $category; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="sidebar-widget recent-posts-widget" data-aos="fade-up">
                    <h3>Articoli Recenti</h3>
                    <ul>
                        <?php for ($i = 0; $i < min(3, count($blog_posts)); $i++): ?>
                            <li>
                                <a href="<?php echo $blog_posts[$i]['link']; ?>">
                                    <div class="post-image">
                                        <img src="<?php echo $blog_posts[$i]['image']; ?>" alt="<?php echo $blog_posts[$i]['title']; ?>">
                                    </div>
                                    <div class="post-info">
                                        <h4><?php echo $blog_posts[$i]['title']; ?></h4>
                                        <span><?php echo $blog_posts[$i]['date']; ?></span>
                                    </div>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
                
                <div class="sidebar-widget newsletter-widget" data-aos="fade-up">
                    <h3>Newsletter</h3>
                    <p>Iscriviti alla nostra newsletter per ricevere aggiornamenti sui nostri servizi e offerte speciali.</p>
                    <form action="/includes/newsletter.php" method="post" class="newsletter-form">
                        <input type="email" name="email" placeholder="La tua email" required>
                        <button type="submit" class="btn btn-primary">Iscriviti</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

