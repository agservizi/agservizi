<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Verifica se l'utente è loggato e ha i permessi di amministratore
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Verifica se è stato fornito un ID valido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID articolo non valido.";
    header('Location: blog.php');
    exit;
}

$post_id = intval($_GET['id']);

// Recupera i dati dell'articolo con informazioni sull'autore
$stmt = $conn->prepare("
    SELECT p.*, u.username as author_name, u.email as author_email 
    FROM blog_posts p
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Articolo non trovato.";
    header('Location: blog.php');
    exit;
}

$post = $result->fetch_assoc();

// Titolo della pagina
$page_title = "Visualizza Articolo";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Articolo - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <?php include 'includes/header.php'; ?>
            
            <main class="main-content">
                <div class="page-header">
                    <h1><?php echo $page_title; ?></h1>
                    <div class="header-actions">
                        <a href="edit-post.php?id=<?php echo $post_id; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifica
                        </a>
                        <a href="blog.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Torna al Blog
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                        <div class="post-meta">
                            <span class="status-badge <?php echo $post['status'] === 'published' ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $post['status'] === 'published' ? 'Pubblicato' : 'Bozza'; ?>
                            </span>
                            <span class="post-date">
                                <i class="fas fa-calendar"></i> Creato: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                            </span>
                            <?php if ($post['updated_at'] !== $post['created_at']): ?>
                                <span class="post-date">
                                    <i class="fas fa-edit"></i> Aggiornato: <?php echo date('d/m/Y H:i', strtotime($post['updated_at'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="post-details">
                            <div class="post-info">
                                <div class="info-group">
                                    <h3>Informazioni</h3>
                                    <div class="info-item">
                                        <strong>Autore:</strong>
                                        <span><?php echo htmlspecialchars($post['author_name']); ?> (<?php echo htmlspecialchars($post['author_email']); ?>)</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Slug:</strong>
                                        <span><?php echo htmlspecialchars($post['slug']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <strong>URL:</strong>
                                        <a href="../blog/<?php echo htmlspecialchars($post['slug']); ?>" target="_blank">
                                            ../blog/<?php echo htmlspecialchars($post['slug']); ?>
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <?php if (!empty($post['excerpt'])): ?>
                                    <div class="info-group">
                                        <h3>Estratto</h3>
                                        <div class="post-excerpt">
                                            <?php echo htmlspecialchars($post['excerpt']); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($post['featured_image']) && file_exists("../uploads/blog/" . $post['featured_image'])): ?>
                                    <div class="info-group">
                                        <h3>Immagine in Evidenza</h3>
                                        <div class="post-image">
                                            <img src="../uploads/blog/<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="post-content">
                                <h3>Contenuto</h3>
                                <div class="content-preview">
                                    <?php echo $post['content']; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="post-actions">
                            <a href="edit-post.php?id=<?php echo $post_id; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <a href="blog.php?action=toggle_status&id=<?php echo $post_id; ?>" class="btn <?php echo $post['status'] === 'published' ? 'btn-warning' : 'btn-success'; ?>">
                                <i class="fas <?php echo $post['status'] === 'published' ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                <?php echo $post['status'] === 'published' ? 'Imposta come bozza' : 'Pubblica'; ?>
                            </a>
                            <a href="blog.php?action=delete&id=<?php echo $post_id; ?>" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo articolo?');">
                                <i class="fas fa-trash"></i> Elimina
                            </a>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="../js/admin.js"></script>
</body>
</html>

