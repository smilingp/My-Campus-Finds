<?php
session_start();
require 'database.php';

// Handle search filter
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';

// Build SQL query based on filters
$sql = "SELECT * FROM items WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($type_filter)) {
    $sql .= " AND title LIKE ?";
    $params[] = "$type_filter:%";
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query("SELECT title, date_posted FROM items ORDER BY id DESC LIMIT 3");
$marquee = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Campus Finds</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #6B705C  ; /*#4361ee;*/
      --secondary: #a5a58d ; /*#3a0ca3;*/
      --accent: #cb997e;/*#7209b7;*/
      --success: #4cc9f0;
      --warning: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --gradient: linear-gradient(140deg, #4cc9f0 /*#f4d927ff/*#5b74r3ff*/ 0%, #5312ecff /*#83f14bff*/100%);
      --shadow: 0 8px 30px rgba(121, 39, 39, 0.12);
      --shadow-hover: 0 15px 40px rgba(0,0,0,0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #FAF3E0;
      /*background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);*/
      min-height: 100vh;
      color: var(--dark);
      line-height: 1.6;
    }

    header {
      background: var(--gradient);
      color: white;
      padding: 25px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"/></svg>');
      background-size: cover;
    }

    .header-content {
      position: relative;
      z-index: 2;
    }

    .logo {
      font-size: 3rem;
      font-weight: 800;
      margin-bottom: 10px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .tagline {
      font-size: 1.2rem;
      opacity: 0.9;
      font-weight: 500;
    }

    .marquee {
      background: linear-gradient(135deg,  #5312ecff 0%, #4cc9f0 100%);
      color: var(--dark);
      padding: 10px 0;
      position: relative;
      overflow: hidden;
      font-weight: 500;
    }

    .marquee::before {
      content: '🔔';
      position: absolute;
      left: 20px;
      z-index: 2;
    }

    .marquee p {
      display: inline-block;
      padding-left: 100%;
      animation: scroll 20s linear infinite;
      margin: 0;
      white-space: nowrap;
    }

    @keyframes scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }

    .container {
      max-width: 1300px;
      margin: 10px auto;
      padding: 0 25px;
    }

    .admin-link {
      text-align: left;
      margin-bottom: 10px;
    }

    .admin-link a {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 8px 20px;
      background: var(--gradient);
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: var(--shadow);
    }

    .admin-link a:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .search-filter {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      /*padding: 20px;
      border-radius: 20px;
      box-shadow: var(--shadow);
      margin-bottom: 30px;*/
    }

    .search-form {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
    }

    .search-box {
      flex: 1;
      min-width: 200px;
      position: relative;
    }

    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      /*color: #6c757d;*/
    }

    .search-box input {
      width: 100%;
      padding: 15px 15px 15px 45px;
      border: 1px solid #e9ecef;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .search-box input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
      outline: none;
    }

    .filter-select {
      position: relative;
    }

    .filter-select i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      z-index: 2;
    }

    .filter-select select {
      padding: 15px 15px 15px 45px;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      background: white;
      font-size: 1rem;
      cursor: pointer;
      appearance: none;
      min-width: 180px;
    }

    .search-btn {
      padding: 15px 25px;
      background: var(--gradient);
      color: white;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .search-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .clear-btn {
      padding: 15px 20px;
      background: #6c757d;
      color: white;
      text-decoration: none;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .clear-btn:hover {
      background: #545b62;
      transform: translateY(-2px);
      color: white;
    }
    
    h2 {
      color: var(--dark);
      margin: 30px 0 20px;
      font-size: 2rem;
      position: relative;
      display: inline-block;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--gradient);
      border-radius: 2px;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 25px;
      margin-top: 20px;
    }

    .card {
      background: white;
      border-radius: 20px;
      box-shadow: var(--shadow);
      padding: 0;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
    }

    .card-image {
      width: 100%;
      height: 200px;
      position: relative;
      overflow: hidden;
    }

    .card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover .card-image img {
      transform: scale(1.1);
    }

    .no-image {
      width: 100%;
      height: 200px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #6c757d;
      border: 2px dashed #dee2e6;
    }

    .no-image i {
      font-size: 3rem;
      margin-bottom: 10px;
      opacity: 0.5;
    }

    .no-image span {
      font-size: 0.9rem;
      font-weight: 500;
    }

    .card-badge {
      position: absolute;
      top: 15px;
      left: 15px;
      background: var(--gradient);
      color: white;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .badge-lost {
      background: linear-gradient(135deg, #c455edff 0%, #4cc9f0 100%);
    }

    .badge-found {
      background: linear-gradient(135deg, #4cc9f0 0%, #c455edff 100%);
    }

    .card-content {
      padding: 20px;
    }

    .card h3 {
      color: var(--primary);
      margin: 0 0 10px;
      font-size: 1.2rem;
      line-height: 1.4;
    }

    .card p {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.5;
      margin-bottom: 15px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .card-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 15px;
      border-top: 1px solid #f0f0f0;
    }

    .meta-date {
      font-size: 0.85rem;
      color: #888;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .results-info {
      background: var(--gradient);
      color: white;
      padding: 15px 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: var(--shadow);
    }

    .no-items {
      text-align: center;
      padding: 60px 20px;
      color: #666;
      grid-column: 1 / -1;
    }

    .no-items i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 20px;
    }

    .no-items h3 {
      color: #888;
      margin-bottom: 10px;
    }

    footer {
      background: var(--gradient);
      color: white;
      text-align: center;
      padding: 1px 20px;
      margin-top: 50px;
    }

    .footer-content {
      max-width: 1300px;
      margin: 0 auto;
    }

    .footer-logo {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 15px;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin: 25px 0;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: white;
      text-decoration: none;
      transition: opacity 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .footer-links a:hover {
      opacity: 0.8;
    }

    .copyright {
      opacity: 0.8;
      font-size: 0.9rem;
      margin-top: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .search-form {
        flex-direction: column;
      }
      
      .search-box, .filter-select {
        min-width: 100%;
      }
      
      .cards {
        grid-template-columns: 1fr;
      }
      
      .logo {
        font-size: 2.2rem;
      }
      
      .footer-links {
        flex-direction: column;
        gap: 15px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-content">
      <div class="logo">
        <i class="fas fa-search-location"></i> MyCampusFinds
      </div>
      <div class="tagline">Your Campus Lost & Found Platform - Connecting Lost Items with Their Owners</div>
    </div>
  </header>

  <div class="marquee">
    <p>
      <?php
      if (count($marquee) === 0) {
        echo "✨ Welcome to CampusFinds! No recent updates yet.";
      } else {
        $parts = [];
        foreach ($marquee as $m) {
          $parts[] = "📢 " . htmlspecialchars($m['title']) . " (" . date('m/d', strtotime($m['date_posted'])) . ")";
        }
        echo implode(" • ", $parts);
      }
      ?>
    </p>
  </div>

  <div class="container">
    <!-- Admin Link -->
    <div class="admin-link">
      <a href="admin_login.php">
        <i class="fas fa-lock"></i> Admin Login
      </a>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-filter">
      <form method="GET" action="" class="search-form">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" name="search" placeholder="Search items by name or description..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="filter-select">
          <i class="fas fa-filter"></i>
          <select name="type">
            <option value="">All Types</option>
            <option value="Lost" <?php echo ($type_filter === 'Lost') ? 'selected' : ''; ?>>Lost Items</option>
            <option value="Found" <?php echo ($type_filter === 'Found') ? 'selected' : ''; ?>>Found Items</option>
          </select>
        </div>
        
        <button type="submit" class="search-btn">
          <i class="fas fa-search"></i> Search
        </button>
        
        <?php if (!empty($search) || !empty($type_filter)): ?>
          <a href="index.php" class="clear-btn">
            <i class="fas fa-times"></i> Clear Filters
          </a>
        <?php endif; ?>
      </form>
    </div>

    <h2>Recent Posts</h2>
    
    <!-- Results Info -->
    <?php if (!empty($search) || !empty($type_filter)): ?>
      <div class="results-info">
        <i class="fas fa-info-circle"></i>
        Found <?php echo count($items); ?> item(s)
        <?php if (!empty($search)): ?>
          matching "<strong><?php echo htmlspecialchars($search); ?></strong>"
        <?php endif; ?>
        <?php if (!empty($type_filter)): ?>
          in <strong><?php echo htmlspecialchars($type_filter); ?></strong> category
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="cards">
      <?php if(empty($items)): ?>
        <div class="no-items">
          <i class="fas fa-inbox"></i>
          <h3>No Items Found</h3>
          <p><?php echo (!empty($search) || !empty($type_filter)) ? 'Try adjusting your search criteria' : 'Be the first to post an item!'; ?></p>
        </div>
      <?php else: ?>
        <?php foreach($items as $it): ?>
          <?php 
          $is_lost = strpos($it['title'], 'Lost:') === 0;
          $badge_text = $is_lost ? 'LOST' : 'FOUND';
          $badge_class = $is_lost ? 'badge-lost' : 'badge-found';
          ?>
          <div class="card">
            <div class="card-image">
              <?php if(!empty($it['image']) && file_exists(__DIR__ . '/uploads/' . $it['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['title']); ?>">
              <?php else: ?>
                <div class="no-image">
                  <i class="fas fa-image"></i>
                  <span>No Image</span>
                </div>
              <?php endif; ?>
              <div class="card-badge <?php echo $badge_class; ?>">
                <?php echo $badge_text; ?>
              </div>
            </div>
            <div class="card-content">
              <h3><?php echo htmlspecialchars($it['title']); ?></h3>
              <p><?php echo nl2br(htmlspecialchars($it['description'])); ?></p>
              <div class="card-meta">
                <div class="meta-date">
                  <i class="far fa-clock"></i>
                  <?php echo date('M j, Y g:i A', strtotime($it['date_posted'])); ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  
  <footer>
    <div class="footer-content">
      <div class="footer-logo">
        <i class="fas fa-search-location"></i> CampusFinds
      </div>
      <div class="footer-links">
        <a href="#"><i class="fas fa-info-circle"></i> About Us</a>
        <a href="#"><i class="fas fa-envelope"></i> Contact</a>
        <a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
        <a href="admin_login.php"><i class="fas fa-lock"></i> Admin</a>
      </div>
      <div class="copyright">
        &copy; <?php echo date('Y'); ?> MyCampusFinds Lost & Found Platform. All rights reserved.
      </div>
    </div>
  </footer>
</body>
</html>