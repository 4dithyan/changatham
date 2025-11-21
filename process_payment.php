<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];

// Get booking data from session or POST
if (isset($_SESSION['booking_data'])) {
    $booking_data = $_SESSION['booking_data'];
} else {
    header("Location: packages.php");
    exit();
}

$package_id = $booking_data['package_id'];
$age = $booking_data['age'];
$aadhaar_no = $booking_data['aadhaar_no'];
$payment_type = $booking_data['payment_type'];
$special_requests = $booking_data['special_requests'];

// Fetch package details
$package_query = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");
if(mysqli_num_rows($package_query) == 0){
    header("Location: packages.php");
    exit();
}
$package = mysqli_fetch_assoc($package_query);

// Check if user already has an active booking
$user_has_active_booking = false;
$active_booking_result = mysqli_query($conn, "SELECT * FROM bookings WHERE user_id='$user_id' AND status IN ('Pending', 'Confirmed', 'Scheduled')");
if(mysqli_num_rows($active_booking_result) > 0) {
    $user_has_active_booking = true;
}

// Check if booking is allowed based on date
$booking_allowed = true;
if (!empty($package['start_date'])) {
    $start_date = new DateTime($package['start_date']);
    $current_date = new DateTime();
    
    // For regular users, stop booking 1 day before the trip
    if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
        $booking_deadline = clone $start_date;
        $booking_deadline->sub(new DateInterval('P1D')); // 1 day before start date
        
        if ($current_date > $booking_deadline) {
            $booking_allowed = false;
        }
    }
    // For admins, allow booking until the start date
    else {
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Set to beginning of day for comparison
        
        if ($start_date < $today) {
            $booking_allowed = false;
        }
    }
}

// If user already has an active booking or booking is not allowed, redirect
if ($user_has_active_booking || !$booking_allowed) {
    // Clear the booking data processed flag
    unset($_SESSION['booking_data_processed']);
    header("Location: book_package.php?id=" . $package_id);
    exit();
}

// Handle payment submission
if (isset($_POST['complete_payment'])) {
    // Check if payment has already been processed to prevent duplicate submissions
    if (isset($_SESSION['payment_processed']) && $_SESSION['payment_processed']) {
        // Redirect to bookings page to prevent resubmission
        header("Location: bookings.php?payment_success=1");
        exit();
    }
    
    // In a real application, you would integrate with a payment gateway here
    // For this example, we'll just create the booking
    
    // Calculate amounts
    $total_amount = $package['price'];
    if ($payment_type == 'advance') {
        $advance_amount = $total_amount * 0.5;
    } else {
        $advance_amount = $total_amount;
    }
    $remaining_amount = $total_amount - $advance_amount;
    
    // Insert booking
    $booking_date = date('Y-m-d H:i:s');
    $insert = "INSERT INTO bookings (user_id, package_id, age, aadhaar_no, total_amount, advance_amount, remaining_amount, payment_type, special_requests, booking_date) 
               VALUES ('$user_id', '$package_id', '$age', '$aadhaar_no', '$total_amount', '$advance_amount', '$remaining_amount', '$payment_type', '$special_requests', '$booking_date')";
    
    if (mysqli_query($conn, $insert)) {
        // Set session flag to prevent duplicate submissions
        $_SESSION['payment_processed'] = true;
        
        // Clear the booking data processed flag
        unset($_SESSION['booking_data_processed']);
        
        // Update available slots
        $update_slots = "UPDATE packages SET available_slots = available_slots - 1 WHERE package_id = '$package_id'";
        mysqli_query($conn, $update_slots);
        
        // Create notification for the user
        $title = "Booking Confirmed";
        $message = "Your booking for package '{$package['name']}' has been confirmed.";
        $type = "info";
        
        $insert_notification = "INSERT INTO notifications (package_id, title, message, type) VALUES ('$package_id', '$title', '$message', '$type')";
        if (mysqli_query($conn, $insert_notification)) {
            $notification_id = mysqli_insert_id($conn);
            $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) VALUES ('$user_id', '$notification_id')";
            mysqli_query($conn, $insert_user_notification);
        }
        
        // Clear booking data from session
        unset($_SESSION['booking_data']);
        
        // Redirect to bookings page with success message
        header("Location: bookings.php?payment_success=1");
        exit();
    } else {
        $error = "Error processing payment. Please try again.";
    }
} else {
    // Clear the payment processed flag when loading the page normally
    unset($_SESSION['payment_processed']);
}

$page_title = "Process Payment - Changatham";
include 'includes/dashboard_header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Complete Your Payment</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="booking-summary mb-4">
                        <h4>Booking Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Package:</strong> <?php echo $package['name']; ?></p>
                                <p><strong>Destination:</strong> <?php echo $package['destination']; ?></p>
                                <p><strong>Duration:</strong> <?php echo $package['duration']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Amount:</strong> Rs. <?php echo number_format($package['price'], 2); ?></p>
                                <?php if ($payment_type == 'advance'): ?>
                                    <p><strong>Advance Amount (50%):</strong> <span class="text-success">Rs. <?php echo number_format($package['price'] * 0.5, 2); ?></span></p>
                                <?php else: ?>
                                    <p><strong>Full Payment:</strong> <span class="text-success">Rs. <?php echo number_format($package['price'], 2); ?></span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-options">
                        <h4>Payment Method</h4>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> In a real application, this would integrate with a payment gateway like Razorpay, Stripe, etc.
                        </div>
                        
                        <div class="payment-methods mb-4">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card payment-card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-credit-card" style="font-size: 2rem;"></i>
                                            <h5>Credit/Debit Card</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card payment-card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-wallet" style="font-size: 2rem;"></i>
                                            <h5>Net Banking</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card payment-card">
                                        <div class="card-body text-center">
                                            <i class="bi bi-phone" style="font-size: 2rem;"></i>
                                            <h5>UPI</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form method="post" onsubmit="return confirm('Are you sure you want to pay <?php echo ($payment_type == 'advance' ? 'Rs. ' . number_format($package['price'] * 0.5, 2) : 'Rs. ' . number_format($package['price'], 2)); ?> for this booking?')">
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                </label>
                            </div>
                            
                            <button type="submit" name="complete_payment" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-currency-rupee"></i> Pay <?php echo ($payment_type == 'advance' ? 'Rs. ' . number_format($package['price'] * 0.5, 2) : 'Rs. ' . number_format($package['price'], 2)); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>Booking Terms</h4>
                <ul>
                    <li>All bookings are subject to availability.</li>
                    <li>Bookings are non-transferable.</li>
                    <li>Bookings must be made at least 24 hours before the trip start date.</li>
                    <li>Failure to complete payment may result in booking cancellation.</li>
                </ul>
                
                <h4>Payment Terms</h4>
                <ul>
                    <li>All payments are non-refundable unless otherwise specified.</li>
                    <li>Payments must be completed before the trip start date.</li>
                    <li>Payment processing fees may apply.</li>
                </ul>
                
                <h4>Refund Policy</h4>
                <ul>
                    <li>Refunds are subject to our refund policy.</li>
                    <li>Requests for refunds must be made in writing.</li>
                    <li>Processing time for refunds is 7-14 business days.</li>
                </ul>
                
                <h4>Security</h4>
                <ul>
                    <li>All payments are processed through secure payment gateways.</li>
                    <li>Your payment information is encrypted and protected.</li>
                    <li>We do not store sensitive payment information.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.payment-card {
    cursor: pointer;
    transition: all 0.3s;
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.payment-card.active {
    border: 2px solid #00695c;
    background-color: rgba(0, 105, 92, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add active class to payment cards on click
    const paymentCards = document.querySelectorAll('.payment-card');
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            paymentCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>