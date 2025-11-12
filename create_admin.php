<?php
require 'database.php';

$username = 'admin';
$password = 'admin000';

// Create proper password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert or update admin user
$stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?) 
                      ON DUPLICATE KEY UPDATE password = ?");
$stmt->execute([$username, $hashed_password, $hashed_password]);

echo "Admin user created/updated successfully!<br>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "Hash: " . $hashed_password;
?>