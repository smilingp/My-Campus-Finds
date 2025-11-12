<?php
session_start();
require 'database.php';
if (empty($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'Lost';
    $title = trim($_POST['title'] ?? ''); 
    $description = trim($_POST['description'] ?? '');

    if (!$title || !$description) {
        die('Please provide title and description. <a href="admin_panel.php">Back</a>');
    }

    $imageName = null;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        
        if ($f['error'] !== UPLOAD_ERR_OK) {
            die('Upload error. <a href="admin_panel.php">Back</a>');
        }
        
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $f['tmp_name']);
        finfo_close($fileInfo);
        
        if (!in_array($mimeType, $allowed)) {
            die('Only JPG, PNG, GIF allowed. <a href="admin_panel.php">Back</a>');
        }
        
        if ($f['size'] > 3 * 1024 * 1024) {
            die('Image too large (max 3MB). <a href="admin_panel.php">Back</a>');
        }
        
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('itm_', true) . '.' . $ext;
        $target = $uploadDir . $imageName;
        
        if (!move_uploaded_file($f['tmp_name'], $target)) {
            die('Failed to move uploaded file. <a href="admin_panel.php">Back</a>');
        }
    }

    $fullTitle = $type . ': ' . $title;

    $stmt = $pdo->prepare("INSERT INTO items (title, description, image, date_posted) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$fullTitle, $description, $imageName]);

    header('Location: index.php');
    exit;
} else {
    header('Location: admin_panel.php');
    exit;
}
?>