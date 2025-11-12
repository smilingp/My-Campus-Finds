<?php
session_start();
require 'database.php';
if (empty($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

// Get admin details
$stmt = $pdo->prepare("SELECT username, display_name FROM admin WHERE username = ?");
$stmt->execute([$_SESSION['admin_user']]);
$admin_info = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Panel - CampusFinds</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --warning: #f72585;
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }

        .admin-header {
            background: var(--gradient);
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            text-align: center;
            border-left: 6px solid var(--primary);
        }

        .welcome-card h1 {
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .welcome-card p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .form-card h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-card h2::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, var(--primary), transparent);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .submit-btn {
            background: var(--gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            border-top: 4px solid var(--primary);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats-cards {
                grid-template-columns: 1fr;
            }

            .admin-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-cogs"></i> Admin Panel
            </div>
            <div class="admin-info">
                <div class="user-badge">
                    <i class="fas fa-user-shield"></i>
                    <?php echo htmlspecialchars($admin_info['display_name'] ?? $_SESSION['admin_user']); ?>
                    (<?php echo htmlspecialchars($_SESSION['admin_user']); ?>)
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <h1>Welcome Back! 👋</h1>
            <p><?php echo htmlspecialchars($admin_info['display_name'] ?? $_SESSION['admin_user']); ?>, start managing your lost and found items</p>
        </div>

        <!-- Stats Cards -->
        <?php
        $total_items = $pdo->query("SELECT COUNT(*) as count FROM items")->fetch()['count'];
        $today_items = $pdo->query("SELECT COUNT(*) as count FROM items WHERE DATE(date_posted) = CURDATE()")->fetch()['count'];
        $lost_items = $pdo->query("SELECT COUNT(*) as count FROM items WHERE title LIKE 'Lost:%'")->fetch()['count'];
        $found_items = $pdo->query("SELECT COUNT(*) as count FROM items WHERE title LIKE 'Found:%'")->fetch()['count'];
        ?>
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="stat-number"><?php echo $total_items; ?></div>
                <div class="stat-label">Total Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-number"><?php echo $today_items; ?></div>
                <div class="stat-label">Today's Posts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="stat-number"><?php echo $lost_items; ?></div>
                <div class="stat-label">Lost Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="stat-number"><?php echo $found_items; ?></div>
                <div class="stat-label">Found Items</div>
            </div>
        </div>

        <!-- Post Form -->
        <div class="form-card">
            <h2><i class="fas fa-plus-circle"></i> Post New Item</h2>
            <form action="upload_item.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="type"><i class="fas fa-tag"></i> Item Type</label>
                    <select name="type" class="form-control" required>
                        <option value="Lost">Lost Item</option>
                        <option value="Found">Found Item</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title"><i class="fas fa-pen"></i> Item Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Black Wallet, Student ID Card, etc." required>
                </div>

                <div class="form-group">
                    <label for="description"><i class="fas fa-file-alt"></i> Description</label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Please provide detailed description, location found/lost, contact information, etc." required></textarea>
                </div>

                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Item Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Post Item
                </button>
            </form>
        </div>
    </div>
</body>
</html>