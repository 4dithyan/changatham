<?php
session_start();
include 'config.php'; // Database connection

$package_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch package details
$package_query = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");

if(mysqli_num_rows($package_query) == 0){
    die("Package not found.");
}
$package = mysqli_fetch_assoc($package_query);

// Check if user has booked this package
$user_has_booked = false;
if(isset($_SESSION['USER_ID'])) {
    $user_id = $_SESSION['USER_ID'];
    $booking_check = mysqli_query($conn, "SELECT * FROM bookings WHERE package_id='$package_id' AND user_id='$user_id' AND status IN ('Pending', 'Confirmed')");
    if(mysqli_num_rows($booking_check) > 0) {
        $user_has_booked = true;
    }
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

$page_title = $package['name'] . " - Changatham";
$additional_css = "
    /* Subtle background image for the entire page */
    body {
        background: url('images/forest.jpg') no-repeat center center fixed;
        background-size: cover;
        background-attachment: fixed;
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        min-height: 100vh;
    }
    
    /* Overlay to ensure content readability */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: -1;
    }
    
    /* Main content container with translucency */
    .main-container {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        margin: 30px auto;
        padding: 30px;
        border: 1px solid rgba(0, 105, 92, 0.1);
    }
    
    .package-header {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('uploads/" . $package['image'] . "');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        color: white;
        padding: 80px 0 50px;
        margin-bottom: 40px;
        position: relative;
        text-align: center;
        overflow: hidden;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        border-radius: 15px;
    }
    
    .package-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }
    
    .package-header-content {
        position: relative;
        z-index: 2;
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .package-header h1 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 2.8rem;
        margin-bottom: 15px;
        text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        letter-spacing: 0.5px;
        color: white;
    }
    
    .package-header h1::after {
        content: '';
        display: block;
        width: 80px;
        height: 3px;
        background: var(--accent);
        margin: 15px auto;
        border-radius: 2px;
    }
    
    .package-header .lead {
        font-size: 1.2rem;
        margin-bottom: 20px;
        text-shadow: 0 1px 4px rgba(0,0,0,0.4);
        line-height: 1.6;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        color: rgba(255, 255, 255, 0.9);
    }
    
    .package-price {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--accent);
        margin-bottom: 15px;
        background: rgba(0, 0, 0, 0.3);
        display: inline-block;
        padding: 12px 30px;
        border-radius: 30px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    .package-price:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        background: rgba(0, 0, 0, 0.4);
    }
    
    /* Adjust layout for booking card - Restored two-column layout */
    .main-content-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -15px;
        gap: 30px;
    }
    
    .content-column {
        flex: 1 1 65%; /* Main content takes up about 65% of the space */
        max-width: 65%;
        padding: 0 15px;
    }
    
    .booking-column {
        flex: 0 0 30%; /* Booking card takes up about 30% of the space */
        max-width: 30%;
        padding: 0 15px;
    }
    
    /* Booking card styling */
    .booking-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(0, 105, 92, 0.15);
    }
    
    .booking-header {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        padding: 20px;
        font-size: 1.5rem;
        font-weight: 600;
        text-align: center;
    }
    
    .booking-body {
        padding: 25px;
    }
    
    .price-box {
        text-align: center;
        margin-bottom: 20px;
        padding: 15px;
        background: rgba(0, 105, 92, 0.05);
        border-radius: 10px;
    }
    
    .price-value {
        font-size: 2rem;
        font-weight: 700;
        color: #00695c;
        margin: 10px 0;
    }
    
    .slots-available {
        text-align: center;
        padding: 10px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 8px;
        margin-bottom: 20px;
        color: #28a745;
        font-weight: 500;
    }
    
    .package-details-list {
        list-style: none;
        padding: 0;
        margin-bottom: 25px;
    }
    
    .package-details-list li {
        padding: 10px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: flex-start;
    }
    
    .package-details-list li:last-child {
        border-bottom: none;
    }
    
    .package-details-list i {
        color: #00695c;
        margin-right: 10px;
        margin-top: 3px;
    }
    
    .btn-book-now {
        width: 100%;
        padding: 12px;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .booking-card {
        width: 100%;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        border: none;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(0, 105, 92, 0.15);
        /* Booking Card Header Style */
        padding: 0;
    }

    .booking-header {
        background-color: var(--primary); /* Use a strong color for the header */
        color: white;
        padding: 15px 20px;
        font-size: 1.3rem;
        font-weight: 700;
        text-align: center;
        border-bottom: 3px solid var(--accent);
        margin-bottom: 0;
    }
    
    .booking-body {
        padding: 20px;
    }
    
    .price-box {
        text-align: center;
        padding: 15px;
        background-color: #fff3cd; /* Light background for the price */
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid #ffeeba;
    }

    .price-box .price-value {
        font-size: 2rem;
        font-weight: 800;
        color: #856404; /* Dark gold/yellow text */
    }

    .slots-available {
        text-align: center;
        padding: 10px;
        background-color: #d4edda; /* Light green background for availability */
        color: #155724;
        border-radius: 10px;
        font-weight: 600;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .package-details-list {
        list-style: none;
        padding: 0;
        margin-bottom: 20px;
    }

    .package-details-list li {
        margin-bottom: 12px;
        padding-left: 20px;
        position: relative;
        font-size: 0.95rem;
        color: #555;
    }

    .package-details-list li i {
        position: absolute;
        left: 0;
        color: var(--primary);
    }

    .btn-book-now {
        width: 100%;
        padding: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        background-color: var(--primary);
        border: none;
        transition: background-color 0.3s ease;
    }
    
    .btn-book-now:hover {
        background-color: var(--secondary);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .package-meta {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-top: 30px;
        margin-bottom: 40px;
        border: 1px solid rgba(0, 105, 92, 0.1);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        backdrop-filter: blur(5px);
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        border-radius: 8px;
        background: rgba(0, 105, 92, 0.05);
        min-width: 180px;
        flex: 1 1 auto;
    }
    
    .meta-item i {
        font-size: 1.8rem;
        color: var(--primary);
        width: 30px;
        text-align: center;
    }
    
    .meta-label {
        font-weight: 600;
        color: var(--secondary);
        margin: 0;
        font-size: 1rem;
        min-width: 100px;
    }
    
    .meta-value {
        font-weight: 500;
        color: #333;
        margin: 0;
        font-size: 1.1rem;
    }
    
    .section-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 40px;
        border: none;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(0, 105, 92, 0.1);
        backdrop-filter: blur(5px);
    }
    
    .section-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .section-header {
        padding: 25px 30px;
        color: white;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-bottom: 0;
        font-size: 1.8rem;
        position: relative;
    }
    
    .section-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 30px;
        width: 60px;
        height: 3px;
        background: var(--accent);
        border-radius: 2px;
    }
    
    .section-body {
        padding: 30px;
    }
    
    /* Dining header styles */
    .dining-header {
        display: flex;
        align-items: center;
    }
    
    .dining-icon {
        font-size: 1.5rem;
    }
    
    .dining-title {
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .day-schedule {
        border-left: 4px solid var(--primary);
        padding-left: 25px;
        margin-bottom: 35px;
        padding-bottom: 20px;
        background: rgba(0, 105, 92, 0.03);
        border-radius: 0 10px 10px 0;
        padding: 25px 25px 25px 35px;
    }
    
    .day-schedule h5 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 1.4rem;
        font-weight: 700;
    }
    
    /* Schedule Item Styling for Hover Effect */
    .schedule-text-container {
        position: relative;
        cursor: pointer;
        padding: 10px 0;
        min-height: 50px; /* Ensure container has enough height */
    }
    
    .itinerary-item {
        position: relative;
        padding-left: 35px;
        font-size: 1.05rem;
        line-height: 1.6;
        margin: 0; /* Remove default margin */
    }

    .itinerary-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 8px; /* Adjust vertical position */
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: var(--accent);
        border: 3px solid var(--primary);
    }
    
    .itinerary-item:not(:last-child):after {
        content: '';
        position: absolute;
        left: 7px;
        top: 24px; /* Adjust vertical position */
        width: 2px;
        height: calc(100% - 16px);
        background-color: #e0e0e0;
    }

    .short-text {
        color: #555;
        transition: opacity 0.3s ease;
        display: block;
        max-height: 60px; /* Limit height to approximately 2 lines */
        overflow: hidden;
    }
    
    .full-text {
        position: absolute;
        top: 0;
        left: 35px;
        right: 0;
        padding-right: 25px;
        color: #111;
        opacity: 0;
        line-height: inherit;
        transition: opacity 0.4s ease, transform 0.4s ease;
        pointer-events: none; /* Allows mouse events to pass through */
        z-index: 10;
        background: rgba(255, 255, 255, 0.95); /* Semi-transparent background for readability */
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-5px); /* Slight lift on hover */
        padding: 15px;
        border: 1px solid #e0e0e0;
    }

    .schedule-text-container:hover .short-text {
        opacity: 0;
    }

    .schedule-text-container:hover .full-text {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto; /* Enable interactions when visible */
    }
    
    /* Add a visual indicator that there's more content */
    .schedule-text-container:not(:hover) .short-text:after {
        content: ' (hover to see more)';
        font-style: italic;
        color: var(--primary);
        font-size: 0.9rem;
    }
    /* End of Schedule Item Styling */
    
    .feature-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        font-size: 1.8rem;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 105, 92, 0.3);
    }
    
    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 105, 92, 0.08), rgba(0, 77, 64, 0.08));
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        border-left: 5px solid var(--primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .gallery-item {
        border-radius: 12px;
        overflow: hidden;
        height: 220px;
        margin-bottom: 25px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .gallery-item:hover img {
        transform: scale(1.08);
    }
    
    .driver-guide-card {
        background: linear-gradient(135deg, rgba(255, 235, 59, 0.15), rgba(255, 235, 59, 0.25));
        border: 1px solid var(--accent);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .driver-guide-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .driver-guide-card h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .driver-guide-card p {
        margin-bottom: 8px;
        color: #555;
    }
    
    .driver-guide-card strong {
        color: var(--primary);
    }
    
    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 105, 92, 0.08), rgba(0, 77, 64, 0.08));
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 5px solid var(--primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .highlight-box h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    /* Alert styling for when user hasn't booked */
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
        border-radius: 10px;
        padding: 25px;
    }
    
    .alert-info h5 {
        color: #0c5460;
        margin-bottom: 15px;
    }
    
    .alert-info p {
        margin-bottom: 20px;
        font-size: 1.05rem;
    }
    
    /* Add visual separation between major sections */
    .section-divider {
        height: 2px;
        background: linear-gradient(to right, transparent, #e0e0e0, transparent);
        margin: 50px 0;
        position: relative;
    }
    
    .section-divider::after {
        content: '•';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        color: var(--primary);
        font-size: 2rem;
        padding: 0 15px;
    }
    
    /* Activities section - Single row layout */
    .activities-container {
        overflow-x: auto;
        padding: 10px 0;
        margin: 0 -15px;
    }
    
    .activities-row {
        display: flex;
        gap: 25px;
        padding: 15px;
        min-width: min-content;
    }
    
    .activity-item-single {
        flex: 0 0 300px;
        transition: all 0.3s ease;
    }
    
    .activity-item-single.activity-hidden {
        display: none;
    }
    
    .activity-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid rgba(0, 105, 92, 0.1);
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .activity-image {
        height: 200px;
        overflow: hidden;
    }
    
    .activity-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .activity-card:hover .activity-image img {
        transform: scale(1.05);
    }
    
    .activity-content {
        padding: 20px;
    }
    
    .activity-content .card-title {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .activity-content .card-text {
        color: #555;
        line-height: 1.6;
        margin-bottom: 0;
    }
    
    /* Improve spacing for activities section */
    .activity-item {
        margin-bottom: 30px;
    }
    
    /* Improve spacing for stay & dine section */
    .stay-dine-section {
        margin-bottom: 35px;
    }
    
    /* Add styling for dining section */
    .dining-section h4 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
        border-bottom: 2px solid rgba(0, 105, 92, 0.1);
        padding-bottom: 8px;
    }
    
    .dining-section p {
        line-height: 1.7;
        color: #555;
        margin-bottom: 0;
    }
    
    /* Add styling for team information section */
    .driver-guide-card {
        background: linear-gradient(135deg, rgba(255, 235, 59, 0.15), rgba(255, 235, 59, 0.25));
        border: 1px solid var(--accent);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .driver-guide-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .driver-guide-card h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .driver-guide-card p {
        margin-bottom: 8px;
        color: #555;
    }
    
    .driver-guide-card strong {
        color: var(--primary);
    }
    
    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 105, 92, 0.08), rgba(0, 77, 64, 0.08));
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 5px solid var(--primary);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .highlight-box h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    /* Alert styling for when user hasn't booked */
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
        border-radius: 10px;
        padding: 25px;
    }
    
    .alert-info h5 {
        color: #0c5460;
        margin-bottom: 15px;
    }
    
    .alert-info p {
        margin-bottom: 20px;
        font-size: 1.05rem;
    }
    
    /* Additional spacing improvements */
    .content-section {
        margin-bottom: 50px;
    }
    
    .section-spacing {
        margin-bottom: 45px;
    }
    
    .sub-section {
        margin-bottom: 35px;
    }
    
    .highlight-content {
        padding: 25px;
        border-radius: 12px;
        background-color: rgba(0, 105, 92, 0.07);
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }
    
    /* Activity card styling */
    .activity-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid rgba(0, 105, 92, 0.1);
        background: rgba(255, 255, 255, 0.9);
    }
    
    .activity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .activity-image {
        height: 200px;
        overflow: hidden;
    }
    
    .activity-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .activity-card:hover .activity-image img {
        transform: scale(1.05);
    }
    
    .activity-content {
        padding: 20px;
    }
    
    .activity-content h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .activity-content p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 0;
    }
    
    /* Locked content styling */
    .locked-content {
        padding: 40px 20px;
    }
    
    .locked-content .feature-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 20px;
        font-size: 3rem;
    }
    
    /* View More/Less Button */
    #view-toggle-activities {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        padding: 12px 25px;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 105, 92, 0.3);
        margin: 20px auto;
        display: block;
    }
    
    #view-toggle-activities:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 105, 92, 0.4);
    }
    
    #view-toggle-activities:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.3);
    }
    
    /* Adjust sticky behavior for better responsiveness */
    @media (min-width: 992px) {
        .booking-card.sticky-top {
            top: calc(70px + 20px); /* Header height + additional spacing */
        }
    }
    
    @media (max-width: 991px) {
        .main-content-row {
            flex-direction: column;
        }
        
        .content-column,
        .booking-column {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .booking-card.sticky-top {
            position: relative;
            top: auto;
        }
    }
    
    @media (max-width: 768px) {
        .package-header {
            padding: 60px 0 30px;
        }
        
        .package-price {
            font-size: 2rem;
        }
        
        .meta-item {
            padding: 15px 5px;
        }
        
        .section-card {
            margin-bottom: 30px;
        }
        
        .day-schedule {
            margin-bottom: 25px;
            padding: 20px 20px 20px 30px;
        }
        
        .highlight-box {
            padding: 20px;
        }
        
        .driver-guide-card {
            padding: 20px;
        }
        
        .content-section {
            margin-bottom: 30px;
        }
        
        .booking-card {
            margin-bottom: 20px;
        }
        
        .section-header {
            padding: 20px 25px;
            font-size: 1.5rem;
        }
        
        .section-body {
            padding: 25px;
        }
        
        .locked-content {
            padding: 30px 15px;
        }
        
        .locked-content .feature-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }
    }
";
include 'includes/header.php';
?>

<div class="package-header">
    <div class="container package-header-content">
        <div class="row">
            <div class="col-12">
                <h1 class="display-3 fw-bold mb-4"><?php echo $package['name']; ?></h1>
                <p class="lead fs-4"><?php echo $package['description']; ?></p>
                <div class="package-price">Rs. <?php echo number_format($package['price'],2); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="container main-container">
    <div class="package-meta">
        <div class="meta-item">
            <i class="bi bi-geo-alt"></i>
            <div>
                <h6 class="meta-label">Destination</h6>
                <p class="meta-value"><?php echo $package['destination']; ?></p>
            </div>
        </div>
        <div class="meta-item">
            <i class="bi bi-clock"></i>
            <div>
                <h6 class="meta-label">Duration</h6>
                <p class="meta-value"><?php echo $package['duration']; ?></p>
            </div>
        </div>
        <div class="meta-item">
            <i class="bi bi-calendar-event"></i>
            <div>
                <h6 class="meta-label">Date</h6>
                <p class="meta-value">
                    <?php 
                    if (!empty($package['start_date'])) {
                        echo date('d M Y', strtotime($package['start_date']));
                        if (!empty($package['end_date'])) {
                            echo ' - ' . date('d M Y', strtotime($package['end_date']));
                        }
                    } else {
                        echo "Not specified";
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="meta-item">
            <i class="bi bi-clock-history"></i>
            <div>
                <h6 class="meta-label">Time</h6>
                <p class="meta-value">
                    <?php 
                    if (!empty($package['daily_start_time']) || !empty($package['daily_end_time'])) {
                        if (!empty($package['daily_start_time'])) {
                            echo date('g:i A', strtotime($package['daily_start_time']));
                        }
                        if (!empty($package['daily_end_time'])) {
                            if (!empty($package['daily_start_time'])) {
                                echo ' - ';
                            }
                            echo date('g:i A', strtotime($package['daily_end_time']));
                        }
                    } else {
                        echo "Not specified";
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="meta-item">
            <i class="bi bi-people"></i>
            <div>
                <h6 class="meta-label">Accommodation</h6>
                <p class="meta-value"><?php echo $package['accommodation']; ?></p>
            </div>
        </div>
        <div class="meta-item">
            <i class="bi bi-utensils"></i>
            <div>
                <h6 class="meta-label">Food</h6>
                <p class="meta-value"><?php echo $package['food_type']; ?></p>
            </div>
        </div>
        <div class="meta-item">
            <i class="bi bi-cash"></i>
            <div>
                <h6 class="meta-label">Price</h6>
                <p class="meta-value">Rs. <?php echo number_format($package['price'],2); ?></p>
            </div>
        </div>
    </div>

    <div class="main-content-row">
        
        <div class="booking-column order-lg-2">
            <div class="card booking-card sticky-top">
                <div class="booking-header">Book This Package</div>
                <div class="booking-body">
                    
                    <?php if (!$booking_allowed): // Booking is closed ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <i class="bi bi-lock-fill me-2"></i> <?php echo $booking_message; ?>
                        </div>
                    <?php else: ?>
                        <div class="price-box">
                            <small class="text-muted d-block">Total Package Cost</small>
                            <div class="price-value">Rs. <?php echo number_format($package['price'],2); ?></div>
                        </div>

                        <?php 
                        $slots_left = $package['available_slots'];
                        if ($slots_left > 0): 
                        ?>
                            <div class="slots-available">
                                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $slots_left; ?> Slots Available
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Fully Booked!
                            </div>
                        <?php endif; ?>

                        <ul class="package-details-list">
                            <li><i class="bi bi-geo-fill"></i> <strong>Pickup Point:</strong> <?php echo htmlspecialchars($package['pickup_point']); ?></li>
                            <?php if (!empty($package['additional_pickups'])): ?>
                                <li><i class="bi bi-geo-alt-fill"></i> <strong>Additional Pickup Points:</strong> <?php echo htmlspecialchars($package['additional_pickups']); ?></li>
                            <?php endif; ?>
                            <li><i class="bi bi-arrow-down-right-square-fill"></i> <strong>Drop-off Point:</strong> <?php echo htmlspecialchars($package['dropoff_point']); ?></li>
                            <li><i class="bi bi-truck-flatbed"></i> <strong>Transportation:</strong> <?php echo htmlspecialchars($package['transportation']); ?></li>
                        </ul>

                        <?php if ($user_has_booked): ?>
                            <button class="btn btn-secondary btn-book-now" disabled><i class="bi bi-bookmark-check me-2"></i> Already Booked</button>
                            <a href="bookings.php" class="btn btn-outline-primary btn-sm mt-2 w-100">View My Booking</a>
                        <?php elseif (isset($_SESSION['EMAIL'])): // User must be logged in to book ?>
                            <a href="book_package.php?id=<?php echo $package_id; ?>" class="btn btn-success btn-book-now">
                                <i class="bi bi-calendar-check me-2"></i> Book Now
                            </a>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i> You'll need to provide your age and Aadhaar number during booking
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center" role="alert">
                                <i class="bi bi-info-circle me-2"></i> Please <a href="login.php">login</a> to book this package
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        
        <div class="content-column order-lg-1">
            <div class="card section-card content-section">
                <div class="section-header bg-primary">
                    <h3 class="mb-0"><i class="bi bi-calendar-week me-3"></i> Schedules</h3>
                </div>
                <div class="section-body">
                    <?php 
                    // Display dynamic schedule based on non-empty days
                    $day_count = 0;
                    for ($i = 1; $i <= 10; $i++) {
                        if (!empty($package["day{$i}_schedule"])) {
                            $day_count = $i;
                        }
                    }
                    
                    if ($day_count > 0) {
                        for ($i = 1; $i <= $day_count; $i++) {
                            if (!empty($package["day{$i}_schedule"])) {
                                $full_text = htmlspecialchars($package["day{$i}_schedule"]);
                                // Split text into lines and take only first 2 lines
                                $lines = preg_split('/\r\n|\r|\n/', $full_text);
                                $short_text = implode("\n", array_slice($lines, 0, 2));
                                $has_more = count($lines) > 2;

                                echo '<div class="day-schedule">';
                                echo '<h5>Day ' . $i . '</h5>';
                                // Apply the hover structure
                                echo '<div class="schedule-text-container">';
                                echo '<p class="itinerary-item short-text">' . nl2br($short_text) . ($has_more ? '...' : '') . '</p>';
                                echo '<p class="itinerary-item full-text">' . nl2br($full_text) . '</p>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                    } else {
                        echo '<p class="text-muted">No detailed schedule available for this package.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="card section-card content-section">
                <div class="section-header bg-info">
                    <h3 class="mb-0"><i class="bi bi-bicycle me-3"></i> Activities</h3>
                </div>
                <div class="section-body">
                    <?php
                    // Fetch activities for this package
                    $activities_query = mysqli_query($conn, "SELECT a.* FROM activities a JOIN package_activities pa ON a.activity_id = pa.activity_id WHERE pa.package_id='$package_id' ORDER BY a.activity_id");
                    $activities_count = mysqli_num_rows($activities_query);
                    
                    if ($activities_count > 0) {
                        echo '<div class="activities-container">';
                        echo '<div class="activities-row">';
                        
                        $activities_displayed = 0;
                        $activities_to_show_initially = 2; // Changed from 3 to 2
                        
                        while ($activity = mysqli_fetch_assoc($activities_query)) {
                            $activities_displayed++;
                            $should_hide = ($activities_displayed > $activities_to_show_initially) ? 'activity-hidden' : '';
                            
                            echo '<div class="activity-item-single ' . $should_hide . '" data-activity-id="' . $activity['activity_id'] . '">';
                            echo '<div class="card activity-card h-100">';
                            
                            if (!empty($activity['image'])) {
                                echo '<div class="activity-image">';
                                echo '<img src="uploads/' . htmlspecialchars($activity['image']) . '" alt="' . htmlspecialchars($activity['name']) . '" class="card-img-top">';
                                echo '</div>';
                            }
                            
                            echo '<div class="activity-content">';
                            echo '<h5 class="card-title">' . htmlspecialchars($activity['name']) . '</h5>';
                            echo '<p class="card-text">' . nl2br(htmlspecialchars($activity['description'])) . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        echo '</div>'; // Close activities-row
                        echo '</div>'; // Close activities-container
                        
                        // Add View More/Less button if there are more than 2 activities
                        if ($activities_count > $activities_to_show_initially) {
                            $remaining_count = $activities_count - $activities_to_show_initially;
                            echo '<div class="text-center mt-4">';
                            echo '<button id="view-toggle-activities" class="btn btn-primary" 
                                  data-state="more" 
                                  data-remaining-count="' . $remaining_count . '">
                                  <span class="toggle-text">View Activities (' . $remaining_count . ' more)</span>
                                  </button>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="text-muted">No activities specified for this package.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="card section-card content-section">
                <div class="section-header bg-warning">
                    <h3 class="mb-0"><i class="bi bi-egg-fried me-3"></i> Dining</h3>
                </div>
                <div class="section-body">
                    <?php 
                    // Check if any meal information is available
                    $has_meal_info = !empty($package['breakfast']) || !empty($package['lunch']) || !empty($package['dinner']);
                    
                    // Show edit button for admins
                    if (isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin') {
                        echo '<div class="mb-3">';
                        echo '<a href="manage_packages.php" class="btn btn-sm btn-primary">';
                        echo '<i class="bi bi-pencil me-1"></i> Edit Dining Details';
                        echo '</a>';
                        echo '</div>';
                    }
                    
                    if ($has_meal_info): ?>
                        <div class="row">
                            <?php if (!empty($package['breakfast'])): ?>
                                <div class="col-md-12 dining-section mb-4">
                                    <h4><i class="bi bi-sunrise me-2"></i> Breakfast</h4>
                                    <p><?php echo nl2br(htmlspecialchars($package['breakfast'])); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($package['lunch'])): ?>
                                <div class="col-md-12 dining-section mb-4">
                                    <h4><i class="bi bi-sun me-2"></i> Lunch</h4>
                                    <p><?php echo nl2br(htmlspecialchars($package['lunch'])); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($package['dinner'])): ?>
                                <div class="col-md-12 dining-section">
                                    <h4><i class="bi bi-moon-stars me-2"></i> Dinner</h4>
                                    <p><?php echo nl2br(htmlspecialchars($package['dinner'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No dining details specified.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card section-card content-section">
                <div class="section-header bg-danger">
                    <h3 class="mb-0"><i class="bi bi-people me-3"></i> Your Team & Transportation</h3>
                </div>
                <div class="section-body">
                    <?php 
                    // Only show this section if user has booked the package
                    if ($user_has_booked):
                        // Fetch assigned buses for this package
                        $buses_query = mysqli_query($conn, "SELECT b.* FROM buses b JOIN package_buses pb ON b.bus_id = pb.bus_id WHERE pb.package_id = '$package_id'");
                        
                        // Fetch assigned drivers for this package
                        $drivers_query = mysqli_query($conn, "SELECT d.* FROM drivers d JOIN package_drivers pd ON d.driver_id = pd.driver_id WHERE pd.package_id = '$package_id'");
                        
                        // Fetch assigned guides for this package
                        $guides_query = mysqli_query($conn, "SELECT g.* FROM guides g JOIN package_guides pg ON g.guide_id = pg.guide_id WHERE pg.package_id = '$package_id'");
                        
                        // Check if any team members or transportation details are available
                        $has_team_info = (mysqli_num_rows($buses_query) > 0 || mysqli_num_rows($drivers_query) > 0 || mysqli_num_rows($guides_query) > 0 || !empty($package['transportation_details']));
                        
                        if ($has_team_info):
                    ?>
                    <div class="row">
                        <?php 
                        // Display guides
                        if (mysqli_num_rows($guides_query) > 0): 
                            while ($guide = mysqli_fetch_assoc($guides_query)): ?>
                                <div class="col-md-6">
                                    <div class="driver-guide-card">
                                        <h5><i class="bi bi-person-badge me-2"></i> Guide</h5>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($guide['name']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($guide['phone']); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; 
                        endif; ?>
                        
                        <?php 
                        // Display drivers
                        if (mysqli_num_rows($drivers_query) > 0): 
                            while ($driver = mysqli_fetch_assoc($drivers_query)): ?>
                                <div class="col-md-6">
                                    <div class="driver-guide-card">
                                        <h5><i class="bi bi-person-circle me-2"></i> Driver</h5>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['name']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($driver['phone']); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; 
                        endif; ?>
                        
                        <?php 
                        // Display buses
                        if (mysqli_num_rows($buses_query) > 0): ?>
                            <div class="col-12 mt-4">
                                <h4><i class="bi bi-bus-front me-2"></i> Transportation Details</h4>
                                <?php while ($bus = mysqli_fetch_assoc($buses_query)): ?>
                                    <div class="highlight-box mb-3">
                                        <h5><?php echo htmlspecialchars($bus['name']); ?></h5>
                                        <p><strong>Type:</strong> <?php echo htmlspecialchars($bus['type']); ?></p>
                                        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($bus['capacity']); ?> passengers</p>
                                        <?php if (!empty($bus['description'])): ?>
                                            <p><?php echo nl2br(htmlspecialchars($bus['description'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($package['transportation_details'])): ?>
                        <div class="highlight-box mt-4">
                            <h4><i class="bi bi-truck me-2"></i> Additional Transportation Information</h4>
                            <p><?php echo nl2br(htmlspecialchars($package['transportation_details'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                        <p class="text-muted">No team or transportation information has been assigned to this package yet.</p>
                    <?php endif; ?>
                    
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle me-2"></i> Team Information Available After Booking</h5>
                            <p>Details about your guide, driver, and transportation will be available here after you book this package.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card section-card content-section">
                <div class="section-header bg-dark">
                    <h3 class="mb-0"><i class="bi bi-images me-3"></i> Gallery</h3>
                </div>
                <div class="section-body">
                    <?php
                    // Show edit button for admins
                    if (isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin') {
                        echo '<div class="mb-3">';
                        echo '<a href="admin_gallery.php" class="btn btn-sm btn-primary">';
                        echo '<i class="bi bi-pencil me-1"></i> Manage Gallery Images';
                        echo '</a>';
                        echo '<p class="text-muted mt-2"><small>Gallery images are managed globally and not specific to individual packages.</small></p>';
                        echo '</div>';
                    }
                    
                    // Display gallery images from the separate gallery table
                    // For now, we'll show a message that gallery images are managed separately
                    echo '<div class="col-12">';
                    echo '<p class="text-muted">Gallery images are managed globally through the admin gallery system.</p>';
                    echo '</div>';
                    ?>
                </div>
            </div>
        </div>


    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View More/Less functionality for activities
    const toggleButton = document.getElementById('view-toggle-activities');
    
    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            const state = this.getAttribute('data-state');
            const remainingCount = parseInt(this.getAttribute('data-remaining-count'));
            const toggleText = this.querySelector('.toggle-text');
            
            if (state === 'more') {
                // Show all hidden activities
                const hiddenActivities = document.querySelectorAll('.activity-item-single.activity-hidden');
                hiddenActivities.forEach(activity => {
                    activity.classList.remove('activity-hidden');
                });
                
                // Update button to "View Less"
                this.setAttribute('data-state', 'less');
                toggleText.textContent = 'View Less';
            } else {
                // Hide activities beyond the initial count (2)
                const allActivities = document.querySelectorAll('.activity-item-single');
                for (let i = 2; i < allActivities.length; i++) { // Changed from 3 to 2
                    allActivities[i].classList.add('activity-hidden');
                }
                
                // Update button to "View More"
                this.setAttribute('data-state', 'more');
                toggleText.textContent = `View Activities (${remainingCount} more)`;
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>