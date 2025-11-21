<?php
session_start();
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle quick add activity
$activity_success = false;
$activity_error = false;

if (isset($_POST['quick_add_activity'])) {
    $activity_name = $conn->real_escape_string($_POST['activity_name']);
    $activity_description = $conn->real_escape_string($_POST['activity_description']);
    
    $insert_activity = "INSERT INTO activities (name, description) VALUES ('$activity_name', '$activity_description')";
    
    if ($conn->query($insert_activity)) {
        $activity_success = true;
    } else {
        $activity_error = true;
    }
}

// Calculate total revenue
$revenue_query = $conn->query("SELECT SUM(total_amount) as total_revenue FROM bookings WHERE status='Confirmed'");
$revenue_result = $revenue_query->fetch_assoc();
$total_revenue = $revenue_result['total_revenue'];

// Count pending payments
$pending_payments_query = $conn->query("SELECT COUNT(*) as pending_count, SUM(remaining_amount) as pending_amount FROM bookings WHERE payment_type='advance' AND remaining_amount > 0");
$pending_payments_result = $pending_payments_query->fetch_assoc();
$pending_payments_count = $pending_payments_result['pending_count'];
$pending_payments_amount = $pending_payments_result['pending_amount'];

include 'includes/admin_header.php';
?>

                <div class="container-fluid mt-4">
                    <!-- Alerts for Quick Add -->
                    <?php if ($activity_success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>Activity added successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($activity_error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Error adding activity. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Quick Add Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Add</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Add Package Quick Link -->
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0"><i class="bi bi-bag-plus me-2"></i> Add New Package</h6>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <p class="flex-grow-1">Create a new travel package with detailed itinerary, schedule, and activities.</p>
                                                    <a href="add_package.php" class="btn btn-success">
                                                        <i class="bi bi-plus-circle me-1"></i> Add Package
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Manage Gallery Quick Link -->
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0"><i class="bi bi-images me-2"></i> Manage Gallery</h6>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <p class="flex-grow-1">Add, edit, or remove images from the homepage gallery.</p>
                                                    <a href="admin_gallery.php" class="btn btn-info">
                                                        <i class="bi bi-gear me-1"></i> Manage Gallery
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Add Activity Form -->
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header bg-warning text-dark">
                                                    <h6 class="mb-0"><i class="bi bi-activity me-2"></i> Add New Activity</h6>
                                                </div>
                                                <div class="card-body">
                                                    <form method="post" enctype="multipart/form-data">
                                                        <div class="mb-2">
                                                            <label class="form-label">Activity Name</label>
                                                            <input type="text" name="activity_name" class="form-control" required>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="activity_description" class="form-control" rows="2"></textarea>
                                                        </div>
                                                        <button type="submit" name="quick_add_activity" class="btn btn-warning w-100">
                                                            <i class="bi bi-plus-circle me-1"></i> Add Activity
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Package Report Quick Link -->
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i> Package Reports</h6>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <p class="flex-grow-1">Generate detailed reports for specific travel packages with bookings and reviews data.</p>
                                                    <a href="admin_package_report.php" class="btn btn-primary">
                                                        <i class="bi bi-bar-chart me-1"></i> View Reports
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row g-4 mb-4">
                        <!-- Total Users -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon text-primary mb-2">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <?php
                                    $select_users = $conn->query("SELECT * FROM register WHERE ROLE='user'");
                                    $number_of_users = $select_users->num_rows;
                                    ?>
                                    <h3 class="mb-1"><?php echo $number_of_users; ?></h3>
                                    <p class="mb-0 text-muted">Users</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Admins -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon text-success mb-2">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <?php
                                    $select_admins = $conn->query("SELECT * FROM register WHERE ROLE='admin'");
                                    $number_of_admins = $select_admins->num_rows;
                                    ?>
                                    <h3 class="mb-1"><?php echo $number_of_admins; ?></h3>
                                    <p class="mb-0 text-muted">Admins</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Accounts -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon text-info mb-2">
                                        <i class="bi bi-person-lines-fill"></i>
                                    </div>
                                    <?php
                                    $select_accounts = $conn->query("SELECT * FROM register");
                                    $number_of_accounts = $select_accounts->num_rows;
                                    ?>
                                    <h3 class="mb-1"><?php echo $number_of_accounts; ?></h3>
                                    <p class="mb-0 text-muted">Accounts</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Bookings -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon text-warning mb-2">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <?php
                                    $select_bookings = $conn->query("SELECT * FROM bookings");
                                    $number_of_bookings = $select_bookings->num_rows;
                                    ?>
                                    <h3 class="mb-1"><?php echo $number_of_bookings; ?></h3>
                                    <p class="mb-0 text-muted">Bookings</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Packages -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon text-danger mb-2">
                                        <i class="bi bi-bag"></i>
                                    </div>
                                    <?php
                                    $select_packages = $conn->query("SELECT * FROM packages");
                                    $number_of_packages = $select_packages->num_rows;
                                    ?>
                                    <h3 class="mb-1"><?php echo $number_of_packages; ?></h3>
                                    <p class="mb-0 text-muted">Packages</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pending Payments -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card stat-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon text-secondary mb-2">
                                        <i class="bi bi-currency-rupee"></i>
                                    </div>
                                    <h3 class="mb-1"><?php echo $pending_payments_count; ?></h3>
                                    <p class="mb-0 text-muted">Pending Payments</p>
                                    <small class="text-muted">₹<?php echo number_format($pending_payments_amount ?? 0, 2); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Users Table -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Users</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped recent-table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $users = $conn->query("SELECT * FROM register ORDER BY user_id DESC LIMIT 5");
                                                while($user = $users->fetch_assoc()) {
                                                    echo "<tr>
                                                            <td>{$user['user_id']}</td>
                                                            <td>{$user['name']}</td>
                                                            <td>{$user['email']}</td>
                                                            <td>{$user['ROLE']}</td>
                                                        </tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Bookings Table -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Bookings</h5>
                                    <a href="manage_bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped recent-table">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>User</th>
                                                    <th>