<?php
session_start();
require 'database.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    
    if (!$u || !$p) {
        $err = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$u]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($p, $admin['password'])) {
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $admin['username'];
            header('Location: admin_panel.php');
            exit;
        } else {
            $err = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login - CampusFinds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .login-header h1 {
            color: #333;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 2;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
        }

        .err {
            background: #ffe6e6;
            color: #d63031;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #d63031;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .back-link a {
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            gap: 12px;
        }

        .admin-accounts {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .admin-accounts h4 {
            margin-bottom: 10px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .account-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .account-info:last-child {
            border-bottom: none;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Admin Login</h1>
            <p>Access the admin dashboard</p>
        </div>

        <?php if($err): ?>
            <div class="err">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($err); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

       
        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>
</body>
</html>