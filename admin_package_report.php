<?php
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Package Report - Changatham Admin";
include 'includes/admin_header.php';

// Get package ID from URL parameter
$package_id = isset($_GET['package_id']) ? intval($_GET['package_id']) : 0;

// Fetch package details
$package = null;
$bookings = [];
$reviews = [];
$total_revenue = 0;
$total_bookings = 0;

if ($package_id > 0) {
    // Get package information
    $package_query = "SELECT * FROM packages WHERE package_id = $package_id";
    $package_result = $conn->query($package_query);
    if ($package_result && $package_result->num_rows > 0) {
        $package = $package_result->fetch_assoc();
        
        // Get bookings for this package
        $bookings_query = "SELECT b.*, r.name as user_name, r.email 
                          FROM bookings b 
                          JOIN register r ON b.user_id = r.user_id 
                          WHERE b.package_id = $package_id 
                          ORDER BY b.created_at DESC";
        $bookings_result = $conn->query($bookings_query);
        if ($bookings_result) {
            while ($booking = $bookings_result->fetch_assoc()) {
                $bookings[] = $booking;
                if ($booking['status'] !== 'Cancelled') {
                    $total_revenue += $booking['total_amount'];
                }
            }
            $total_bookings = count($bookings);
        }
        
        // Get reviews for this package
        $reviews_query = "SELECT rev.*, r.name as user_name 
                         FROM reviews rev 
                         JOIN register r ON rev.user_id = r.user_id 
                         WHERE rev.package_id = $package_id 
                         ORDER BY rev.created_at DESC";
        $reviews_result = $conn->query($reviews_query);
        if ($reviews_result) {
            while ($review = $reviews_result->fetch_assoc()) {
                $reviews[] = $review;
            }
        }
    }
}
?>

<style>
    .report-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .report-header {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .report-header h1 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 10px;
    }
    
    .report-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .report-section-title {
        color: #00695c;
        font-family: 'Playfair Display', serif;
        border-bottom: 2px solid #00695c;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #e0f2f1, #b2dfdb);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #00695c;
    }
    
    .stat-label {
        font-size: 1rem;
        color: #004d40;
    }
    
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .table th {
        background-color: #00695c;
        color: white;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 105, 92, 0.05);
    }
    
    .print-button {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 30px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .print-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .no-data {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    
    .rating-stars {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background: white;
            padding: 20px;
        }
        
        .report-container {
            box-shadow: none;
            margin: 0;
            padding: 0;
        }
        
        .report-header {
            box-shadow: none;
            border-radius: 0;
        }
    }
</style>

<div class="report-container">
    <div class="report-header">
        <h1><i class="bi bi-file-earmark-bar-graph me-2"></i> Package Report</h1>
        <p>Generate detailed reports for specific travel packages</p>
    </div>
    
    <?php if ($package_id == 0): ?>
        <!-- Package selection form -->
        <div class="report-card">
            <h3 class="report-section-title">Select Package</h3>
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <label for="package_id" class="form-label">Choose Package</label>
                    <select name="package_id" id="package_id" class="form-control" required>
                        <option value="">Select a package</option>
                        <?php
                        $packages_query = "SELECT package_id, name FROM packages ORDER BY name";
                        $packages_result = $conn->query($packages_query);
                        if ($packages_result) {
                            while ($pkg = $packages_result->fetch_assoc()) {
                                echo '<option value="'.$pkg['package_id'].'">'.htmlspecialchars($pkg['name']).'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <?php if ($package): ?>
            <!-- Report content -->
            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h2>Report for: <?php echo htmlspecialchars($package['name']); ?></h2>
                <button onclick="window.print()" class="print-button">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
            </div>
            
            <!-- Package Details -->
            <div class="report-card">
                <h3 class="report-section-title">Package Information</h3>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($package['name'] ?? $package['package_name'] ?? ''); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($package['location'] ?? $package['destination'] ?? ''); ?></p>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($package['duration'] ?? $package['no_of_days'] ?? ''); ?> days</p>
                        <p><strong>Price:</strong> ₹<?php echo number_format($package['price'] ?? $package['cost'] ?? 0, 2); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($package['description'] ?? $package['desc'] ?? ''); ?></p>
                        <p><strong>Inclusions:</strong> <?php echo htmlspecialchars($package['inclusions'] ?? $package['includes'] ?? ''); ?></p>
                        <p><strong>Exclusions:</strong> <?php echo htmlspecialchars($package['exclusions'] ?? $package['excludes'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_bookings; ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₹<?php echo number_format($total_revenue, 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($reviews); ?></div>
                    <div class="stat-label">Total Reviews</div>
                </div>
                <div class="stat-card">
                    <?php 
                    $avg_rating = 0;
                    if (count($reviews) > 0) {
                        $sum_ratings = 0;
                        foreach ($reviews as $review) {
                            $sum_ratings += $review['rating'];
                        }
                        $avg_rating = round($sum_ratings / count($reviews), 1);
                    }
                    ?>
                    <div class="stat-value"><?php echo $avg_rating; ?>/5</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
            
            <!-- Bookings Table -->
            <div class="report-card">
                <h3 class="report-section-title">Bookings (<?php echo count($bookings); ?>)</h3>
                <?php if (count($bookings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Booking Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Payment Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['booking_id']; ?></td>
                                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                        <td>
                                            <?php if ($booking['status'] == 'Confirmed'): ?>
                                                <span class="badge bg-success">Confirmed</span>
                                            <?php elseif ($booking['status'] == 'Pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                        <td><?php echo ucfirst($booking['payment_type']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                        <h4>No bookings found for this package</h4>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Reviews Table -->
            <div class="report-card">
                <h3 class="report-section-title">Reviews (<?php echo count($reviews); ?>)</h3>
                <?php if (count($reviews) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                        <td>
                                            <div class="rating-stars">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <?php if($i <= $review['rating']): ?>
                                                        <i class="bi bi-star-fill"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                        <h4>No reviews found for this package</h4>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h4>Package Not Found</h4>
                <p>The selected package could not be found. Please select a valid package.</p>
                <a href="?package_id=0" class="btn btn-primary">Select Another Package</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/admin_footer.php'; ?>