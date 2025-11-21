<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];
$package_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch package details
$package_query = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");

if(mysqli_num_rows($package_query) == 0){
    die("Package not found.");
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
$booking_message = "";

// Check if the package has a start date
if (!empty($package['start_date'])) {
    $start_date = new DateTime($package['start_date']);
    $current_date = new DateTime();
    
    // For regular users, stop booking 1 day before the trip
    if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
        $booking_deadline = clone $start_date;
        $booking_deadline->sub(new DateInterval('P1D')); // 1 day before start date
        
        if ($current_date > $booking_deadline) {
            $booking_allowed = false;
            $booking_message = "Booking for this package is closed. The trip starts on " . $start_date->format('d M Y') . " and bookings close 1 day before the trip.";
        }
    }
    // For admins, allow booking until the start date
    else {
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Set to beginning of day for comparison
        
        if ($start_date < $today) {
            $booking_allowed = false;
            $booking_message = "This package has already started. The trip started on " . $start_date->format('d M Y') . ".";
        }
    }
}

// Handle booking submission
if (isset($_POST['book_now']) && $booking_allowed) {
    // Check if user already has an active booking
    if ($user_has_active_booking) {
        $error = "You already have an active booking. You can only book one package at a time.";
    } else {
        $age = mysqli_real_escape_string($conn, $_POST['age']);
        $aadhaar_no = mysqli_real_escape_string($conn, $_POST['aadhaar_no']);
        $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
        $special_requests = mysqli_real_escape_string($conn, $_POST['special_requests']);
        
        // Validate age
        if ($age < 18) {
            $error = "You must be at least 18 years old to book this trip.";
        }
        // Validate Aadhaar number
        else if (strlen($aadhaar_no) != 12 || !ctype_digit($aadhaar_no)) {
            $error = "Aadhaar number must be exactly 12 digits.";
        }
        // Validate payment type
        else if (!in_array($payment_type, ['advance', 'full'])) {
            $error = "Invalid payment type selected.";
        }
        else {
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
                $booking_id = mysqli_insert_id($conn); // Get the booking ID
                $success = "Booking confirmed successfully!";
                
                // Update available slots
                $update_slots = "UPDATE packages SET available_slots = available_slots - 1 WHERE package_id = '$package_id'";
                mysqli_query($conn, $update_slots);
                
                // Create notification for the user
                $title = "Booking Confirmed";
                $message = "Your booking for package '" . mysqli_real_escape_string($conn, $package['name']) . "' has been confirmed.";
                $type = "info";
                
                // Escape the values properly to prevent SQL injection
                $title = mysqli_real_escape_string($conn, $title);
                $message = mysqli_real_escape_string($conn, $message);
                $type = mysqli_real_escape_string($conn, $type);
                
                $insert_notification = "INSERT INTO notifications (package_id, title, message, type) VALUES ('$package_id', '$title', '$message', '$type')";
                if (mysqli_query($conn, $insert_notification)) {
                    $notification_id = mysqli_insert_id($conn);
                    $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) VALUES ('$user_id', '$notification_id')";
                    mysqli_query($conn, $insert_user_notification);
                }
                
                // Redirect to payment page if advance payment was selected
                if ($payment_type == 'advance') {
                    header("Location: pay_remaining.php?booking_id=$booking_id");
                    exit();
                }
            } else {
                $error = "Error confirming booking: " . mysqli_error($conn);
            }
        }
    }
}

$page_title = "Book Package: " . $package['name'];
include 'includes/header.php';
?>

<style>
    .booking-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .package-header {
        text-align: center;
        margin-bottom: 30px;
        position: relative;
        padding: 30px;
        background: linear-gradient(135deg, #00695c, #004d40);
        border-radius: 15px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    
    .package-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('uploads/<?php echo $package['image']; ?>') center/cover no-repeat;
        opacity: 0.2;
        z-index: 1;
    }
    
    .package-header-content {
        position: relative;
        z-index: 2;
    }
    
    .package-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        font-family: 'Playfair Display', serif;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .package-price-tag {
        display: inline-block;
        background: #ffeb3b;
        color: #00695c;
        padding: 10px 25px;
        border-radius: 30px;
        font-size: 1.8rem;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        margin: 15px 0;
    }
    
    .main-layout {
        display: flex;
        gap: 30px;
        margin-top: 30px;
    }
    
    .left-column {
        flex: 2;
    }
    
    .right-column {
        flex: 1;
    }
    
    .package-image {
        width: 100%;
        height: 450px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin-bottom: 30px;
        border: 5px solid white;
    }
    
    .package-meta-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e0e0e0;
    }
    
    .meta-card-title {
        font-size: 1.5rem;
        color: #00695c;
        margin-bottom: 20px;
        font-weight: 700;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8f5e9;
    }
    
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .detail-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        border-color: #00695c;
    }
    
    .detail-icon {
        font-size: 1.8rem;
        color: #00695c;
        margin-bottom: 15px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #00695c;
        font-size: 0.9rem;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-value {
        font-size: 1.1rem;
        color: #333;
        margin: 0;
        font-weight: 500;
    }
    
    .package-description {
        margin: 25px 0;
        line-height: 1.8;
        color: #555;
        font-size: 1.1rem;
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        border-left: 5px solid #00695c;
    }
    
    .package-highlights {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 30px;
        border: 1px solid #e0e0e0;
        margin-top: 30px;
    }
    
    .highlights-title {
        font-size: 1.8rem;
        color: #00695c;
        margin-bottom: 25px;
        font-weight: 700;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8f5e9;
        font-family: 'Playfair Display', serif;
    }
    
    .package-highlight {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        background: white;
    }
    
    .package-highlight:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    
    .highlight-header {
        padding: 20px 25px;
        background: linear-gradient(to right, #f8f9fa, #e8f5e9);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #00695c;
        border-bottom: 1px solid #e0e0e0;
        font-size: 1.2rem;
    }
    
    .highlight-header:hover {
        background: linear-gradient(to right, #e8f5e9, #d1e7dd);
    }
    
    .arrow-icon {
        transition: transform 0.4s ease;
        font-size: 1.4rem;
        color: #00695c;
    }
    
    .arrow-icon.rotated {
        transform: rotate(180deg);
    }
    
    .highlight-content {
        padding: 0 25px;
        max-height: 0;
        overflow: hidden;
        transition: all 0.4s ease;
        background-color: white;
    }
    
    .highlight-content.expanded {
        padding: 25px;
        max-height: 1000px;
    }
    
    .highlight-text {
        line-height: 1.8;
        color: #444;
        font-size: 1.05rem;
    }
    
    .highlight-text h6 {
        color: #00695c;
        margin-top: 20px;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .booking-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 30px;
        border: 1px solid #e0e0e0;
        position: sticky;
        top: 120px;
    }
    
    .booking-form-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e9ecef;
    }
    
    .form-title {
        font-size: 1.8rem;
        color: #00695c;
        margin-bottom: 25px;
        font-weight: 700;
        text-align: center;
        font-family: 'Playfair Display', serif;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        font-weight: 600;
        color: #00695c;
        margin-bottom: 10px;
        display: block;
        font-size: 1.1rem;
    }
    
    .form-control {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1.05rem;
        transition: all 0.3s;
        background: white;
    }
    
    .form-control:focus {
        border-color: #00695c;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.15);
    }
    
    .btn-book {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        border: none;
        padding: 16px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 10px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        letter-spacing: 0.5px;
    }
    
    .btn-book:hover {
        background: linear-gradient(135deg, #004d40, #00695c);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
    
    .btn-book:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .alert {
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 1.1rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
    }
    
    .slots-available {
        display: inline-block;
        background: #d4edda;
        color: #155724;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: 15px;
    }
    
    .no-slots {
        display: inline-block;
        background: #f8d7da;
        color: #721c24;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: 15px;
    }
    
    .active-booking-alert {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 1.1rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .active-booking-alert i {
        font-size: 2rem;
        margin-bottom: 15px;
        display: block;
    }
    
    .view-booking-btn {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
    }
    
    .view-booking-btn:hover {
        background: linear-gradient(135deg, #004d40, #00695c);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    @media (max-width: 992px) {
        .main-layout {
            flex-direction: column;
        }
        
        .booking-section {
            position: static;
        }
        
        .package-header {
            padding: 20px;
        }
        
        .package-title {
            font-size: 2rem;
        }
        
        .package-price-tag {
            font-size: 1.5rem;
            padding: 8px 20px;
        }
    }
    
    @media (max-width: 768px) {
        .booking-container {
            padding: 15px;
        }
        
        .package-image {
            height: 300px;
        }
        
        .details-grid {
            grid-template-columns: 1fr;
        }
        
        .package-details,
        .booking-section {
            padding: 20px;
        }
        
        .package-header {
            padding: 20px 15px;
        }
        
        .package-title {
            font-size: 1.8rem;
        }
        
        .package-price-tag {
            font-size: 1.3rem;
        }
        
        .detail-item {
            padding: 15px;
        }
        
        .detail-value {
            font-size: 1rem;
        }
    }
</style>

<div class="booking-container">

    <?php if(isset($success)){ echo '<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i> '.$success.'</div>'; } ?>
    <?php if(isset($error)){ echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> '.$error.'</div>'; } ?>
    
    <?php if($user_has_active_booking): 
        $active_booking = mysqli_fetch_assoc($active_booking_result);
        ?>
        <div class="active-booking-alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h4>You Already Have an Active Booking</h4>
            <p>You can only book one package at a time. Please complete or cancel your current booking before booking a new package.</p>
            <p><strong>Current Booking:</strong> <?php echo $active_booking['booking_id']; ?> - <?php echo $active_booking['status']; ?></p>
            <a href="bookings.php" class="view-booking-btn">
                <i class="bi bi-eye me-1"></i> View Your Booking
            </a>
        </div>
    <?php endif; ?>

    <div class="main-layout">
        <div class="left-column">
            <img src="uploads/<?php echo $package['image']; ?>" alt="<?php echo $package['name']; ?>" class="package-image">

            <div class="package-meta-card">
                <h3 class="meta-card-title">Package Information</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <span class="detail-label">Destination</span>
                        <p class="detail-value"><?php echo $package['destination']; ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-cash"></i>
                        </div>
                        <span class="detail-label">Price</span>
                        <p class="detail-value">Rs. <?php echo number_format($package['price'], 2); ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <span class="detail-label">Duration</span>
                        <p class="detail-value"><?php echo $package['duration']; ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-cup"></i>
                        </div>
                        <span class="detail-label">Food</span>
                        <p class="detail-value"><?php echo $package['food_type']; ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <span class="detail-label">Accommodation</span>
                        <p class="detail-value"><?php echo $package['accommodation']; ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <span class="detail-label">Transportation</span>
                        <p class="detail-value"><?php echo $package['transportation']; ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-geo"></i>
                        </div>
                        <span class="detail-label">Pickup Point</span>
                        <p class="detail-value"><?php echo $package['pickup_point']; ?></p>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <span class="detail-label">Dropoff Point</span>
                        <p class="detail-value"><?php echo $package['dropoff_point']; ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($package['description'])): ?>
            <div class="package-description">
                <h4><i class="bi bi-info-circle me-2"></i> About This Package</h4>
                <p><?php echo $package['description']; ?></p>
            </div>
            <?php endif; ?>

            <!-- Package Highlights with collapsible sections -->
            <?php if(!empty($package['day1_schedule']) || !empty($package['food_menu']) || !empty($package['stay_details']) || !empty($package['transportation_details']) || !empty($package['activities'])): ?>
            <div class="package-highlights">
                <h3 class="highlights-title">Package Details</h3>
                
                <?php if(!empty($package['day1_schedule'])): ?>
                <div class="package-highlight">
                    <div class="highlight-header" onclick="toggleContent('schedule')">
                        <span><i class="bi bi-calendar-week me-2"></i> Itinerary Schedule</span>
                        <i class="bi bi-chevron-down arrow-icon" id="arrow-schedule"></i>
                    </div>
                    <div class="highlight-content" id="content-schedule">
                        <div class="highlight-text">
                            <?php 
                            // Display dynamic schedule based on non-empty days
                            $day_count = 0;
                            for ($i = 1; $i <= 10; $i++) {
                                if (!empty($package["day{$i}_schedule"])) {
                                    $day_count = $i;
                                }
                            }
                            
                            for ($i = 1; $i <= $day_count; $i++) {
                                if (!empty($package["day{$i}_schedule"])) {
                                    echo '<h6 class="mt-4">Day ' . $i . '</h6>';
                                    echo '<p>' . nl2br(htmlspecialchars($package["day{$i}_schedule"])) . '</p>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($package['food_menu'])): ?>
                <div class="package-highlight">
                    <div class="highlight-header" onclick="toggleContent('food')">
                        <span><i class="bi bi-egg-fried me-2"></i> Food Menu</span>
                        <i class="bi bi-chevron-down arrow-icon" id="arrow-food"></i>
                    </div>
                    <div class="highlight-content" id="content-food">
                        <div class="highlight-text">
                            <?php echo nl2br(htmlspecialchars($package['food_menu'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($package['stay_details'])): ?>
                <div class="package-highlight">
                    <div class="highlight-header" onclick="toggleContent('stay')">
                        <span><i class="bi bi-house-door me-2"></i> Stay Details</span>
                        <i class="bi bi-chevron-down arrow-icon" id="arrow-stay"></i>
                    </div>
                    <div class="highlight-content" id="content-stay">
                        <div class="highlight-text">
                            <?php echo nl2br(htmlspecialchars($package['stay_details'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($package['transportation_details'])): ?>
                <div class="package-highlight">
                    <div class="highlight-header" onclick="toggleContent('transport')">
                        <span><i class="bi bi-bus me-2"></i> Transportation Details</span>
                        <i class="bi bi-chevron-down arrow-icon" id="arrow-transport"></i>
                    </div>
                    <div class="highlight-content" id="content-transport">
                        <div class="highlight-text">
                            <?php echo nl2br(htmlspecialchars($package['transportation_details'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($package['activities'])): ?>
                <div class="package-highlight">
                    <div class="highlight-header" onclick="toggleContent('activities')">
                        <span><i class="bi bi-activity me-2"></i> Activities</span>
                        <i class="bi bi-chevron-down arrow-icon" id="arrow-activities"></i>
                    </div>
                    <div class="highlight-content" id="content-activities">
                        <div class="highlight-text">
                            <?php echo nl2br(htmlspecialchars($package['activities'])); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <!-- End Package Highlights -->
        </div>
        
        <div class="right-column">
            <div class="booking-section">
                <div class="booking-form-card">
                    <h3 class="form-title">Booking Information</h3>
                    
                    <?php if($user_has_active_booking): ?>
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <h5>You Already Have an Active Booking</h5>
                            <p>You can only book one package at a time. Please complete or cancel your current booking before booking a new package.</p>
                            <a href="bookings.php" class="view-booking-btn">
                                <i class="bi bi-eye me-1"></i> View Your Booking
                            </a>
                        </div>
                    <?php elseif(!$booking_allowed): ?>
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <h5>Booking Not Available</h5>
                            <p><?php echo $booking_message; ?></p>
                        </div>
                    <?php elseif($package['available_slots'] > 0): ?>
                    <form method="post">
                        <div class="form-group">
                            <label class="form-label" for="age"><i class="bi bi-person me-2"></i> Age <span class="text-danger">*</span></label>
                            <input type="number" name="age" id="age" class="form-control" required min="18" max="120" placeholder="Enter your age (minimum 18)">
                            <small class="form-text text-muted">You must be at least 18 years old to book this trip.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="aadhaar_no"><i class="bi bi-card-text me-2"></i> Aadhaar Number <span class="text-danger">*</span></label>
                            <input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control" maxlength="12" required placeholder="Enter 12-digit Aadhaar number">
                            <small class="form-text text-muted">Aadhaar number is mandatory and must be exactly 12 digits.</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="payment_type"><i class="bi bi-wallet2 me-2"></i> Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-control" required>
                                <option value="">Select payment option</option>
                                <option value="advance">Advance Payment (50% - Rs. <?php echo number_format($package['price'] * 0.5, 2); ?>)</option>
                                <option value="full">Full Payment (100% - Rs. <?php echo number_format($package['price'], 2); ?>)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="special_requests"><i class="bi bi-chat-square-text me-2"></i> Special Requests</label>
                            <textarea name="special_requests" id="special_requests" class="form-control" placeholder="Any special requests or requirements..." rows="4"></textarea>
                            <small class="form-text text-muted">This field is optional</small>
                        </div>
                        
                        <button type="submit" name="book_now" class="btn-book">
                            <i class="bi bi-calendar-check me-2"></i> Confirm Booking
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <h5>Sorry, no available slots for this package.</h5>
                            <p>Please check other available packages or contact our support team for more information.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleContent(sectionId) {
        const content = document.getElementById('content-' + sectionId);
        const arrow = document.getElementById('arrow-' + sectionId);
        
        if (content.classList.contains('expanded')) {
            content.classList.remove('expanded');
            arrow.classList.remove('rotated');
        } else {
            content.classList.add('expanded');
            arrow.classList.add('rotated');
        }
    }
    
    // Auto-expand the schedule section by default
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleContent = document.getElementById('content-schedule');
        const scheduleArrow = document.getElementById('arrow-schedule');
        
        if (scheduleContent && scheduleArrow) {
            scheduleContent.classList.add('expanded');
            scheduleArrow.classList.add('rotated');
        }
        
        // Add form validation
        const bookingForm = document.querySelector('form');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                const ageInput = document.getElementById('age');
                const aadhaarInput = document.getElementById('aadhaar_no');
                
                // Validate age
                const age = parseInt(ageInput.value);
                if (age < 18) {
                    e.preventDefault();
                    alert('You must be at least 18 years old to book this trip.');
                    ageInput.focus();
                    return false;
                }
                
                // Validate Aadhaar number
                const aadhaar = aadhaarInput.value.trim();
                if (aadhaar.length !== 12 || !/^\d{12}$/.test(aadhaar)) {
                    e.preventDefault();
                    alert('Aadhaar number must be exactly 12 digits.');
                    aadhaarInput.focus();
                    return false;
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>