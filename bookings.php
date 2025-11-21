<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];

// Handle success message from payment
$success = '';
if (isset($_GET['payment_success']) && $_GET['payment_success'] == 1) {
    $success = "Payment successful! Your booking is now fully paid.";
}

// Handle success/error messages from cancellation
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Fetch user's booking statistics
$bookings_count_query = "SELECT COUNT(*) as total FROM bookings WHERE user_id = '$user_id'";
$bookings_count_result = mysqli_query($conn, $bookings_count_query);
$bookings_count = mysqli_fetch_assoc($bookings_count_result)['total'];

// Fetch user's bookings with package details
$bookings_query = "SELECT b.*, p.name as package_name, p.price as package_price, p.destination as destination_name, p.image as package_image, p.start_date as package_start_date
                   FROM bookings b 
                   JOIN packages p ON b.package_id = p.package_id 
                   WHERE b.user_id = '$user_id' 
                   ORDER BY b.booking_date DESC";

$bookings_result = mysqli_query($conn, $bookings_query);

$page_title = "My Bookings - Changatham";
include 'includes/dashboard_header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="section-title">My Bookings</h1>
            <p class="lead">View and manage your travel bookings. Click on any booking to see detailed information.</p>
        </div>
    </div>
    
    <!-- Alerts -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if(mysqli_num_rows($bookings_result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Package</th>
                        <th>Destination</th>
                        <th>Booking Date</th>
                        <th>Start Date</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Remaining</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = mysqli_fetch_assoc($bookings_result)): ?>
                    <tr class="booking-row" data-booking-id="<?php echo $booking['booking_id']; ?>" style="cursor: pointer;">
                        <td><?php echo $booking['booking_id']; ?></td>
                        <td><?php echo $booking['package_name']; ?></td>
                        <td><?php echo $booking['destination_name']; ?></td>
                        <td><?php echo date('d M Y, H:i', strtotime($booking['booking_date'])); ?></td>
                        <td><?php echo $booking['package_start_date'] ? date('d M Y', strtotime($booking['package_start_date'])) : 'Not specified'; ?></td>
                        <td>Rs. <?php echo number_format($booking['total_amount'], 2); ?></td>
                        <td>Rs. <?php echo number_format($booking['advance_amount'], 2); ?></td>
                        <td>Rs. <?php echo number_format($booking['remaining_amount'], 2); ?></td>
                        <td><?php echo ucfirst($booking['payment_type']); ?></td>
                        <td>
                            <span class="badge <?php 
                                echo $booking['status'] == 'Confirmed' ? 'bg-success' : 
                                     ($booking['status'] == 'Pending' ? 'bg-warning' : 
                                     ($booking['status'] == 'Cancelled' ? 'bg-danger' : 'bg-secondary')); 
                            ?>">
                                <?php echo $booking['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($booking['status'] == 'Cancelled'): ?>
                                <span class="badge bg-danger">Cancelled</span>
                            <?php elseif ($booking['payment_type'] == 'advance' && $booking['remaining_amount'] > 0): ?>
                                <a href="pay_remaining.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-currency-rupee"></i> Pay Remaining
                                </a>
                            <?php elseif ($booking['payment_type'] == 'full'): ?>
                                <span class="badge bg-success">Fully Paid</span>
                            <?php endif; ?>
                            
                            <?php 
                            // Show review button for confirmed bookings
                            if ($booking['status'] == 'Confirmed') {
                                // Check if review already exists for this booking
                                $review_check = $conn->query("SELECT * FROM reviews WHERE booking_id = '{$booking['booking_id']}' AND user_id = '$user_id'");
                                if ($review_check && $review_check->num_rows == 0) {
                                    echo '<a href="add_review.php?booking_id=' . $booking['booking_id'] . '&package_id=' . $booking['package_id'] . '" class="btn btn-sm btn-primary mt-1">';
                                    echo '<i class="bi bi-star"></i> Add Review';
                                    echo '</a>';
                                } else if ($review_check) {
                                    echo '<span class="badge bg-info mt-1">Reviewed</span>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <h4>You haven't made any bookings yet.</h4>
            <p>Start exploring our packages and make your first booking today!</p>
            <a href="packages.php" class="btn btn-modern">Browse Packages</a>
        </div>
    <?php endif; ?>
</div>

<!-- Booking Detail Modal -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingDetailModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookingDetailContent">
                <!-- Booking details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.booking-row:hover {
    background-color: #f8f9fa !important;
}

.booking-detail-card {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.detail-header {
    background: linear-gradient(135deg, #00695c, #004d40);
    color: white;
    padding: 20px;
}

.detail-body {
    padding: 25px;
}

.detail-item {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.detail-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.detail-label {
    font-weight: 600;
    color: #00695c;
    min-width: 150px;
}

.detail-value {
    flex: 1;
}

.package-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event to booking rows
    const bookingRows = document.querySelectorAll('.booking-row');
    bookingRows.forEach(row => {
        row.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');
            showBookingDetails(bookingId);
        });
    });
});

function showBookingDetails(bookingId) {
    // Show loading state
    document.getElementById('bookingDetailContent').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
    modal.show();
    
    // Fetch booking details via AJAX
    fetch('get_booking_details.php?id=' + bookingId)
        .then(response => response.text())
        .then(html => {
            document.getElementById('bookingDetailContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('bookingDetailContent').innerHTML = '<div class="alert alert-danger">Error loading booking details.</div>';
        });
}
</script>

<?php include 'includes/footer.php'; ?>