<?php
session_start();
include 'config.php'; // DB connection

// Select the database
mysqli_select_db($conn, $database);

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Function to send notifications when a package is updated
function sendPackageUpdateNotification($conn, $package_id, $package_name) {
    // Create a notification in the notifications table
    $title = "Package Updated";
    $message = "The package '$package_name' has been updated. Please check the updated details.";
    $type = "info";
    
    $insert_notification = "INSERT INTO notifications (package_id, title, message, type) VALUES ('$package_id', '$title', '$message', '$type')";
    if (mysqli_query($conn, $insert_notification)) {
        $notification_id = mysqli_insert_id($conn);
        
        // Get all users who have booked this package
        $bookings_query = "SELECT DISTINCT user_id FROM bookings WHERE package_id = '$package_id'";
        $bookings_result = mysqli_query($conn, $bookings_query);
        
        // Create user notifications for each user who booked this package
        while ($booking = mysqli_fetch_assoc($bookings_result)) {
            $user_id = $booking['user_id'];
            $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) VALUES ('$user_id', '$notification_id')";
            mysqli_query($conn, $insert_user_notification);
        }
    }
}

// Check if we're redirecting after a successful package addition, update, or delete
$success = '';
$error = '';

if (isset($_SESSION['package_added']) && $_SESSION['package_added'] === true) {
    $success = "Package added successfully!";
    unset($_SESSION['package_added']); // Clear the flag
} elseif (isset($_SESSION['package_add_error'])) {
    $error = $_SESSION['package_add_error'];
    unset($_SESSION['package_add_error']); // Clear the error
} elseif (isset($_SESSION['package_updated']) && $_SESSION['package_updated'] === true) {
    $success = "Package updated successfully!";
    unset($_SESSION['package_updated']); // Clear the flag
} elseif (isset($_SESSION['package_update_error'])) {
    $error = $_SESSION['package_update_error'];
    unset($_SESSION['package_update_error']); // Clear the error
} elseif (isset($_SESSION['package_deleted']) && $_SESSION['package_deleted'] === true) {
    $success = "Package deleted successfully!";
    unset($_SESSION['package_deleted']); // Clear the flag
} elseif (isset($_SESSION['package_delete_error'])) {
    $error = $_SESSION['package_delete_error'];
    unset($_SESSION['package_delete_error']); // Clear the error
}

$page_title = "Add Package - Admin Panel";

// Fetch drivers, guides, and buses for selection
$drivers = mysqli_query($conn, "SELECT * FROM drivers ORDER BY name");
$guides = mysqli_query($conn, "SELECT * FROM guides ORDER BY name");
$buses = mysqli_query($conn, "SELECT * FROM buses ORDER BY name");

// Handle Add Package
if (isset($_POST['add_package'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // Calculate duration based on number of days
    $num_days = isset($_POST['num_days']) ? intval($_POST['num_days']) : 1;
    $num_nights = max(0, $num_days - 1); // At least 0 nights
    $duration = $num_days . " Days " . $num_nights . " Nights";
    
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
    $accommodation = mysqli_real_escape_string($conn, $_POST['accommodation']);
    $available_slots = mysqli_real_escape_string($conn, $_POST['available_slots']);
    $pickup_point = mysqli_real_escape_string($conn, $_POST['pickup_point']);
    $dropoff_point = mysqli_real_escape_string($conn, $_POST['dropoff_point']);
    $transportation = mysqli_real_escape_string($conn, $_POST['transportation']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']); // New destination field
    
    // New schedule fields
    $day_schedules = isset($_POST['day_schedule']) ? $_POST['day_schedule'] : [];
    
    // Create schedule fields for database
    $schedule_fields = [];
    for ($i = 1; $i <= 10; $i++) {
        $schedule_fields["day{$i}_schedule"] = mysqli_real_escape_string($conn, isset($day_schedules[$i]) ? $day_schedules[$i] : '');
    }
    
    $additional_pickups = mysqli_real_escape_string($conn, $_POST['additional_pickups']);
    $food_menu = mysqli_real_escape_string($conn, $_POST['food_menu']);
    $breakfast = mysqli_real_escape_string($conn, $_POST['breakfast']);
    $lunch = mysqli_real_escape_string($conn, $_POST['lunch']);
    $dinner = mysqli_real_escape_string($conn, $_POST['dinner']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $daily_start_time = mysqli_real_escape_string($conn, $_POST['daily_start_time']);
    $daily_end_time = mysqli_real_escape_string($conn, $_POST['daily_end_time']);
    $transportation_details = mysqli_real_escape_string($conn, $_POST['transportation_details']);
    $activities_details = mysqli_real_escape_string($conn, $_POST['activities']);

    // Calculate end date based on number of days
    if (!empty($start_date) && $num_days > 0) {
        $start_date_obj = new DateTime($start_date);
        $end_date_obj = clone $start_date_obj;
        $end_date_obj->add(new DateInterval('P' . ($num_days - 1) . 'D'));
        $end_date = $end_date_obj->format('Y-m-d');
    } else {
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    }

    // Image upload
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $upload_dir = "uploads/";
    move_uploaded_file($tmp_name, $upload_dir.$image);

    $insert = "INSERT INTO packages (name, price, duration, description, food_type, accommodation, available_slots, pickup_point, dropoff_point, transportation, image, 
               day1_schedule, day2_schedule, day3_schedule, day4_schedule, day5_schedule, day6_schedule, day7_schedule, day8_schedule, day9_schedule, day10_schedule,
               additional_pickups, food_menu, breakfast, lunch, dinner, start_date, end_date, daily_start_time, daily_end_time, transportation_details, activities, destination) 
               VALUES ('$name','$price','$duration','$description','$food_type','$accommodation','$available_slots','$pickup_point','$dropoff_point','$transportation','$image',
               '{$schedule_fields['day1_schedule']}','{$schedule_fields['day2_schedule']}','{$schedule_fields['day3_schedule']}','{$schedule_fields['day4_schedule']}','{$schedule_fields['day5_schedule']}',
               '{$schedule_fields['day6_schedule']}','{$schedule_fields['day7_schedule']}','{$schedule_fields['day8_schedule']}','{$schedule_fields['day9_schedule']}','{$schedule_fields['day10_schedule']}',
               '$additional_pickups','$food_menu','$breakfast','$lunch','$dinner','$start_date','$end_date','$daily_start_time','$daily_end_time','$transportation_details','$activities_details', '$destination')";
    
    if (mysqli_query($conn, $insert)) {
        $package_id = mysqli_insert_id($conn);
        // Set session variable to indicate successful addition
        $_SESSION['package_added'] = true;
        
        // Handle selected activities
        if (isset($_POST['selected_activities']) && is_array($_POST['selected_activities'])) {
            foreach ($_POST['selected_activities'] as $activity_id) {
                $activity_id = intval($activity_id);
                $insert_activity = "INSERT INTO package_activities (package_id, activity_id) VALUES ('$package_id', '$activity_id')";
                mysqli_query($conn, $insert_activity);
            }
        }
        
        // Handle selected drivers
        if (isset($_POST['selected_drivers']) && is_array($_POST['selected_drivers'])) {
            foreach ($_POST['selected_drivers'] as $driver_id) {
                $driver_id = intval($driver_id);
                $insert_driver = "INSERT INTO package_drivers (package_id, driver_id) VALUES ('$package_id', '$driver_id')";
                mysqli_query($conn, $insert_driver);
            }
        }
        
        // Handle selected guides
        if (isset($_POST['selected_guides']) && is_array($_POST['selected_guides'])) {
            foreach ($_POST['selected_guides'] as $guide_id) {
                $guide_id = intval($guide_id);
                $insert_guide = "INSERT INTO package_guides (package_id, guide_id) VALUES ('$package_id', '$guide_id')";
                mysqli_query($conn, $insert_guide);
            }
        }
        
        // Handle selected buses
        if (isset($_POST['selected_buses']) && is_array($_POST['selected_buses'])) {
            foreach ($_POST['selected_buses'] as $bus_id) {
                $bus_id = intval($bus_id);
                $insert_bus = "INSERT INTO package_buses (package_id, bus_id) VALUES ('$package_id', '$bus_id')";
                mysqli_query($conn, $insert_bus);
            }
        }
        
        // Redirect to prevent form resubmission
        header("Location: add_package.php");
        exit();
    } else {
        // Set error in session and redirect
        $_SESSION['package_add_error'] = "Error adding package: " . mysqli_error($conn);
        header("Location: add_package.php");
        exit();
    }
}

// Handle Delete Package
if (isset($_POST['delete_package'])) {
    $pid = $_POST['package_id'];
    $delete = "DELETE FROM packages WHERE package_id='$pid'";
    if (mysqli_query($conn, $delete)) {
        // Set success message in session and redirect
        $_SESSION['package_deleted'] = true;
        header("Location: add_package.php");
        exit();
    } else {
        // Set error in session and redirect
        $_SESSION['package_delete_error'] = "Error deleting package: " . mysqli_error($conn);
        header("Location: add_package.php");
        exit();
    }
}

// Handle Update Package
if (isset($_POST['update_package'])) {
    $pid = $_POST['package_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // Calculate duration based on number of days
    $num_days = isset($_POST['num_days']) ? intval($_POST['num_days']) : 1;
    $num_nights = max(0, $num_days - 1); // At least 0 nights
    $duration = $num_days . " Days " . $num_nights . " Nights";
    
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
    $accommodation = mysqli_real_escape_string($conn, $_POST['accommodation']);
    $available_slots = mysqli_real_escape_string($conn, $_POST['available_slots']);
    $pickup_point = mysqli_real_escape_string($conn, $_POST['pickup_point']);
    $dropoff_point = mysqli_real_escape_string($conn, $_POST['dropoff_point']);
    $transportation = mysqli_real_escape_string($conn, $_POST['transportation']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']); // New destination field
    
    // New schedule fields
    $day_schedules = isset($_POST['day_schedule']) ? $_POST['day_schedule'] : [];
    
    // Create schedule fields for database
    $schedule_fields = [];
    for ($i = 1; $i <= 10; $i++) {
        $schedule_fields["day{$i}_schedule"] = mysqli_real_escape_string($conn, isset($day_schedules[$i]) ? $day_schedules[$i] : '');
    }
    
    $additional_pickups = mysqli_real_escape_string($conn, $_POST['additional_pickups']);
    $food_menu = mysqli_real_escape_string($conn, $_POST['food_menu']);
    $breakfast = mysqli_real_escape_string($conn, $_POST['breakfast']);
    $lunch = mysqli_real_escape_string($conn, $_POST['lunch']);
    $dinner = mysqli_real_escape_string($conn, $_POST['dinner']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $daily_start_time = mysqli_real_escape_string($conn, $_POST['daily_start_time']);
    $daily_end_time = mysqli_real_escape_string($conn, $_POST['daily_end_time']);
    $transportation_details = mysqli_real_escape_string($conn, $_POST['transportation_details']);
    $activities_details = mysqli_real_escape_string($conn, $_POST['activities']);

    // Calculate end date based on number of days
    if (!empty($start_date) && $num_days > 0) {
        $start_date_obj = new DateTime($start_date);
        $end_date_obj = clone $start_date_obj;
        $end_date_obj->add(new DateInterval('P' . ($num_days - 1) . 'D'));
        $end_date = $end_date_obj->format('Y-m-d');
    } else {
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    }

    // Update image if provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = "uploads/";
        move_uploaded_file($tmp_name, $upload_dir.$image);
        $update = "UPDATE packages SET name='$name', price='$price', duration='$duration', description='$description',
                   food_type='$food_type', accommodation='$accommodation', available_slots='$available_slots', pickup_point='$pickup_point', dropoff_point='$dropoff_point',
                   transportation='$transportation', image='$image', destination='$destination',
                   day1_schedule='{$schedule_fields['day1_schedule']}', day2_schedule='{$schedule_fields['day2_schedule']}', day3_schedule='{$schedule_fields['day3_schedule']}',
                   day4_schedule='{$schedule_fields['day4_schedule']}', day5_schedule='{$schedule_fields['day5_schedule']}', day6_schedule='{$schedule_fields['day6_schedule']}',
                   day7_schedule='{$schedule_fields['day7_schedule']}', day8_schedule='{$schedule_fields['day8_schedule']}', day9_schedule='{$schedule_fields['day9_schedule']}',
                   day10_schedule='{$schedule_fields['day10_schedule']}', additional_pickups='$additional_pickups', food_menu='$food_menu', breakfast='$breakfast', lunch='$lunch', dinner='$dinner',
                   start_date='$start_date', end_date='$end_date', daily_start_time='$daily_start_time', daily_end_time='$daily_end_time',
                   transportation_details='$transportation_details', activities='$activities_details' WHERE package_id='$pid'";
    } else {
        $update = "UPDATE packages SET name='$name', price='$price', duration='$duration', description='$description',
                   food_type='$food_type', accommodation='$accommodation', available_slots='$available_slots', pickup_point='$pickup_point', dropoff_point='$dropoff_point',
                   transportation='$transportation', destination='$destination',
                   day1_schedule='{$schedule_fields['day1_schedule']}', day2_schedule='{$schedule_fields['day2_schedule']}', day3_schedule='{$schedule_fields['day3_schedule']}',
                   day4_schedule='{$schedule_fields['day4_schedule']}', day5_schedule='{$schedule_fields['day5_schedule']}', day6_schedule='{$schedule_fields['day6_schedule']}',
                   day7_schedule='{$schedule_fields['day7_schedule']}', day8_schedule='{$schedule_fields['day8_schedule']}', day9_schedule='{$schedule_fields['day9_schedule']}',
                   day10_schedule='{$schedule_fields['day10_schedule']}', additional_pickups='$additional_pickups', food_menu='$food_menu', breakfast='$breakfast', lunch='$lunch', dinner='$dinner',
                   start_date='$start_date', end_date='$end_date', daily_start_time='$daily_start_time', daily_end_time='$daily_end_time',
                   transportation_details='$transportation_details', activities='$activities_details' WHERE package_id='$pid'";
    }

    if (mysqli_query($conn, $update)) {
        // Set success message in session and redirect
        $_SESSION['package_updated'] = true;
        header("Location: add_package.php");
        exit();
    } else {
        // Set error in session and redirect
        $_SESSION['package_update_error'] = "Error updating package: " . mysqli_error($conn);
        header("Location: add_package.php");
        exit();
    }
}

// Fetch all packages
$packages = mysqli_query($conn, "SELECT * FROM packages ORDER BY package_id DESC");

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

                    <!-- Add Package Form -->
                    <div class="card form-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Add New Package</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <!-- Basic Information Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Basic Information</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Package Name *</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Destination *</label>
                                            <input type="text" name="destination" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Price (₹) *</label>
                                            <input type="number" name="price" class="form-control" step="0.01" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Number of Days *</label>
                                            <input type="number" id="num_days" name="num_days" class="form-control" min="1" max="10" value="3" required>
                                            <div id="duration_display" class="duration-display">3 Days 2 Nights</div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Description *</label>
                                            <textarea name="description" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Schedule Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Schedule Information</h5>
                                    <div class="row">
                                        <div id="schedule_fields">
                                            <!-- Dynamic schedule fields will be added here by JavaScript -->
                                            <div class="schedule-day mb-3">
                                                <label class="form-label">Day 1 Schedule</label>
                                                <textarea name="day_schedule[1]" class="form-control" rows="4" placeholder="Enter schedule details for Day 1"></textarea>
                                            </div>
                                            
                                            <div class="schedule-day mb-3">
                                                <label class="form-label">Day 2 Schedule</label>
                                                <textarea name="day_schedule[2]" class="form-control" rows="4" placeholder="Enter schedule details for Day 2"></textarea>
                                            </div>
                                            
                                            <div class="schedule-day mb-3">
                                                <label class="form-label">Day 3 Schedule</label>
                                                <textarea name="day_schedule[3]" class="form-control" rows="4" placeholder="Enter schedule details for Day 3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Food & Accommodation Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Food & Accommodation</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Food Type *</label>
                                            <input type="text" name="food_type" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Accommodation *</label>
                                            <input type="text" name="accommodation" class="form-control" required>
                                        </div>
                                        <!-- Meal Separation Section -->
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Breakfast Details</label>
                                            <textarea name="breakfast" class="form-control" rows="2" placeholder="Enter breakfast details"></textarea>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Lunch Details</label>
                                            <textarea name="lunch" class="form-control" rows="2" placeholder="Enter lunch details"></textarea>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Dinner Details</label>
                                            <textarea name="dinner" class="form-control" rows="2" placeholder="Enter dinner details"></textarea>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Food Menu</label>
                                            <textarea name="food_menu" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Date & Time Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Date & Time Information</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" name="start_date" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">End Date</label>
                                            <input type="date" name="end_date" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Daily Start Time</label>
                                            <input type="time" name="daily_start_time" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Daily End Time</label>
                                            <input type="time" name="daily_end_time" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pickup Points Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Pickup & Dropoff Information</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Pickup Point *</label>
                                            <input type="text" name="pickup_point" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Dropoff Point *</label>
                                            <input type="text" name="dropoff_point" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Available Slots *</label>
                                            <input type="number" name="available_slots" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Additional Pickup Points</label>
                                            <input type="text" name="additional_pickups" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Transportation Section -->
                                <!-- Removed as buses details are already implemented -->
                                
                                <!-- Activities Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Activities Information</h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Select Activities</label>
                                            <?php 
                                            // Fetch all activities
                                            $activities_query = mysqli_query($conn, "SELECT * FROM activities ORDER BY name");
                                            if (mysqli_num_rows($activities_query) > 0) {
                                                echo '<div class="row">';
                                                while($activity = mysqli_fetch_assoc($activities_query)) {
                                                    echo '<div class="col-md-4 mb-2">';
                                                    echo '<div class="form-check">';
                                                    echo '<input class="form-check-input" type="checkbox" name="selected_activities[]" value="'.$activity['activity_id'].'" id="activity_'.$activity['activity_id'].'">';
                                                    echo '<label class="form-check-label" for="activity_'.$activity['activity_id'].'">'.$activity['name'].'</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                echo '</div>';
                                                echo '<small class="form-text text-muted">Select the activities included in this package</small>';
                                            } else {
                                                echo '<p class="text-muted">No activities available. <a href="manage_activities.php">Add activities first</a>.</p>';
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Additional Activity Details</label>
                                            <textarea name="activities" class="form-control" rows="3"></textarea>
                                            <small class="form-text text-muted">Add any package-specific activity information not covered by the selected activities</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Drivers & Guides Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Drivers & Guides</h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Select Drivers</label>
                                            <?php 
                                            if (mysqli_num_rows($drivers) > 0) {
                                                echo '<div class="row">';
                                                while($driver = mysqli_fetch_assoc($drivers)) {
                                                    echo '<div class="col-md-4 mb-2">';
                                                    echo '<div class="form-check">';
                                                    echo '<input class="form-check-input" type="checkbox" name="selected_drivers[]" value="'.$driver['driver_id'].'" id="driver_'.$driver['driver_id'].'">';
                                                    echo '<label class="form-check-label" for="driver_'.$driver['driver_id'].'">'.$driver['name'].'</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                echo '</div>';
                                            } else {
                                                echo '<p class="text-muted">No drivers available. <a href="manage_drivers.php">Add drivers first</a>.</p>';
                                            }
                                            ?>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Select Guides</label>
                                            <?php 
                                            if (mysqli_num_rows($guides) > 0) {
                                                echo '<div class="row">';
                                                while($guide = mysqli_fetch_assoc($guides)) {
                                                    echo '<div class="col-md-4 mb-2">';
                                                    echo '<div class="form-check">';
                                                    echo '<input class="form-check-input" type="checkbox" name="selected_guides[]" value="'.$guide['guide_id'].'" id="guide_'.$guide['guide_id'].'">';
                                                    echo '<label class="form-check-label" for="guide_'.$guide['guide_id'].'">'.$guide['name'].'</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                echo '</div>';
                                            } else {
                                                echo '<p class="text-muted">No guides available. <a href="manage_guides.php">Add guides first</a>.</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Image Upload Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Package Image</h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Upload Image *</label>
                                            <input type="file" name="image" class="form-control" required>
                                            <small class="form-text text-muted">Please upload a high-quality image for the package (JPG, PNG, GIF)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Buses Section -->
                                <div class="form-section">
                                    <h5 class="section-header">Buses</h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Select Buses</label>
                                            <?php 
                                            if (mysqli_num_rows($buses) > 0) {
                                                echo '<div class="row">';
                                                while($bus = mysqli_fetch_assoc($buses)) {
                                                    echo '<div class="col-md-4 mb-2">';
                                                    echo '<div class="form-check">';
                                                    echo '<input class="form-check-input" type="checkbox" name="selected_buses[]" value="'.$bus['bus_id'].'" id="bus_'.$bus['bus_id'].'">';
                                                    echo '<label class="form-check-label" for="bus_'.$bus['bus_id'].'">'.$bus['name'].' ('.$bus['type'].', '.$bus['capacity'].' seats)</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                echo '</div>';
                                                echo '<small class="form-text text-muted">Select the buses that will be used for this package</small>';
                                            } else {
                                                echo '<p class="text-muted">No buses available. <a href="manage_buses.php">Add buses first</a>.</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" name="add_package" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle me-2"></i>Add Package
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Existing Packages -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Existing Packages</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php while($p = mysqli_fetch_assoc($packages)) { ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 package-card card-modern">
                                            <img src="uploads/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" class="card-img-top package-card-img">
                                            <div class="card-body package-card-body">
                                                <h5 class="card-title" style="color: var(--primary); font-weight: 600;"><?php echo $p['name']; ?></h5>
                                                <p class="card-text"><?php echo substr($p['description'], 0, 100) . '...'; ?></p>
                                                
                                                <div class="package-meta">
                                                    <div class="meta-item"><i class="bi bi-geo-alt text-warning"></i> <?php echo $p['destination']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-clock text-warning"></i> <?php echo $p['duration']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-people text-warning"></i> <?php echo $p['accommodation']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-cup text-warning"></i> <?php echo $p['food_type']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-car-front text-warning"></i> <?php echo $p['transportation']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-geo text-warning"></i> <?php echo $p['pickup_point']; ?></div>
                                                </div>
                                                
                                                <div class="package-price">Rs. <?php echo number_format($p['price'],2); ?></div>
                                                <p class="mb-2"><small><strong>Available Slots:</strong> <?php echo $p['available_slots']; ?></small></p>

                                                <div class="package-actions">
                                                    <form method="post" class="mb-2">
                                                        <input type="hidden" name="package_id" value="<?php echo $p['package_id']; ?>">
                                                        <button type="submit" name="delete_package" class="btn btn-danger w-100 btn-package" onclick="return confirm('Are you sure you want to delete this package?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>

                                                    <!-- Update Button that triggers modal -->
                                                    <button class="btn btn-primary w-100 btn-package" type="button" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $p['package_id']; ?>">
                                                        <i class="bi bi-pencil"></i> Update
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modals -->
    <?php 
    // Reset the packages result set
    mysqli_data_seek($packages, 0);
    while($p = mysqli_fetch_assoc($packages)) { 
    ?>
    <!-- Update Modal for Package <?php echo $p['package_id']; ?> -->
    <div class="modal fade" id="updateModal<?php echo $p['package_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $p['package_id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel<?php echo $p['package_id']; ?>">Update Package: <?php echo $p['name']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="package_id" value="<?php echo $p['package_id']; ?>">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Package Name *</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo $p['name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Destination *</label>
                                    <input type="text" name="destination" class="form-control" value="<?php echo htmlspecialchars($p['destination']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (₹) *</label>
                                    <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $p['price']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration *</label>
                                    <input type="text" name="duration" class="form-control" value="<?php echo $p['duration']; ?>" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description" class="form-control" rows="3" required><?php echo $p['description']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Schedule Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Schedule Information</h5>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Number of Days</label>
                                    <input type="number" id="num_days_<?php echo $p['package_id']; ?>" name="num_days" class="form-control" min="1" max="10" value="<?php 
                                        // Count non-empty day schedules
                                        $day_count = 0;
                                        for ($i = 1; $i <= 10; $i++) {
                                            if (!empty($p["day{$i}_schedule"])) {
                                                $day_count = $i;
                                            }
                                        }
                                        echo max(1, $day_count);
                                    ?>">
                                    <small class="form-text text-muted">Select the number of days for this package (1-10)</small>
                                </div>
                                <div class="col-12">
                                    <div id="schedule_fields_<?php echo $p['package_id']; ?>">
                                        <?php 
                                        // Display existing schedule fields
                                        for ($i = 1; $i <= 10; $i++) {
                                            if (!empty($p["day{$i}_schedule"]) || $i <= 3) {
                                                echo '<div class="schedule-day mb-3">';
                                                echo '<label class="form-label">Day ' . $i . ' Schedule</label>';
                                                echo '<textarea name="day_schedule[' . $i . ']" class="form-control" rows="4">' . htmlspecialchars($p["day{$i}_schedule"]) . '</textarea>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pickup Points Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Pickup Points</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Primary Pickup Point *</label>
                                    <input type="text" name="pickup_point" class="form-control" value="<?php echo htmlspecialchars($p['pickup_point']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Drop-off Point *</label>
                                    <input type="text" name="dropoff_point" class="form-control" value="<?php echo htmlspecialchars($p['dropoff_point']); ?>" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Additional Pickup Points</label>
                                    <textarea name="additional_pickups" class="form-control" rows="3"><?php echo htmlspecialchars($p['additional_pickups']); ?></textarea>
                                    <small class="form-text text-muted">Enter additional pickup points, one per line</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Accommodation & Food Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Accommodation & Food</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Accommodation Type *</label>
                                    <input type="text" name="accommodation" class="form-control" value="<?php echo htmlspecialchars($p['accommodation']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Food Type *</label>
                                    <input type="text" name="food_type" class="form-control" value="<?php echo htmlspecialchars($p['food_type']); ?>" required>
                                </div>
                                <!-- Meal Separation Section -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Breakfast Details</label>
                                    <textarea name="breakfast" class="form-control" rows="2" placeholder="Enter breakfast details"><?php echo htmlspecialchars($p['breakfast']); ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Lunch Details</label>
                                    <textarea name="lunch" class="form-control" rows="2" placeholder="Enter lunch details"><?php echo htmlspecialchars($p['lunch']); ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Dinner Details</label>
                                    <textarea name="dinner" class="form-control" rows="2" placeholder="Enter dinner details"><?php echo htmlspecialchars($p['dinner']); ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Food Menu Details</label>
                                    <textarea name="food_menu" class="form-control" rows="3"><?php echo htmlspecialchars($p['food_menu']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date & Time Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Date & Time Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($p['start_date']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($p['end_date']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Daily Start Time</label>
                                    <input type="time" name="daily_start_time" class="form-control" value="<?php echo htmlspecialchars($p['daily_start_time']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Daily End Time</label>
                                    <input type="time" name="daily_end_time" class="form-control" value="<?php echo htmlspecialchars($p['daily_end_time']); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transportation Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Transportation</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Transportation Type *</label>
                                    <input type="text" name="transportation" class="form-control" value="<?php echo htmlspecialchars($p['transportation']); ?>" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Transportation Details</label>
                                    <textarea name="transportation_details" class="form-control" rows="3"><?php echo htmlspecialchars($p['transportation_details']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Activities Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Activities</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Included Activities</label>
                                    <textarea name="activities" class="form-control" rows="3"><?php echo htmlspecialchars($p['activities']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Package Image</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Current Image</label>
                                    <img src="uploads/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" class="img-fluid mb-2" style="max-height: 150px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Update Image</label>
                                    <input type="file" name="image" class="form-control">
                                    <small class="form-text text-muted">Leave blank to keep current image</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Availability Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Availability</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Available Slots *</label>
                                    <input type="number" name="available_slots" class="form-control" value="<?php echo $p['available_slots']; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Buses Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Buses</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Select Buses</label>
                                    <?php 
                                    // Fetch all buses
                                    $all_buses_query = mysqli_query($conn, "SELECT * FROM buses ORDER BY name");
                                    if (mysqli_num_rows($all_buses_query) > 0) {
                                        echo '<div class="row">';
                                        // Get currently selected buses for this package
                                        $selected_buses_query = mysqli_query($conn, "SELECT bus_id FROM package_buses WHERE package_id = '{$p['package_id']}'");
                                        $selected_buses = [];
                                        while($selected_bus = mysqli_fetch_assoc($selected_buses_query)) {
                                            $selected_buses[] = $selected_bus['bus_id'];
                                        }
                                        
                                        while($bus = mysqli_fetch_assoc($all_buses_query)) {
                                            $is_selected = in_array($bus['bus_id'], $selected_buses) ? 'checked' : '';
                                            echo '<div class="col-md-4 mb-2">';
                                            echo '<div class="form-check">';
                                            echo '<input class="form-check-input" type="checkbox" name="selected_buses[]" value="'.$bus['bus_id'].'" id="bus_'.$p['package_id'].'_'.$bus['bus_id'].'" '.$is_selected.'>';
                                            echo '<label class="form-check-label" for="bus_'.$p['package_id'].'_'.$bus['bus_id'].'">'.$bus['name'].' ('.$bus['type'].', '.$bus['capacity'].' seats)</label>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        echo '<small class="form-text text-muted">Select the buses that will be used for this package</small>';
                                    } else {
                                        echo '<p class="text-muted">No buses available. <a href="manage_buses.php">Add buses first</a>.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="update_package" class="btn btn-primary">Update Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php include 'includes/admin_footer.php'; ?>

    <script>
    // Dynamic schedule fields for add form
    document.getElementById('num_days').addEventListener('change', function() {
        const numDays = parseInt(this.value);
        const scheduleFields = document.getElementById('schedule_fields');
        let html = '';
        
        // Update duration display
        const numNights = Math.max(0, numDays - 1);
        document.getElementById('duration_display').textContent = numDays + ' Days ' + numNights + ' Nights';
        
        for (let i = 1; i <= numDays; i++) {
            html += '<div class="schedule-day mb-3">';
            html += '<label class="form-label">Day ' + i + ' Schedule</label>';
            html += '<textarea name="day_schedule[' + i + ']" class="form-control" rows="4" placeholder="Enter schedule details for Day ' + i + '"></textarea>';
            html += '</div>';
        }
        
        scheduleFields.innerHTML = html;
    });

    // Dynamic schedule fields for update modals
    document.addEventListener('DOMContentLoaded', function() {
        <?php 
        // Reset the packages result set
        mysqli_data_seek($packages, 0);
        while($p = mysqli_fetch_assoc($packages)) { 
        ?>
        const numDaysInput<?php echo $p['package_id']; ?> = document.getElementById('num_days_<?php echo $p['package_id']; ?>');
        const scheduleFields<?php echo $p['package_id']; ?> = document.getElementById('schedule_fields_<?php echo $p['package_id']; ?>');
        
        if (numDaysInput<?php echo $p['package_id']; ?> && scheduleFields<?php echo $p['package_id']; ?>) {
            numDaysInput<?php echo $p['package_id']; ?>.addEventListener('change', function() {
                const numDays = parseInt(this.value);
                let html = '';
                
                for (let i = 1; i <= numDays; i++) {
                    html += '<div class="schedule-day mb-3">';
                    html += '<label class="form-label">Day ' + i + ' Schedule</label>';
                    html += '<textarea name="day_schedule[' + i + ']" class="form-control" rows="4"><?php echo isset($p["day{$i}_schedule"]) ? htmlspecialchars($p["day{$i}_schedule"]) : ""; ?></textarea>';
                    html += '</div>';
                }
                
                scheduleFields<?php echo $p['package_id']; ?>.innerHTML = html;
            });
        }
        <?php } ?>
    });
    </script>
</body>
</html>