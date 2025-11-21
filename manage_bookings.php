<?php
session_start();
include 'config.php'; // Database connection

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Bookings";

// Handle Delete Booking
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    $delete = "DELETE FROM bookings WHERE booking_id='$booking_id'";
    if (mysqli_query($conn, $delete)) {
        $success = "Booking deleted successfully!";
    } else {
        $error = "Error deleting booking: " . mysqli_error($conn);
    }
}

// Handle Update Status
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $update = "UPDATE bookings SET status='$status' WHERE booking_id='$booking_id'";
    if (mysqli_query($conn, $update)) {
        $success = "Booking status updated successfully!";
        
        // Send notification to user when status changes
        $booking_query = "SELECT b.*, u.user_id, u.name as user_name, p.name as package_name 
                         FROM bookings b 
                         JOIN register u ON b.user_id = u.user_id 
                         JOIN packages p ON b.package_id = p.package_id 
                         WHERE b.booking_id='$booking_id'";
        $booking_result = mysqli_query($conn, $booking_query);
        $booking_data = mysqli_fetch_assoc($booking_result);
        
        if($booking_data) {
            $user_id = $booking_data['user_id'];
            $package_name = mysqli_real_escape_string($conn, $booking_data['package_name']);
            $status_escaped = mysqli_real_escape_string($conn, $status);
            $title = "Booking Status Updated";
            $message = "Your booking for '$package_name' has been updated to '$status_escaped'.";
            
            // Escape title and message for SQL insertion
            $title_escaped = mysqli_real_escape_string($conn, $title);
            $message_escaped = mysqli_real_escape_string($conn, $message);
            
            // Insert notification
            $package_id = $booking_data['package_id'];
            $insert_notification = "INSERT INTO notifications (package_id, title, message, type) 
                                  VALUES ('$package_id', '$title_escaped', '$message_escaped', 'info')";
            if(mysqli_query($conn, $insert_notification)) {
                $notification_id = mysqli_insert_id($conn);
                // Create user notification
                $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                           VALUES ('$user_id', '$notification_id')";
                mysqli_query($conn, $insert_user_notification);
            }
            
            // If status is cancelled, send a special cancellation notification
            if ($status == 'Cancelled') {
                $cancel_title = "Booking Cancelled";
                $cancel_message = "Your booking (#{$booking_id}) for '$package_name' has been cancelled. If you have any questions, please contact our support team.";
                $cancel_title_escaped = mysqli_real_escape_string($conn, $cancel_title);
                $cancel_message_escaped = mysqli_real_escape_string($conn, $cancel_message);
                
                $insert_cancel_notification = "INSERT INTO notifications (package_id, title, message, type) 
                                             VALUES ('$package_id', '$cancel_title_escaped', '$cancel_message_escaped', 'danger')";
                if(mysqli_query($conn, $insert_cancel_notification)) {
                    $cancel_notification_id = mysqli_insert_id($conn);
                    // Create user notification
                    $insert_cancel_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                                      VALUES ('$user_id', '$cancel_notification_id')";
                    mysqli_query($conn, $insert_cancel_user_notification);
                }
            }
        }
    } else {
        $error = "Error updating booking status: " . mysqli_error($conn);
    }
}

// Handle Mark as Fully Paid
if (isset($_POST['mark_fully_paid'])) {
    $booking_id = $_POST['booking_id'];
    
    // Update booking to mark as fully paid
    $update = "UPDATE bookings SET payment_type='full', remaining_amount=0.00 WHERE booking_id='$booking_id'";
    if (mysqli_query($conn, $update)) {
        $success = "Booking marked as fully paid successfully!";
        
        // Send notification to user
        $booking_query = "SELECT b.*, u.user_id, u.name as user_name, p.name as package_name 
                         FROM bookings b 
                         JOIN register u ON b.user_id = u.user_id 
                         JOIN packages p ON b.package_id = p.package_id 
                         WHERE b.booking_id='$booking_id'";
        $booking_result = mysqli_query($conn, $booking_query);
        $booking_data = mysqli_fetch_assoc($booking_result);
        
        if($booking_data) {
            $user_id = $booking_data['user_id'];
            $package_name = mysqli_real_escape_string($conn, $booking_data['package_name']);
            $title = "Payment Status Updated";
            $message = "Your booking for '$package_name' has been marked as fully paid.";
            
            // Escape title and message for SQL insertion
            $title_escaped = mysqli_real_escape_string($conn, $title);
            $message_escaped = mysqli_real_escape_string($conn, $message);
            
            // Insert notification
            $package_id = $booking_data['package_id'];
            $insert_notification = "INSERT INTO notifications (package_id, title, message, type) 
                                  VALUES ('$package_id', '$title_escaped', '$message_escaped', 'success')";
            if(mysqli_query($conn, $insert_notification)) {
                $notification_id = mysqli_insert_id($conn);
                // Create user notification
                $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                           VALUES ('$user_id', '$notification_id')";
                mysqli_query($conn, $insert_user_notification);
            }
        }
    } else {
        $error = "Error updating payment status: " . mysqli_error($conn);
    }
}

// Handle Bulk Status Update
if (isset($_POST['bulk_update'])) {
    $booking_ids = $_POST['booking_ids'] ?? [];
    $status = $_POST['bulk_status'];
    
    if (!empty($booking_ids) && !empty($status)) {
        $success_count = 0;
        $error_count = 0;
        
        foreach ($booking_ids as $booking_id) {
            $update = "UPDATE bookings SET status='$status' WHERE booking_id='$booking_id'";
            if (mysqli_query($conn, $update)) {
                $success_count++;
                
                // Send notification to user when status changes
                $booking_query = "SELECT b.*, u.user_id, u.name as user_name, p.name as package_name 
                                 FROM bookings b 
                                 JOIN register u ON b.user_id = u.user_id 
                                 JOIN packages p ON b.package_id = p.package_id 
                                 WHERE b.booking_id='$booking_id'";
                $booking_result = mysqli_query($conn, $booking_query);
                $booking_data = mysqli_fetch_assoc($booking_result);
                
                if($booking_data) {
                    $user_id = $booking_data['user_id'];
                    $package_name = mysqli_real_escape_string($conn, $booking_data['package_name']);
                    $status_escaped = mysqli_real_escape_string($conn, $status);
                    $title = "Booking Status Updated";
                    $message = "Your booking for '$package_name' has been updated to '$status_escaped'.";
                    
                    // Escape title and message for SQL insertion
                    $title_escaped = mysqli_real_escape_string($conn, $title);
                    $message_escaped = mysqli_real_escape_string($conn, $message);
                    
                    // Insert notification
                    $package_id = $booking_data['package_id'];
                    $insert_notification = "INSERT INTO notifications (package_id, title, message, type) 
                                          VALUES ('$package_id', '$title_escaped', '$message_escaped', 'info')";
                    if(mysqli_query($conn, $insert_notification)) {
                        $notification_id = mysqli_insert_id($conn);
                        // Create user notification
                        $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                                   VALUES ('$user_id', '$notification_id')";
                        mysqli_query($conn, $insert_user_notification);
                    }
                    
                    // If status is cancelled, send a special cancellation notification
                    if ($status == 'Cancelled') {
                        $cancel_title = "Booking Cancelled";
                        $cancel_message = "Your booking (#{$booking_id}) for '$package_name' has been cancelled. If you have any questions, please contact our support team.";
                        $cancel_title_escaped = mysqli_real_escape_string($conn, $cancel_title);
                        $cancel_message_escaped = mysqli_real_escape_string($conn, $cancel_message);
                        
                        $insert_cancel_notification = "INSERT INTO notifications (package_id, title, message, type) 
                                                     VALUES ('$package_id', '$cancel_title_escaped', '$cancel_message_escaped', 'danger')";
                        if(mysqli_query($conn, $insert_cancel_notification)) {
                            $cancel_notification_id = mysqli_insert_id($conn);
                            // Create user notification
                            $insert_cancel_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                                              VALUES ('$user_id', '$cancel_notification_id')";
                            mysqli_query($conn, $insert_cancel_user_notification);
                        }
                    }
                }
            } else {
                $error_count++;
            }
        }
        
        if ($success_count > 0) {
            $success = "$success_count booking(s) updated successfully!";
            if ($error_count > 0) {
                $success .= " $error_count booking(s) failed to update.";
            }
        } else {
            $error = "Failed to update bookings.";
        }
    } else {
        $error = "Please select bookings and a status to update.";
    }
}

// Handle Send Package Notification (NEW FEATURE)
if (isset($_POST['send_package_notification'])) {
    $package_id = $_POST['package_id'];
    $notification_type = $_POST['notification_type'];
    $notification_title = mysqli_real_escape_string($conn, $_POST['notification_title']);
    $notification_message = mysqli_real_escape_string($conn, $_POST['notification_message']);
    
    // Insert notification
    $insert_notification = "INSERT INTO notifications (package_id, title, message, type) 
                          VALUES ('$package_id', '$notification_title', '$notification_message', '$notification_type')";
    
    if (mysqli_query($conn, $insert_notification)) {
        $notification_id = mysqli_insert_id($conn);
        
        // Get all users who booked this package
        $bookings_query = "SELECT DISTINCT user_id FROM bookings WHERE package_id = '$package_id' AND status IN ('Pending', 'Confirmed')";
        $bookings_result = mysqli_query($conn, $bookings_query);
        
        $user_count = 0;
        // Create notification records for each user
        while ($booking = mysqli_fetch_assoc($bookings_result)) {
            $user_id = $booking['user_id'];
            $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                       VALUES ('$user_id', '$notification_id')";
            if (mysqli_query($conn, $insert_user_notification)) {
                $user_count++;
            }
        }
        
        $success = "Notification sent successfully to $user_count users who booked this package!";
    } else {
        $error = "Error sending notification: " . mysqli_error($conn);
    }
}

// Fetch all bookings with user and package information, grouped by package
$bookings_query = "SELECT b.*, u.name as user_name, u.email as user_email, p.name as package_name, p.destination as destination_name, p.package_id as package_id
                  FROM bookings b 
                  JOIN register u ON b.user_id = u.user_id 
                  JOIN packages p ON b.package_id = p.package_id 
                  ORDER BY p.name, b.booking_date DESC";

$bookings_result = mysqli_query($conn, $bookings_query);

// Organize bookings by package
$bookings_by_package = [];
while($booking = mysqli_fetch_assoc($bookings_result)) {
    $package_id = $booking['package_id'];
    $package_name = $booking['package_name'];
    
    if (!isset($bookings_by_package[$package_id])) {
        $bookings_by_package[$package_id] = [
            'package_name' => $package_name,
            'bookings' => []
        ];
    }
    
    $bookings_by_package[$package_id]['bookings'][] = $booking;
}

// Fetch statistics for dashboard
$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM register"));
$total_packages = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM packages"));
$total_bookings = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings"));

// Calculate total revenue
$revenue_query = mysqli_query($conn, "SELECT SUM(total_amount) as total_revenue FROM bookings WHERE status='Confirmed'");
$revenue_result = mysqli_fetch_assoc($revenue_query);
$total_revenue = $revenue_result['total_revenue'];

// Count pending payments
$pending_payments_query = mysqli_query($conn, "SELECT COUNT(*) as pending_count, SUM(remaining_amount) as pending_amount FROM bookings WHERE payment_type='advance' AND remaining_amount > 0");
$pending_payments_result = mysqli_fetch_assoc($pending_payments_query);
$pending_payments_count = $pending_payments_result['pending_count'];
$pending_payments_amount = $pending_payments_result['pending_amount'];

include 'includes/admin_header.php';
?>

<!-- Alerts -->
<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2><?php echo $total_users; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Packages</h5>
                        <h2><?php echo $total_packages; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-bag" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Bookings</h5>
                        <h2><?php echo $total_bookings; ?></h2>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-book" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pending Payments</h5>
                        <h2><?php echo $pending_payments_count; ?></h2>
                        <small>₹<?php echo number_format($pending_payments_amount ?? 0, 2); ?></small>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-currency-rupee" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bookings List - Card View -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bookings by Package</h5>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleView">
                <i class="bi bi-list"></i> Toggle View
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($bookings_by_package)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No bookings found</h4>
                <p class="text-muted">There are currently no bookings in the system.</p>
            </div>
        <?php else: ?>
            <div class="row" id="packageCardsContainer">
                <?php foreach ($bookings_by_package as $package_id => $package_data): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100 package-card">
                            <div class="card-header changatham-gradient">
                                <h5 class="mb-0"><?php echo htmlspecialchars($package_data['package_name']); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-info"><?php echo count($package_data['bookings']); ?> booking<?php echo count($package_data['bookings']) > 1 ? 's' : ''; ?></span>
                                    <button class="btn btn-sm btn-outline-warning" type="button" 
                                            data-bs-toggle="modal" data-bs-target="#packageNotificationModal<?php echo $package_id; ?>">
                                        <i class="bi bi-bell"></i> Notify
                                    </button>
                                </div>
                                
                                <!-- Recent bookings (show only 3) -->
                                <div class="recent-bookings">
                                    <?php 
                                    $recent_bookings = array_slice($package_data['bookings'], 0, 3);
                                    foreach ($recent_bookings as $booking): ?>
                                        <div class="booking-item border-bottom pb-2 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <strong>#<?php echo $booking['booking_id']; ?></strong>
                                                <span class="badge <?php 
                                                    echo $booking['status'] == 'Confirmed' ? 'bg-success' : 
                                                         ($booking['status'] == 'Pending' ? 'bg-warning' : 
                                                         ($booking['status'] == 'Cancelled' ? 'bg-danger' : 'bg-secondary'));
                                                ?>"><?php echo $booking['status']; ?></span>
                                            </div>
                                            <div class="small"><?php echo htmlspecialchars($booking['user_name']); ?></div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="small text-muted">₹<?php echo number_format($booking['total_amount'], 2); ?></div>
                                                <div>
                                                    <?php if ($booking['payment_type'] == 'full'): ?>
                                                        <span class="badge bg-success">Fully Paid</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Half Paid</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if ($booking['payment_type'] == 'advance' && $booking['remaining_amount'] > 0): ?>
                                                <div class="mt-1">
                                                    <small class="text-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> 
                                                        Pending: ₹<?php echo number_format($booking['remaining_amount'], 2); ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($package_data['bookings']) > 3): ?>
                                        <div class="text-center mt-2">
                                            <button class="btn btn-sm btn-outline-primary view-all-bookings" 
                                                    data-package-id="<?php echo $package_id; ?>">
                                                View all <?php echo count($package_data['bookings']); ?> bookings
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Individual Package Notification Modal -->
                    <div class="modal fade" id="packageNotificationModal<?php echo $package_id; ?>" tabindex="-1" aria-labelledby="packageNotificationModalLabel<?php echo $package_id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header changatham-gradient">
                                        <h5 class="modal-title" id="packageNotificationModalLabel<?php echo $package_id; ?>">
                                            Notify Users for "<?php echo htmlspecialchars($package_data['package_name']); ?>"
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: none; color: white;"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Notification Type</label>
                                            <select name="notification_type" class="form-select">
                                                <option value="info">Information</option>
                                                <option value="warning">Warning</option>
                                                <option value="danger">Danger/Alert</option>
                                                <option value="success">Success</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="notification_title" class="form-control" placeholder="Notification title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Message</label>
                                            <textarea name="notification_message" class="form-control" rows="4" placeholder="Notification message" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="send_package_notification" class="btn btn-warning">
                                            <i class="bi bi-send me-1"></i>Send Notification
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Detailed View (hidden by default) -->
            <div id="detailedView" class="detailed-view" style="display: none;">
                <?php foreach ($bookings_by_package as $package_id => $package_data): ?>
                    <div class="package-detailed-view" id="package-details-<?php echo $package_id; ?>" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="changatham-primary" style="font-weight: 600;"><?php echo htmlspecialchars($package_data['package_name']); ?> - All Bookings</h4>
                            <button class="btn btn-sm btn-outline-secondary back-to-cards">
                                <i class="bi bi-arrow-left"></i> Back to Cards
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>User</th>
                                        <th>Destination</th>
                                        <th>Booking Date</th>
                                        <th>Total Amount</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($package_data['bookings'] as $booking): ?>
                                        <tr>
                                            <td><strong>#<?php echo $booking['booking_id']; ?></strong></td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php echo htmlspecialchars($booking['user_email']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($booking['destination_name']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                                            <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                            <td>
                                                <?php if ($booking['payment_type'] == 'full'): ?>
                                                    <span class="badge bg-success">Fully Paid</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Half Paid</span>
                                                    <br>
                                                    <small class="text-muted">Adv: ₹<?php echo number_format($booking['advance_amount'], 2); ?></small>
                                                    <br>
                                                    <small class="text-muted">Rem: ₹<?php echo number_format($booking['remaining_amount'], 2); ?></small>
                                                    <?php if ($booking['remaining_amount'] > 0): ?>
                                                        <br>
                                                        <small class="text-warning">
                                                            <i class="bi bi-exclamation-triangle"></i> 
                                                            Pending Payment
                                                        </small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <br>
                                                <span class="badge <?php
                                                    $status_class = '';
                                                    switch($booking['status']) {
                                                        case 'Pending':
                                                            $status_class = 'bg-warning';
                                                            break;
                                                        case 'Confirmed':
                                                            $status_class = 'bg-success';
                                                            break;
                                                        case 'Cancelled':
                                                            $status_class = 'bg-danger';
                                                            break;
                                                        case 'Scheduled':
                                                            $status_class = 'bg-primary';
                                                            break;
                                                        case 'Delayed':
                                                            $status_class = 'bg-secondary';
                                                            break;
                                                        default:
                                                            $status_class = 'bg-info';
                                                    }
                                                    echo $status_class;
                                                ?>"><?php echo $booking['status']; ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Payment Action -->
                                                    <?php if ($booking['payment_type'] != 'full'): ?>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to mark this booking as fully paid?')">
                                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                            <button type="submit" name="mark_fully_paid" class="btn btn-sm btn-outline-success" title="Mark as Fully Paid">
                                                                <i class="bi bi-currency-rupee"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete Button -->
                                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                        <button type="submit" name="delete_booking" class="btn btn-sm btn-outline-danger ms-1" title="Delete Booking">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.package-card {
    transition: transform 0.2s;
    border: 1px solid #dee2e6;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.booking-item:last-child {
    border-bottom: none;
}

.view-all-bookings {
    width: 100%;
}

.detailed-view {
    margin-top: 20px;
}

/* Changatham color scheme */
.changatham-primary {
    color: #00695c;
}

.changatham-secondary {
    color: #004d40;
}

.changatham-accent {
    color: #ffeb3b;
}

.changatham-gradient {
    background: linear-gradient(135deg, #00695c, #004d40);
    color: white;
}
</style>

<script>
// Toggle view functionality
document.addEventListener('DOMContentLoaded', function() {
    const toggleViewBtn = document.getElementById('toggleView');
    const packageCardsContainer = document.getElementById('packageCardsContainer');
    const detailedView = document.getElementById('detailedView');
    const viewAllButtons = document.querySelectorAll('.view-all-bookings');
    const backToCardsButtons = document.querySelectorAll('.back-to-cards');
    
    // View all bookings for a package
    viewAllButtons.forEach(button => {
        button.addEventListener('click', function() {
            const packageId = this.getAttribute('data-package-id');
            
            // Hide all package details
            document.querySelectorAll('.package-detailed-view').forEach(el => {
                el.style.display = 'none';
            });
            
            // Show the specific package details
            document.getElementById('package-details-' + packageId).style.display = 'block';
            
            // Hide cards and show detailed view
            packageCardsContainer.style.display = 'none';
            detailedView.style.display = 'block';
        });
    });
    
    // Back to cards view
    backToCardsButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Hide detailed view and show cards
            detailedView.style.display = 'none';
            packageCardsContainer.style.display = 'flex';
        });
    });
});

// Update status function
function updateStatus(bookingId, status) {
    if (confirm('Are you sure you want to update the status to ' + status + '?')) {
        // Create a form dynamically
        const form = document.createElement('form');
        form.method = 'post';
        form.style.display = 'none';
        
        const bookingIdInput = document.createElement('input');
        bookingIdInput.type = 'hidden';
        bookingIdInput.name = 'booking_id';
        bookingIdInput.value = bookingId;
        form.appendChild(bookingIdInput);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'update_status';
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>