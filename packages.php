<?php
// Start session at the very beginning
session_start();

$page_title = "Travel Packages - Changatham";
include 'config.php'; // Database connection

$search = "";
$sort_order = "package_id DESC";

// Handle search
if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Handle sorting
if(isset($_GET['sort'])){
    switch($_GET['sort']){
        case 'price_low':
            $sort_order = "price ASC";
            break;
        case 'price_high':
            $sort_order = "price DESC";
            break;
        case 'name':
            $sort_order = "name ASC";
            break;
        case 'duration':
            $sort_order = "duration ASC";
            break;
        default:
            $sort_order = "package_id DESC";
    }
}

// Fetch packages from database with destination information
$query = "SELECT * FROM packages WHERE 1=1";

// Add search filter
if($search != ""){
    $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

// Add sorting
$query .= " ORDER BY $sort_order";

$result = mysqli_query($conn, $query);

// Handle query errors
if (!$result) {
    $error = "Database query failed: " . mysqli_error($conn);
}

$additional_css = "
    .package-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
        margin-bottom: 25px;
    }
    
    .package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .package-card-img {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
    
    .package-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }
    
    .package-title {
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1.3rem;
    }
    
    .package-description {
        color: #666;
        margin-bottom: 15px;
        flex: 1;
    }
    
    .package-meta {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin: 15px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
    }
    
    .meta-item i {
        margin-right: 8px;
        min-width: 20px;
        color: var(--primary);
    }
    
    .package-price {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary);
        margin: 15px 0;
        text-align: center;
    }
    
    .package-price small {
        font-size: 1rem;
        font-weight: 400;
    }
    
    .package-actions {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-package {
        padding: 12px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-package:hover {
        transform: translateY(-2px);
    }
    
    .btn-view-details {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        border: none;
    }
    
    .btn-book-now {
        background: linear-gradient(135deg, var(--accent), #ffc107);
        color: var(--primary);
        border: none;
        font-weight: 700;
    }
    
    .btn-login-book {
        background: linear-gradient(135deg, var(--primary), #004d40);
        color: white;
        border: none;
    }
    
    .search-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #e0f7fa, #bbdefb);
        text-align: center;
    }
    
    .section-title {
        font-family: 'Playfair Display', serif;
        color: var(--primary);
        margin-bottom: 30px;
        font-weight: 700;
    }
    
    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: end;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .packages-section {
        padding: 60px 0;
        background-color: #f8f9fa;
    }
    
    .no-packages {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 50px;
        text-align: center;
        margin: 0 auto;
        max-width: 600px;
    }
    
    .package-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--accent);
        color: var(--primary);
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        z-index: 2;
    }
    
    .package-slots {
        text-align: center;
        padding: 8px;
        background: #e9ecef;
        border-radius: 8px;
        margin: 10px 0;
        font-weight: 500;
    }
    
    .slot-low {
        background: #fff3cd;
        color: #856404;
    }
    
    .slot-critical {
        background: #f8d7da;
        color: #721c24;
    }
    
    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
        }
        
        .package-meta {
            grid-template-columns: 1fr;
        }
    }
";

include 'includes/header.php';
?>

<!-- Search Section -->
<section class="search-section">
  <div class="container">
    <h1 class="section-title">Discover Amazing Travel Packages</h1>
    <p class="lead mb-4">Explore our curated collection of unforgettable journeys across Kerala</p>
    <form class="search-form" action="packages.php" method="GET" style="max-width: 700px; margin: 0 auto;">
      <div class="input-group">
        <input type="text" class="form-control form-control-lg" name="search" placeholder="Search for packages, activities..." value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-lg" style="background-color: var(--primary); color: white;" type="submit">
          <i class="bi bi-search"></i> Search
        </button>
      </div>
    </form>
  </div>
</section>

<!-- Packages Section -->
<section class="packages-section">
  <div class="container">
    <!-- Filter Section -->
    <div class="filter-section">
      <form class="filter-form" method="GET">
        <?php if($search != ""): ?>
          <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <?php endif; ?>
        
        <div class="filter-group">
          <label class="form-label">Sort By</label>
          <select name="sort" class="form-select">
            <option value="">Newest First</option>
            <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : ''; ?>>Package Name</option>
            <option value="duration" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'duration') ? 'selected' : ''; ?>>Duration</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label class="form-label">&nbsp;</label>
          <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
        
        <?php if($search != "" || isset($_GET['sort'])): ?>
          <div class="filter-group">
            <label class="form-label">&nbsp;</label>
            <a href="packages.php" class="btn btn-outline-secondary w-100">Clear All</a>
          </div>
        <?php endif; ?>
      </form>
    </div>
    
    <h2 class="section-title text-center mb-4">
      <?php 
      if($search != "") {
        echo "Search Results for \"" . htmlspecialchars($search) . "\"";
      } else {
        echo "Our Travel Packages";
      }
      ?>
    </h2>
    
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="row">
      <?php
      if(isset($result) && mysqli_num_rows($result) > 0){
          while($row = mysqli_fetch_assoc($result)){
              // Determine slot availability badge
              $slot_class = "";
              $slot_text = "";
              if($row['available_slots'] <= 0) {
                  $slot_class = "slot-critical";
                  $slot_text = "SOLD OUT";
              } else if($row['available_slots'] <= 3) {
                  $slot_class = "slot-low";
                  $slot_text = "ONLY " . $row['available_slots'] . " LEFT!";
              } else {
                  $slot_text = $row['available_slots'] . " slots available";
              }
              
              echo '<div class="col-lg-4 col-md-6">';
              echo '<div class="card package-card">';
              
              // Package image with badge
              echo '<div class="position-relative">';
              echo '<img src="uploads/'.$row['image'].'" alt="'.$row['destination'].'" class="package-card-img" onerror="this.src=\'images/default-package.jpg\'">';
              if($row['available_slots'] <= 3 && $row['available_slots'] > 0) {
                  echo '<div class="package-badge">' . $slot_text . '</div>';
              } else if($row['available_slots'] <= 0) {
                  echo '<div class="package-badge bg-danger text-white">SOLD OUT</div>';
              }
              echo '</div>';
              
              echo '<div class="card-body package-card-body">';
              echo '<h3 class="package-title">'.$row['name'].'</h3>';
              echo '<p class="package-description">'.substr($row['description'], 0, 120).'...</p>';
              
              echo '<div class="package-meta">';
              echo '<div class="meta-item"><i class="bi bi-geo-alt"></i> '.$row['destination'].'</div>';
              echo '<div class="meta-item"><i class="bi bi-clock"></i> '.$row['duration'].'</div>';
              // Add date information
              if (!empty($row['start_date'])) {
                  echo '<div class="meta-item"><i class="bi bi-calendar-event"></i> ';
                  echo date('d M Y', strtotime($row['start_date']));
                  if (!empty($row['end_date'])) {
                      echo ' - ' . date('d M Y', strtotime($row['end_date']));
                  }
                  echo '</div>';
              }
              // Add time information
              if (!empty($row['daily_start_time']) || !empty($row['daily_end_time'])) {
                  echo '<div class="meta-item"><i class="bi bi-clock-history"></i> ';
                  if (!empty($row['daily_start_time'])) {
                      echo date('g:i A', strtotime($row['daily_start_time']));
                  }
                  if (!empty($row['daily_end_time'])) {
                      if (!empty($row['daily_start_time'])) {
                          echo ' - ';
                      }
                      echo date('g:i A', strtotime($row['daily_end_time']));
                  }
                  echo '</div>';
              }
              echo '<div class="meta-item"><i class="bi bi-people"></i> '.$row['accommodation'].'</div>';
              echo '<div class="meta-item"><i class="bi bi-cup"></i> '.$row['food_type'].'</div>';
              echo '<div class="meta-item"><i class="bi bi-car-front"></i> '.$row['transportation'].'</div>';
              echo '<div class="meta-item"><i class="bi bi-geo"></i> '.$row['pickup_point'].'</div>';
              echo '</div>';
              
              echo '<div class="package-price">₹'.number_format($row['price'],2).'</div>';
              
              echo '<div class="package-slots ' . $slot_class . '">';
              echo $slot_text;
              echo '</div>';
              
              echo '<div class="package-actions">';
              echo '<a href="package_detail.php?id='.$row['package_id'].'" class="btn btn-view-details btn-package">View Details</a>';
              
              // Show Book Now button only for logged-in users
              if(isset($_SESSION['EMAIL']) && (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin')) {
                  if($row['available_slots'] > 0) {
                      echo '<a href="book_package.php?id='.$row['package_id'].'" class="btn btn-book-now btn-package">Book Now</a>';
                  } else {
                      echo '<button class="btn btn-outline-secondary btn-package" disabled>Sold Out</button>';
                  }
              } else if(!isset($_SESSION['EMAIL'])) {
                  echo '<a href="login.php" class="btn btn-login-book btn-package">Login to Book</a>';
              }
              // Admins don't see the Book Now button
              
              echo '</div>'; // package-actions
              echo '</div>'; // card-body
              echo '</div>'; // card
              echo '</div>'; // col
          }
      } else {
          echo '<div class="col-12">';
          echo '<div class="no-packages">';
          echo '<i class="bi bi-search" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>';
          echo '<h3>No Packages Found</h3>';
          if($search != "") {
              echo '<p class="mb-4">Sorry, we couldn\'t find any packages matching your criteria.</p>';
              echo '<a href="packages.php" class="btn btn-accent">View All Packages</a>';
          } else {
              echo '<p class="mb-4">We currently don\'t have any packages available. Please check back later.</p>';
              echo '<a href="home.php" class="btn btn-primary">Go to Homepage</a>';
          }
          echo '</div>';
          echo '</div>';
      }
      ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>