<?php
// Check if session is already started to avoid "session already active" notice
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['USER_ID'])) {
    echo '<div class="alert alert-danger">You must be logged in to view booking details.</div>';
    exit();
}

$user_id = $_SESSION['USER_ID'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch booking details with package information
$booking_query = "SELECT b.*, p.name as package_name, p.destination as destination_name, p.image as package_image, p.duration, p.accommodation, p.food_type, p.transportation, p.pickup_point, p.dropoff_point, p.start_date, p.end_date, p.daily_start_time, p.daily_end_time
                  FROM bookings b 
                  JOIN packages p ON b.package_id = p.package_id 
                  WHERE b.booking_id = '$booking_id' AND b.user_id = '$user_id'";

$booking_result = mysqli_query($conn, $booking_query);

if (mysqli_num_rows($booking_result) == 0) {
    echo '<div class="alert alert-danger">Booking not found or you do not have permission to view this booking.</div>';
    exit();
}

$booking = mysqli_fetch_assoc($booking_result);
?>

<div class="booking-detail-card">
    <div class="detail-header text-center">
        <h3>Booking #<?php echo $booking['booking_id']; ?></h3>
        <p class="mb-0"><?php echo $booking['package_name']; ?> - <?php echo $booking['destination_name']; ?></p>
    </div>
    
    <div class="detail-body">
        <?php if (!empty($booking['package_image'])): ?>
            <img src="uploads/<?php echo $booking['package_image']; ?>" alt="<?php echo $booking['package_name']; ?>" class="package-image">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <h5>Booking Information</h5>
                <div class="detail-item">
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value"><?php echo $booking['booking_id']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value"><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="badge <?php 
                            echo $booking['status'] == 'Confirmed' ? 'bg-success' : 
                                 ($booking['status'] == 'Pending' ? 'bg-warning' : 
                                 ($booking['status'] == 'Cancelled' ? 'bg-danger' : 'bg-secondary')); 
                        ?>">
                            <?php echo $booking['status']; ?>
                        </span>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Age:</div>
                    <div class="detail-value"><?php echo $booking['age']; ?> years</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Aadhaar Number:</div>
                    <div class="detail-value"><?php echo $booking['aadhaar_no']; ?></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <h5>Payment Details</h5>
                <div class="detail-item">
                    <div class="detail-label">Payment Type:</div>
                    <div class="detail-value"><?php echo ucfirst($booking['payment_type']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Total Amount:</div>
                    <div class="detail-value">Rs. <?php echo number_format($booking['total_amount'], 2); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Paid Amount:</div>
                    <div class="detail-value">Rs. <?php echo number_format($booking['advance_amount'], 2); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Remaining Amount:</div>
                    <div class="detail-value">Rs. <?php echo number_format($booking['remaining_amount'], 2); ?></div>
                </div>
                
                <?php if ($booking['payment_type'] == 'advance' && $booking['remaining_amount'] > 0): ?>
                    <div class="detail-item">
                        <div class="detail-label">Payment Status:</div>
                        <div class="detail-value">
                            <span class="badge bg-warning">Half Paid</span>
                            <br>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                You need to pay the remaining amount of Rs. <?php echo number_format($booking['remaining_amount'], 2); ?>
                            </small>
                        </div>
                    </div>
                <?php elseif ($booking['payment_type'] == 'full'): ?>
                    <div class="detail-item">
                        <div class="detail-label">Payment Status:</div>
                        <div class="detail-value">
                            <span class="badge bg-success">Fully Paid</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h5>Package Information</h5>
                <div class="detail-item">
                    <div class="detail-label">Package Name:</div>
                    <div class="detail-value"><?php echo $booking['package_name']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Destination:</div>
                    <div class="detail-value"><?php echo $booking['destination_name']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Duration:</div>
                    <div class="detail-value"><?php echo $booking['duration']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Accommodation:</div>
                    <div class="detail-value"><?php echo $booking['accommodation']; ?></div>
                </div>
                
                <?php if ($booking['start_date']): ?>
                <div class="detail-item">
                    <div class="detail-label">Start Date:</div>
                    <div class="detail-value"><?php echo date('d M Y', strtotime($booking['start_date'])); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['end_date']): ?>
                <div class="detail-item">
                    <div class="detail-label">End Date:</div>
                    <div class="detail-value"><?php echo date('d M Y', strtotime($booking['end_date'])); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['daily_start_time']): ?>
                <div class="detail-item">
                    <div class="detail-label">Daily Start Time:</div>
                    <div class="detail-value"><?php echo date('g:i A', strtotime($booking['daily_start_time'])); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['daily_end_time']): ?>
                <div class="detail-item">
                    <div class="detail-label">Daily End Time:</div>
                    <div class="detail-value"><?php echo date('g:i A', strtotime($booking['daily_end_time'])); ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <h5>Travel Information</h5>
                <div class="detail-item">
                    <div class="detail-label">Food Type:</div>
                    <div class="detail-value"><?php echo $booking['food_type']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Transportation:</div>
                    <div class="detail-value"><?php echo $booking['transportation']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Pickup Point:</div>
                    <div class="detail-value"><?php echo $booking['pickup_point']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Dropoff Point:</div>
                    <div class="detail-value"><?php echo $booking['dropoff_point']; ?></div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($booking['special_requests'])): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h5>Special Requests</h5>
                <div class="alert alert-info">
                    <?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Cancellation button for eligible bookings -->
        <?php if ($booking['status'] == 'Pending' || $booking['status'] == 'Confirmed'): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="bi bi-exclamation-triangle"></i> Cancel Booking</h5>
                    <p>Are you sure you want to cancel this booking? A refund of Rs. <?php echo number_format($booking['advance_amount'], 2); ?> will be processed to your original payment method within 7-10 business days.</p>
                    <form method="post" action="cancel_booking.php" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php elseif ($booking['status'] == 'Cancelled'): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Booking Cancelled</h5>
                    <p>This booking has been cancelled. A refund of Rs. <?php echo number_format($booking['advance_amount'], 2); ?> will be processed to your original payment method within 7-10 business days.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
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

/* Cancellation alert styling */
.alert-warning {
    border-left: 4px solid #ffc107;
    background-color: #fff3cd;
    border-radius: 8px;
}

.alert-info {
    border-left: 4px solid #17a2b8;
    background-color: #d1ecf1;
    border-radius: 8px;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}
</style>