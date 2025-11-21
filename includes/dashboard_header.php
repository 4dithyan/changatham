<?php
// Check if user is logged in
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

// Count unread notifications for the user
include 'config.php';
$user_id = $_SESSION['USER_ID'];
$unread_count_query = "SELECT COUNT(*) as unread_count 
                      FROM user_notifications 
                      WHERE user_id = '$user_id' AND is_read = 0";
$unread_count_result = mysqli_query($conn, $unread_count_query);
$unread_count = mysqli_fetch_assoc($unread_count_result)['unread_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title : 'User Dashboard - Changatham'; ?></title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  <!-- Common CSS -->
  <link href="includes/common.css" rel="stylesheet">
  <style>
    /* Dashboard specific styles */
    .navbar-dashboard {
      background: linear-gradient(135deg, #004d40, #00695c);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .navbar-dashboard .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
    }
    
    .navbar-dashboard .nav-link:hover,
    .navbar-dashboard .nav-link.active {
      color: #ffeb3b !important;
    }
    
    .breadcrumb-dashboard {
      background-color: #e8f5e9;
    }
    
    .notification-badge {
      position: absolute;
      top: 5px;
      right: 5px;
      font-size: 0.6rem;
      padding: 2px 5px;
      border-radius: 50%;
      background-color: #ff4444;
      color: white;
    }
    
    .nav-item-notification {
      position: relative;
    }
  </style>
  <?php 
  // Additional page-specific CSS if needed
  if (isset($additional_css)) {
      echo '<style>' . $additional_css . '</style>';
  }
  ?>
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar d-flex justify-content-between">
    <div>
      <i class="bi bi-envelope"></i> <a href="mailto:packagebooking@gmail.com">packagebooking@gmail.com</a>
      <i class="bi bi-instagram ms-3"></i> <a href="https://www.instagram.com/packagebooking" target="_blank">@packagebooking</a>
    </div>
    <div>
      <i class="bi bi-telephone"></i> +91 9876543210
    </div>
  </div>

  <!-- Dashboard Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dashboard">
    <div class="container">
      <a class="navbar-brand" href="home.php">
        <img src="images/packagebookinglogo.png" alt="Package Booking Logo" class="logo">
        <span class="ms-2">Changatham</span>
      </a>
      <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="dashboardNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>" href="home.php">Home</a></li>
          
          <!-- Notifications icon next to Home button -->
          <li class="nav-item nav-item-notification">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'notifications.php') ? 'active' : ''; ?>" href="notifications.php" title="Notifications">
              <i class="bi bi-bell"></i>
              <?php if($unread_count > 0): ?>
                <span class="notification-badge"><?php echo $unread_count; ?></span>
              <?php endif; ?>
            </a>
          </li>
          
          <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'packages.php') ? 'active' : ''; ?>" href="packages.php">Packages</a></li>
          <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'active' : ''; ?>" href="gallery.php">Gallery</a></li>
          <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reviews.php') ? 'active' : ''; ?>" href="reviews.php">Reviews</a></li>
          <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bookings.php') ? 'active' : ''; ?>" href="bookings.php">My Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="home.php#about">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout (<?php echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'User'; ?>)</a></li>
        </ul>
      </div>
    </div>
  </nav>