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

// Recupera i dati dell'articolo
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Articolo non trovato.";
    header('Location: blog.php');
    exit;
}

$post = $result->fetch_assoc();
$errors = [];

// Gestione del form di invio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validazione dei dati
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    
    // Validazione
    if (empty($title)) {
        $errors[] = "Il titolo è obbligatorio.";
    }
    
    if (empty($slug)) {
        // Genera slug dal titolo
        $slug = create_slug($title);
    } else {
        $slug = create_slug($slug);
    }
    
    // Verifica se lo slug esiste già (escludendo l'articolo corrente)
    $stmt = $conn->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
    $stmt->bind_param("si", $slug, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Esiste già un articolo con questo slug. Scegli un titolo o uno slug diverso.";
    }
    
    if (empty($content)) {
        $errors[] = "Il contenuto dell'articolo è obbligatorio.";
    }
    
    // Gestione dell'immagine
    $featured_image = $post['featured_image']; // Mantieni l'immagine esistente di default
    
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['featured_image']['type'], $allowed_types)) {
            $errors[] = "Il formato dell'immagine non è valido. Formati supportati: JPG, PNG, GIF, WEBP.";
        } elseif ($_FILES['featured_image']['size'] > $max_size) {
            $errors[] = "L'immagine è troppo grande. Dimensione massima: 5MB.";
        } else {
            // Crea la directory se non esiste
            $upload_dir = "../uploads/blog/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Genera un nome file unico
            $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
            $new_featured_image = uniqid('blog_') . '.' . $file_extension;
            $upload_path = $upload_dir . $new_featured_image;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                // Se c'è un'immagine precedente, eliminala
                if (!empty($post['featured_image']) && file_exists($upload_dir . $post['featured_image'])) {
                    unlink($upload_dir . $post['featured_image']);
                }
                $featured_image = $new_featured_image;
            } else {
                $errors[] = "Errore durante il caricamento dell'immagine.";
            }
        }
    } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        // Se è stato richiesto di rimuovere l'immagine
        if (!empty($post['featured_image']) && file_exists("../uploads/blog/" . $post['featured_image'])) {
            unlink("../uploads/blog/" . $post['featured_image']);
        }
        $featured_image = '';
    }
    
    // Se non ci sono errori, aggiorna l'articolo
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssssssi", $title, $slug, $content, $excerpt, $featured_image, $status, $post_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Articolo aggiornato con successo.";
            header("Location: blog.php");
            exit;
        } else {
            $errors[] = "Errore durante l'aggiornamento dell'articolo: " . $conn->error;
        }
    }
}

// Titolo della pagina
$page_title = "Modifica Articolo";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Articolo - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 400
        });
    </script>
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
                        <a href="view-post.php?id=<?php echo $post_id; ?>" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizza
                        </a>
                        <a href="blog.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Torna al Blog
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Informazioni Articolo</h2>
                    </div>
                    
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" class="form">
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="title">Titolo *</label>
                                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? $post['title']); ?>" required>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label for="status">Stato</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="draft" <?php echo (isset($_POST['status']) ? $_POST['status'] === 'draft' : $post['status'] === 'draft') ? 'selected' : ''; ?>>Bozza</option>
                                        <option value="published" <?php echo (isset($_POST['status']) ? $_POST['status'] === 'published' : $post['status'] === 'published') ? 'selected' : ''; ?>>Pubblicato</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="slug">Slug</label>
                                <input type="text" id="slug" name="slug" class="form-control" value="<?php echo htmlspecialchars($_POST['slug'] ?? $post['slug']); ?>">
                                <small class="form-text text-muted">Se lasciato vuoto, verrà generato automaticamente dal titolo.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="excerpt">Estratto</label>
                                <textarea id="excerpt" name="excerpt" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['excerpt'] ?? $post['excerpt']); ?></textarea>
                                <small class="form-text text-muted">Un breve riassunto dell'articolo (opzionale).</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="featured_image">Immagine in Evidenza</label>
                                
                                <?php if (!empty($post['featured_image']) && file_exists("../uploads/blog/" . $post['featured_image'])): ?>
                                    <div class="current-image">
                                        <img src="../uploads/blog/<?php echo $post['featured_image']; ?>" alt="Immagine attuale" class="img-thumbnail" style="max-height: 200px;">
                                        <div class="image-actions">
                                            <label class="checkbox-container">
                                                <input type="checkbox" name="remove_image" value="1" <?php echo isset($_POST['remove_image']) ? 'checked' : ''; ?>>
                                                <span class="checkmark"></span>
                                                Rimuovi immagine
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" id="featured_image" name="featured_image" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Formati supportati: JPG, PNG, GIF, WEBP. Dimensione massima: 5MB.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Contenuto *</label>
                                <textarea id="content" name="content" class="form-control"><?php echo htmlspecialchars($_POST['content'] ?? $post['content']); ?></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Aggiorna Articolo
                                </button>
                                <a href="blog.php" class="btn btn-outline">Annulla</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="../js/admin.js"></script>
    <script>
        // Script per generare automaticamente lo slug dal titolo
        document.getElementById('title').addEventListener('blur', function() {
            const slugField = document.getElementById('slug');
            if (slugField.value === '') {
                const titleValue = this.value.trim();
                if (titleValue) {
                    // Semplice conversione in slug (per una versione più robusta, usare una funzione lato server)
                    const slug = titleValue
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '') // Rimuove caratteri speciali
                        .replace(/\s+/g, '-')     // Sostituisce spazi con trattini
                        .replace(/-+/g, '-');     // Rimuove trattini multipli
                    
                    slugField.value = slug;
                }
            }
        });
    </script>
</body>
</html>

