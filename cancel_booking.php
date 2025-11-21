<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;

// Validate booking ownership
$booking_query = "SELECT b.*, p.name as package_name, p.price as package_price, p.package_id as package_id
                  FROM bookings b 
                  JOIN packages p ON b.package_id = p.package_id 
                  WHERE b.booking_id = '$booking_id' AND b.user_id = '$user_id'";

$booking_result = mysqli_query($conn, $booking_query);

if (mysqli_num_rows($booking_result) == 0) {
    $_SESSION['error'] = "Booking not found or you do not have permission to cancel this booking.";
    header("Location: bookings.php");
    exit();
}

$booking = mysqli_fetch_assoc($booking_result);

// Check if booking can be cancelled (only Pending or Confirmed bookings)
if ($booking['status'] != 'Pending' && $booking['status'] != 'Confirmed') {
    $_SESSION['error'] = "This booking cannot be cancelled as it is already " . strtolower($booking['status']) . ".";
    header("Location: bookings.php");
    exit();
}

// Update booking status to Cancelled
$update_query = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$booking_id'";
if (mysqli_query($conn, $update_query)) {
    // Update available slots - increase by 1 when booking is cancelled
    $package_id = $booking['package_id'];
    $update_slots = "UPDATE packages SET available_slots = available_slots + 1 WHERE package_id = '$package_id'";
    mysqli_query($conn, $update_slots);
    
    // Set success message with refund information
    $refund_amount = $booking['advance_amount'];
    $_SESSION['success'] = "Booking #" . $booking_id . " for '" . $booking['package_name'] . "' has been successfully cancelled. A refund of Rs. " . number_format($refund_amount, 2) . " will be processed to your original payment method within 7-10 business days.";
} else {
    $_SESSION['error'] = "Error cancelling booking. Please try again later.";
}

header("Location: bookings.php");
exit();
?>