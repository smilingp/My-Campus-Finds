<?php
require 'database.php';

$password = "admin123"; // Your preferred password
$hash = password_hash($password, PASSWORD_DEFAULT);

$pdo->exec("TRUNCATE TABLE admin");
$stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->execute(['admin', $hash]);

echo "Admin setup complete!<br>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<a href='admin_login.php'>Login Now</a>";
?>