<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Verify the booking belongs to the user and has remaining amount
$booking_query = "SELECT b.*, p.name as package_name, p.destination as destination_name
                  FROM bookings b 
                  JOIN packages p ON b.package_id = p.package_id 
                  WHERE b.booking_id = '$booking_id' AND b.user_id = '$user_id' AND b.payment_type = 'advance' AND b.remaining_amount > 0";

$booking_result = mysqli_query($conn, $booking_query);

if (mysqli_num_rows($booking_result) == 0) {
    header("Location: bookings.php");
    exit();
}

$booking = mysqli_fetch_assoc($booking_result);

// Handle payment submission
if (isset($_POST['complete_payment'])) {
    // Check if payment has already been processed to prevent duplicate submissions
    if (isset($_SESSION['payment_processed']) && $_SESSION['payment_processed']) {
        // Redirect to bookings page to prevent resubmission
        header("Location: bookings.php?payment_success=1");
        exit();
    }
    
    // In a real application, you would integrate with a payment gateway here
    // For this example, we'll just update the booking status
    
    // Update booking to mark as fully paid
    $update = "UPDATE bookings SET payment_type='full', remaining_amount=0.00 WHERE booking_id='$booking_id'";
    if (mysqli_query($conn, $update)) {
        // Set session flag to prevent duplicate submissions
        $_SESSION['payment_processed'] = true;
        
        // Send notification to user
        $package_name = mysqli_real_escape_string($conn, $booking['package_name']);
        $title = "Payment Completed";
        $message = "Your payment for booking #{$booking_id} for '$package_name' has been completed successfully. Your booking is now fully paid.";
        
        // Escape title and message for SQL insertion
        $title_escaped = mysqli_real_escape_string($conn, $title);
        $message_escaped = mysqli_real_escape_string($conn, $message);
        
        // Insert notification
        $package_id = $booking['package_id'];
        $insert_notification = "INSERT INTO notifications (package_id, title, message, type) 
                              VALUES ('$package_id', '$title_escaped', '$message_escaped', 'success')";
        if(mysqli_query($conn, $insert_notification)) {
            $notification_id = mysqli_insert_id($conn);
            // Create user notification
            $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) 
                                       VALUES ('$user_id', '$notification_id')";
            mysqli_query($conn, $insert_user_notification);
        }
        
        // Redirect with success message
        header("Location: bookings.php?payment_success=1");
        exit();
    } else {
        $error = "Error processing payment. Please try again.";
    }
} else {
    // Clear the payment processed flag when loading the page normally
    unset($_SESSION['payment_processed']);
}

$page_title = "Pay Remaining Amount - Changatham";
include 'includes/dashboard_header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Pay Remaining Amount</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="booking-summary mb-4">
                        <h4>Booking Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Booking ID:</strong> #<?php echo $booking['booking_id']; ?></p>
                                <p><strong>Package:</strong> <?php echo $booking['package_name']; ?></p>
                                <p><strong>Destination:</strong> <?php echo $booking['destination_name']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Amount:</strong> Rs. <?php echo number_format($booking['total_amount'], 2); ?></p>
                                <p><strong>Already Paid:</strong> Rs. <?php echo number_format($booking['advance_amount'], 2); ?></p>
                                <p><strong>Remaining Amount:</strong> <span class="text-danger">Rs. <?php echo number_format($booking['remaining_amount'], 2); ?></span></p>
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
                        
                        <form method="post" onsubmit="return confirm('Are you sure you want to pay Rs. <?php echo number_format($booking['remaining_amount'], 2); ?>?')">
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                </label>
                            </div>
                            
                            <button type="submit" name="complete_payment" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-currency-rupee"></i> Pay Rs. <?php echo number_format($booking['remaining_amount'], 2); ?>
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
                <h4>Payment Terms</h4>
                <ul>
                    <li>All payments are non-refundable unless otherwise specified.</li>
                    <li>Payments must be completed before the trip start date.</li>
                    <li>Failure to complete payment may result in booking cancellation.</li>
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