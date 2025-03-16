<?php
/**
 * Sanitize user input
 * 
 * @param string $data The input data to sanitize
 * @return string The sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to login page if user is not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Get user data by ID
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @return array|null User data or null if not found
 */
function get_user_by_id($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Format date in Italian format
 * 
 * @param string $date Date in Y-m-d format
 * @return string Date in d/m/Y format
 */
function format_date($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Get all services
 * 
 * @param mysqli $conn Database connection
 * @return array Array of services
 */
function get_all_services($conn) {
    $services = [];
    $result = $conn->query("SELECT * FROM services ORDER BY name");
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
    
    return $services;
}

/**
 * Get service by ID
 * 
 * @param mysqli $conn Database connection
 * @param int $service_id Service ID
 * @return array|null Service data or null if not found
 */
function get_service_by_id($conn, $service_id) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get recent blog posts
 * 
 * @param mysqli $conn Database connection
 * @param int $limit Number of posts to retrieve
 * @return array Array of blog posts
 */
function get_recent_posts($conn, $limit = 3) {
    $posts = [];
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    
    return $posts;
}

/**
 * Get blog post by ID
 * 
 * @param mysqli $conn Database connection
 * @param int $post_id Post ID
 * @return array|null Post data or null if not found
 */
function get_post_by_id($conn, $post_id) {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 'published'");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Crea uno slug da una stringa
 * 
 * @param string $string La stringa da convertire in slug
 * @return string Lo slug generato
 */
function create_slug($string) {
    // Converti in minuscolo
    $string = strtolower($string);
    
    // Rimuovi caratteri speciali
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    
    // Sostituisci spazi con trattini
    $string = preg_replace('/\s+/', '-', $string);
    
    // Rimuovi trattini multipli
    $string = preg_replace('/-+/', '-', $string);
    
    // Rimuovi trattini all'inizio e alla fine
    $string = trim($string, '-');
    
    return $string;
}
?>

