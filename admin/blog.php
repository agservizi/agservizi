<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Verifica se l'utente è loggato e ha i permessi di amministratore
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Gestione delle azioni
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Azione di eliminazione
    if ($_GET['action'] === 'delete' && !empty($id)) {
        // Prima eliminiamo eventuali immagini associate
        $stmt = $conn->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['featured_image']) && file_exists("../uploads/blog/" . $row['featured_image'])) {
                unlink("../uploads/blog/" . $row['featured_image']);
            }
        }
        
        // Poi eliminiamo il post
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Articolo eliminato con successo.";
        } else {
            $_SESSION['error_message'] = "Errore durante l'eliminazione dell'articolo.";
        }
        
        header('Location: blog.php');
        exit;
    }
    
    // Azione di cambio stato (pubblicato/bozza)
    if ($_GET['action'] === 'toggle_status' && !empty($id)) {
        $stmt = $conn->prepare("SELECT status FROM blog_posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $new_status = ($row['status'] === 'published') ? 'draft' : 'published';
            
            $update_stmt = $conn->prepare("UPDATE blog_posts SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_status, $id);
            
            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = "Stato dell'articolo aggiornato con successo.";
            } else {
                $_SESSION['error_message'] = "Errore durante l'aggiornamento dello stato.";
            }
        }
        
        header('Location: blog.php');
        exit;
    }
}

// Impostazioni di paginazione
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($current_page - 1) * $items_per_page;

// Filtri
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Costruzione della query
$query = "SELECT b.*, u.username as author_name 
          FROM blog_posts b 
          LEFT JOIN users u ON b.author_id = u.id 
          WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM blog_posts WHERE 1=1";
$params = [];
$param_types = "";

// Aggiungiamo i filtri alla query
if (!empty($status_filter)) {
    $query .= " AND b.status = ?";
    $count_query .= " AND status = ?";
    $params[] = $status_filter;
    $param_types .= "s";
}

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
if (!empty($param_types)) {
    $count_param_types = str_replace('ii', '', $param_types); // Rimuoviamo i parametri di limit e offset
    $count_params = array_slice($params, 0, -2); // Rimuoviamo i valori di limit e offset
    
    if (!empty($count_params)) {
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
if (!empty($param_types)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Conteggio articoli per stato
$status_counts = [
    'all' => $total_items,
    'published' => 0,
    'draft' => 0
];

$status_count_query = "SELECT status, COUNT(*) as count FROM blog_posts GROUP BY status";
$status_count_result = $conn->query($status_count_query);
while ($status_row = $status_count_result->fetch_assoc()) {
    $status_counts[$status_row['status']] = $status_row['count'];
}

// Titolo della pagina
$page_title = "Gestione Blog";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Blog - Dashboard</title>
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
                    <div>
                        <h1><?php echo $page_title; ?></h1>
                        <p class="text-muted">Gestisci gli articoli del tuo blog</p>
                    </div>
                    <div class="action-buttons">
                        <a href="../blog.php" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-eye"></i> Visualizza Blog
                        </a>
                        <a href="add-post.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuovo Articolo
                        </a>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php 
                        echo $_SESSION['success_message']; 
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-card-icon blog-all">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3>Tutti gli articoli</h3>
                            <p><?php echo $status_counts['all']; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon blog-published">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3>Pubblicati</h3>
                            <p><?php echo $status_counts['published'] ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-icon blog-draft">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="stat-card-content">
                            <h3>Bozze</h3>
                            <p><?php echo $status_counts['draft'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Articoli del Blog</h2>
                        
                        <div class="filters-container">
                            <form action="" method="GET" class="filter-form">
                                <div class="filters">
                                    <div class="form-group">
                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                            <option value="">Tutti gli stati</option>
                                            <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Pubblicati</option>
                                            <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Bozze</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="search">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="search" placeholder="Cerca articoli..." value="<?php echo htmlspecialchars($search_query); ?>" class="form-control">
                                    <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="10%">Immagine</th>
                                            <th width="30%">Titolo</th>
                                            <th width="15%">Autore</th>
                                            <th width="15%">Data</th>
                                            <th width="10%">Stato</th>
                                            <th width="15%">Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td>
                                                    <?php if (!empty($row['featured_image']) && file_exists("../uploads/blog/" . $row['featured_image'])): ?>
                                                        <img src="../uploads/blog/<?php echo $row['featured_image']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="thumbnail">
                                                    <?php else: ?>
                                                        <div class="no-image">No Image</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="post-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                                    <?php if (!empty($row['slug'])): ?>
                                                        <div class="post-slug"><?php echo htmlspecialchars($row['slug']); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                                                <td>
                                                    <div class="post-date">
                                                        <i class="far fa-calendar-alt"></i>
                                                        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                                    </div>
                                                    <?php if ($row['created_at'] != $row['updated_at']): ?>
                                                        <div class="post-date updated">
                                                            <i class="far fa-edit"></i>
                                                            <?php echo date('d/m/Y', strtotime($row['updated_at'])); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?php echo $row['status'] === 'published' ? 'status-active' : 'status-inactive'; ?>">
                                                        <?php echo $row['status'] === 'published' ? 'Pubblicato' : 'Bozza'; ?>
                                                    </span>
                                                </td>
                                                <td class="actions">
                                                    <a href="view-post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="Visualizza">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Modifica">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="blog.php?action=toggle_status&id=<?php echo $row['id']; ?>" class="btn btn-sm <?php echo $row['status'] === 'published' ? 'btn-warning' : 'btn-success'; ?>" title="<?php echo $row['status'] === 'published' ? 'Imposta come bozza' : 'Pubblica'; ?>">
                                                        <i class="fas <?php echo $row['status'] === 'published' ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                                    </a>
                                                    <a href="blog.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" title="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo articolo?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if ($total_pages > 1): ?>
                                <div class="pagination">
                                    <?php if ($current_page > 1): ?>
                                        <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($status_filter) ? '&status=' . $status_filter : ''; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="btn btn-sm btn-outline">
                                            <i class="fas fa-chevron-left"></i> Precedente
                                        </a>
                                    <?php endif; ?>
                                    
                                    <div class="pagination-numbers">
                                        <?php
                                        $start_page = max(1, $current_page - 2);
                                        $end_page = min($total_pages, $current_page + 2);
                                        
                                        if ($start_page > 1) {
                                            echo '<a href="?page=1' . (!empty($status_filter) ? '&status=' . $status_filter : '') . (!empty($search_query) ? '&search=' . urlencode($search_query) : '') . '" class="pagination-number">1</a>';
                                            if ($start_page > 2) {
                                                echo '<span class="pagination-ellipsis">...</span>';
                                            }
                                        }
                                        
                                        for ($i = $start_page; $i <= $end_page; $i++) {
                                            $active_class = ($i === $current_page) ? 'active' : '';
                                            echo '<a href="?page=' . $i . (!empty($status_filter) ? '&status=' . $status_filter : '') . (!empty($search_query) ? '&search=' . urlencode($search_query) : '') . '" class="pagination-number ' . $active_class . '">' . $i . '</a>';
                                        }
                                        
                                        if ($end_page < $total_pages) {
                                            if ($end_page < $total_pages - 1) {
                                                echo '<span class="pagination-ellipsis">...</span>';
                                            }
                                            echo '<a href="?page=' . $total_pages . (!empty($status_filter) ? '&status=' . $status_filter : '') . (!empty($search_query) ? '&search=' . urlencode($search_query) : '') . '" class="pagination-number">' . $total_pages . '</a>';
                                        }
                                        ?>
                                    </div>
                                    
                                    <?php if ($current_page < $total_pages): ?>
                                        <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($status_filter) ? '&status=' . $status_filter : ''; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="btn btn-sm btn-outline">
                                            Successiva <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-newspaper empty-state-icon"></i>
                                <h3>Nessun articolo trovato</h3>
                                <p>Non ci sono articoli che corrispondono ai criteri di ricerca.</p>
                                <a href="add-post.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crea nuovo articolo
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    
    <script src="../js/admin.js"></script>
</body>
</html>

