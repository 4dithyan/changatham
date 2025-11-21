<?php
session_start();
include 'config.php'; // DB connection

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Packages - Admin Panel";

// Handle Delete Package
if (isset($_POST['delete_package'])) {
    $pid = $_POST['package_id'];
    $delete = "DELETE FROM packages WHERE package_id='$pid'";
    if (mysqli_query($conn, $delete)) {
        $success = "Package deleted successfully!";
    } else {
        $error = "Error deleting package: " . mysqli_error($conn);
    }
}

// Handle Update Package
if (isset($_POST['update_package'])) {
    $pid = $_POST['package_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']); // New destination field
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
    $accommodation = mysqli_real_escape_string($conn, $_POST['accommodation']);
    $available_slots = mysqli_real_escape_string($conn, $_POST['available_slots']);
    $pickup_point = mysqli_real_escape_string($conn, $_POST['pickup_point']);
    $dropoff_point = mysqli_real_escape_string($conn, $_POST['dropoff_point']);
    $transportation = mysqli_real_escape_string($conn, $_POST['transportation']);
    
    // New schedule fields
    $day_schedules = isset($_POST['day_schedule']) ? $_POST['day_schedule'] : [];
    
    // Create schedule fields for database
    $schedule_fields = [];
    for ($i = 1; $i <= 10; $i++) {
        $schedule_fields["day{$i}_schedule"] = mysqli_real_escape_string($conn, isset($day_schedules[$i]) ? $day_schedules[$i] : '');
    }
    
    $additional_pickups = mysqli_real_escape_string($conn, $_POST['additional_pickups']);
    $food_menu = mysqli_real_escape_string($conn, $_POST['food_menu']);
    $stay_details = mysqli_real_escape_string($conn, $_POST['stay_details']);
    $transportation_details = mysqli_real_escape_string($conn, $_POST['transportation_details']);
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    
    // Dining fields
    $breakfast = mysqli_real_escape_string($conn, $_POST['breakfast']);
    $lunch = mysqli_real_escape_string($conn, $_POST['lunch']);
    $dinner = mysqli_real_escape_string($conn, $_POST['dinner']);

    // Update image if provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = "uploads/";
        move_uploaded_file($tmp_name, $upload_dir.$image);
        $update = "UPDATE packages SET name='$name', destination='$destination', price='$price', duration='$duration', description='$description',
                   food_type='$food_type', accommodation='$accommodation', available_slots='$available_slots', pickup_point='$pickup_point', dropoff_point='$dropoff_point',
                   transportation='$transportation', image='$image', 
                   day1_schedule='{$schedule_fields['day1_schedule']}', day2_schedule='{$schedule_fields['day2_schedule']}', day3_schedule='{$schedule_fields['day3_schedule']}',
                   day4_schedule='{$schedule_fields['day4_schedule']}', day5_schedule='{$schedule_fields['day5_schedule']}', day6_schedule='{$schedule_fields['day6_schedule']}',
                   day7_schedule='{$schedule_fields['day7_schedule']}', day8_schedule='{$schedule_fields['day8_schedule']}', day9_schedule='{$schedule_fields['day9_schedule']}',
                   day10_schedule='{$schedule_fields['day10_schedule']}', additional_pickups='$additional_pickups', food_menu='$food_menu', stay_details='$stay_details',
                   transportation_details='$transportation_details', activities='$activities', breakfast='$breakfast', lunch='$lunch', dinner='$dinner' WHERE package_id='$pid'";
    } else {
        $update = "UPDATE packages SET name='$name', destination='$destination', price='$price', duration='$duration', description='$description',
                   food_type='$food_type', accommodation='$accommodation', available_slots='$available_slots', pickup_point='$pickup_point', dropoff_point='$dropoff_point',
                   transportation='$transportation',
                   day1_schedule='{$schedule_fields['day1_schedule']}', day2_schedule='{$schedule_fields['day2_schedule']}', day3_schedule='{$schedule_fields['day3_schedule']}',
                   day4_schedule='{$schedule_fields['day4_schedule']}', day5_schedule='{$schedule_fields['day5_schedule']}', day6_schedule='{$schedule_fields['day6_schedule']}',
                   day7_schedule='{$schedule_fields['day7_schedule']}', day8_schedule='{$schedule_fields['day8_schedule']}', day9_schedule='{$schedule_fields['day9_schedule']}',
                   day10_schedule='{$schedule_fields['day10_schedule']}', additional_pickups='$additional_pickups', food_menu='$food_menu', stay_details='$stay_details',
                   transportation_details='$transportation_details', activities='$activities', breakfast='$breakfast', lunch='$lunch', dinner='$dinner' WHERE package_id='$pid'";
    }

    if (mysqli_query($conn, $update)) {
        $success = "Package updated successfully!";
        
        // Handle selected activities
        // First, delete existing activities for this package
        $delete_activities = "DELETE FROM package_activities WHERE package_id='$pid'";
        mysqli_query($conn, $delete_activities);
        
        // Then insert new selected activities
        if (isset($_POST['selected_activities']) && is_array($_POST['selected_activities'])) {
            foreach ($_POST['selected_activities'] as $activity_id) {
                $activity_id = intval($activity_id);
                $insert_activity = "INSERT INTO package_activities (package_id, activity_id) VALUES ('$pid', '$activity_id')";
                mysqli_query($conn, $insert_activity);
            }
        }
        
        // Handle selected drivers
        // First, delete existing drivers for this package
        $delete_drivers = "DELETE FROM package_drivers WHERE package_id='$pid'";
        mysqli_query($conn, $delete_drivers);
        
        // Then insert new selected drivers
        if (isset($_POST['selected_drivers']) && is_array($_POST['selected_drivers'])) {
            foreach ($_POST['selected_drivers'] as $driver_id) {
                $driver_id = intval($driver_id);
                $insert_driver = "INSERT INTO package_drivers (package_id, driver_id) VALUES ('$pid', '$driver_id')";
                mysqli_query($conn, $insert_driver);
            }
        }
        
        // Handle selected guides
        // First, delete existing guides for this package
        $delete_guides = "DELETE FROM package_guides WHERE package_id='$pid'";
        mysqli_query($conn, $delete_guides);
        
        // Then insert new selected guides
        if (isset($_POST['selected_guides']) && is_array($_POST['selected_guides'])) {
            foreach ($_POST['selected_guides'] as $guide_id) {
                $guide_id = intval($guide_id);
                $insert_guide = "INSERT INTO package_guides (package_id, guide_id) VALUES ('$pid', '$guide_id')";
                mysqli_query($conn, $insert_guide);
            }
        }
        
        // Handle selected buses
        // First, delete existing buses for this package
        $delete_buses = "DELETE FROM package_buses WHERE package_id='$pid'";
        mysqli_query($conn, $delete_buses);
        
        // Then insert new selected buses
        if (isset($_POST['selected_buses']) && is_array($_POST['selected_buses'])) {
            foreach ($_POST['selected_buses'] as $bus_id) {
                $bus_id = intval($bus_id);
                $insert_bus = "INSERT INTO package_buses (package_id, bus_id) VALUES ('$pid', '$bus_id')";
                mysqli_query($conn, $insert_bus);
            }
        }
    } else {
        $error = "Error updating package: " . mysqli_error($conn);
    }
}

// Handle Send Notification
if (isset($_POST['send_notification'])) {
    $package_id = intval($_POST['package_id']);
    $notification_type = mysqli_real_escape_string($conn, $_POST['notification_type']);
    $notification_title = mysqli_real_escape_string($conn, $_POST['notification_title']);
    $notification_message = mysqli_real_escape_string($conn, $_POST['notification_message']);
    
    // Insert notification
    $insert_notification = "INSERT INTO notifications (package_id, title, message, type) VALUES ('$package_id', '$notification_title', '$notification_message', '$notification_type')";
    
    if (mysqli_query($conn, $insert_notification)) {
        $notification_id = mysqli_insert_id($conn);
        
        // Get all users who booked this package
        $bookings_query = "SELECT DISTINCT user_id FROM bookings WHERE package_id = '$package_id' AND status IN ('Pending', 'Confirmed')";
        $bookings_result = mysqli_query($conn, $bookings_query);
        
        // Create notification records for each user
        while ($booking = mysqli_fetch_assoc($bookings_result)) {
            $user_id = $booking['user_id'];
            $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) VALUES ('$user_id', '$notification_id')";
            mysqli_query($conn, $insert_user_notification);
        }
        
        $success = "Notification sent successfully to all users who booked this package!";
    } else {
        $error = "Error sending notification: " . mysqli_error($conn);
    }
}

// Fetch all packages with booking count
$packages_query = "SELECT p.*, COUNT(b.booking_id) as booking_count 
                  FROM packages p 
                  LEFT JOIN bookings b ON p.package_id = b.package_id 
                  GROUP BY p.package_id 
                  ORDER BY p.package_id DESC";
$packages = mysqli_query($conn, $packages_query);

// Fetch drivers, guides, and buses for the forms
$drivers_query = "SELECT * FROM drivers ORDER BY name";
$drivers_result = mysqli_query($conn, $drivers_query);

$guides_query = "SELECT * FROM guides ORDER BY name";
$guides_result = mysqli_query($conn, $guides_query);

$buses_query = "SELECT * FROM buses ORDER BY name";
$buses_result = mysqli_query($conn, $buses_query);

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

                    <!-- Add Package Button -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2>Manage Packages</h2>
                            <p class="text-muted">Organize and manage all travel packages</p>
                        </div>
                        <a href="add_package.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add New Package
                        </a>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control" id="packageSearch" placeholder="Search packages...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="sortPackages">
                                        <option value="newest">Newest First</option>
                                        <option value="oldest">Oldest First</option>
                                        <option value="name">Name A-Z</option>
                                        <option value="price-low">Price: Low to High</option>
                                        <option value="price-high">Price: High to Low</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterBookings">
                                        <option value="all">All Packages</option>
                                        <option value="booked">Booked Only</option>
                                        <option value="unbooked">Unbooked Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Packages List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">All Packages</h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="packagesContainer">
                                <?php while($p = mysqli_fetch_assoc($packages)) { ?>
                                    <div class="col-lg-4 col-md-6 mb-4 package-item" 
                                         data-name="<?php echo strtolower($p['name']); ?>" 
                                         data-price="<?php echo $p['price']; ?>"
                                         data-bookings="<?php echo $p['booking_count']; ?>">
                                        <div class="card h-100 package-card card-modern shadow-sm">
                                            <div class="position-relative">
                                                <img src="uploads/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" class="card-img-top package-card-img" style="height: 200px; object-fit: cover;">
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <?php if($p['booking_count'] > 0): ?>
                                                        <span class="badge bg-success"><?php echo $p['booking_count']; ?> bookings</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">No bookings</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="card-body package-card-body d-flex flex-column">
                                                <h5 class="card-title" style="color: var(--primary); font-weight: 600;"><?php echo $p['name']; ?></h5>
                                                <p class="card-text flex-grow-1"><?php echo substr($p['description'], 0, 100) . '...'; ?></p>
                                                
                                                <div class="package-meta mb-3">
                                                    <div class="meta-item"><i class="bi bi-geo-alt text-warning"></i> <?php echo $p['destination']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-clock text-warning"></i> <?php echo $p['duration']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-people text-warning"></i> <?php echo $p['accommodation']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-cup text-warning"></i> <?php echo $p['food_type']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-car-front text-warning"></i> <?php echo $p['transportation']; ?></div>
                                                    <div class="meta-item"><i class="bi bi-geo text-warning"></i> <?php echo $p['pickup_point']; ?></div>
                                                </div>
                                                
                                                <div class="package-price mb-3">Rs. <?php echo number_format($p['price'],2); ?></div>
                                                <p class="mb-3"><small><strong>Available Slots:</strong> <?php echo $p['available_slots']; ?></small></p>

                                                <div class="package-actions mt-auto">
                                                    <form method="post" class="mb-2">
                                                        <input type="hidden" name="package_id" value="<?php echo $p['package_id']; ?>">
                                                        <button type="submit" name="delete_package" class="btn btn-danger w-100 btn-package" onclick="return confirm('Are you sure you want to delete this package?')">
                                                            <i class="bi bi-trash me-1"></i> Delete
                                                        </button>
                                                    </form>

                                                    <!-- Update Button that triggers modal -->
                                                    <button class="btn btn-primary w-100 btn-package mb-2" type="button" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $p['package_id']; ?>">
                                                        <i class="bi bi-pencil me-1"></i> Update
                                                    </button>
                                                    
                                                    <!-- Send Notification Button -->
                                                    <button class="btn btn-warning w-100 btn-package" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal<?php echo $p['package_id']; ?>">
                                                        <i class="bi bi-bell me-1"></i> Send Notification
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            
                            <!-- No packages message -->
                            <?php 
                            mysqli_data_seek($packages, 0);
                            if(mysqli_num_rows($packages) == 0) { ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
                                    <h3 class="mt-3">No packages found</h3>
                                    <p class="text-muted">Get started by adding your first travel package.</p>
                                    <a href="add_package.php" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Add New Package
                                    </a>
                                </div>
                            <?php } ?>
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
    
    // Reset other result sets
    $drivers_array = [];
    mysqli_data_seek($drivers_result, 0);
    while($driver = mysqli_fetch_assoc($drivers_result)) {
        $drivers_array[] = $driver;
    }
    
    $guides_array = [];
    mysqli_data_seek($guides_result, 0);
    while($guide = mysqli_fetch_assoc($guides_result)) {
        $guides_array[] = $guide;
    }
    
    $buses_array = [];
    mysqli_data_seek($buses_result, 0);
    while($bus = mysqli_fetch_assoc($buses_result)) {
        $buses_array[] = $bus;
    }
    
    while($p = mysqli_fetch_assoc($packages)) { 
    ?>
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
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Stay Details</label>
                                    <textarea name="stay_details" class="form-control" rows="3"><?php echo htmlspecialchars($p['stay_details']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Drivers Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Drivers</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Select Drivers</label>
                                    <div class="row">
                                        <?php 
                                        // Fetch selected drivers for this package
                                        $selected_drivers_query = mysqli_query($conn, "SELECT driver_id FROM package_drivers WHERE package_id = '{$p['package_id']}'");
                                        $selected_drivers = [];
                                        while($row = mysqli_fetch_assoc($selected_drivers_query)) {
                                            $selected_drivers[] = $row['driver_id'];
                                        }
                                        
                                        // Use the stored drivers array
                                        foreach($drivers_array as $driver) { ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="selected_drivers[]" value="<?php echo $driver['driver_id']; ?>" id="driver_<?php echo $p['package_id']; ?>_<?php echo $driver['driver_id']; ?>" <?php echo in_array($driver['driver_id'], $selected_drivers) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="driver_<?php echo $p['package_id']; ?>_<?php echo $driver['driver_id']; ?>">
                                                        <?php echo htmlspecialchars($driver['name']); ?> (<?php echo htmlspecialchars($driver['phone']); ?>)
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Guides Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Guides</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Select Guides</label>
                                    <div class="row">
                                        <?php 
                                        // Fetch selected guides for this package
                                        $selected_guides_query = mysqli_query($conn, "SELECT guide_id FROM package_guides WHERE package_id = '{$p['package_id']}'");
                                        $selected_guides = [];
                                        while($row = mysqli_fetch_assoc($selected_guides_query)) {
                                            $selected_guides[] = $row['guide_id'];
                                        }
                                        
                                        // Use the stored guides array
                                        foreach($guides_array as $guide) { ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="selected_guides[]" value="<?php echo $guide['guide_id']; ?>" id="guide_<?php echo $p['package_id']; ?>_<?php echo $guide['guide_id']; ?>" <?php echo in_array($guide['guide_id'], $selected_guides) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="guide_<?php echo $p['package_id']; ?>_<?php echo $guide['guide_id']; ?>">
                                                        <?php echo htmlspecialchars($guide['name']); ?> (<?php echo htmlspecialchars($guide['phone']); ?>)
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Buses Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Buses</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Select Buses</label>
                                    <div class="row">
                                        <?php 
                                        // Fetch selected buses for this package
                                        $selected_buses_query = mysqli_query($conn, "SELECT bus_id FROM package_buses WHERE package_id = '{$p['package_id']}'");
                                        $selected_buses = [];
                                        while($row = mysqli_fetch_assoc($selected_buses_query)) {
                                            $selected_buses[] = $row['bus_id'];
                                        }
                                        
                                        // Use the stored buses array
                                        foreach($buses_array as $bus) { ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="selected_buses[]" value="<?php echo $bus['bus_id']; ?>" id="bus_<?php echo $p['package_id']; ?>_<?php echo $bus['bus_id']; ?>" <?php echo in_array($bus['bus_id'], $selected_buses) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="bus_<?php echo $p['package_id']; ?>_<?php echo $bus['bus_id']; ?>">
                                                        <?php echo htmlspecialchars($bus['name']); ?> (<?php echo htmlspecialchars($bus['type']); ?>, Capacity: <?php echo $bus['capacity']; ?>)
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Activities Section -->
                        <div class="form-section mb-4">
                            <h5 class="section-header">Activities</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Select Activities</label>
                                    <?php 
                                    // Fetch all activities
                                    $activities_query = mysqli_query($conn, "SELECT * FROM activities ORDER BY name");
                                    if (mysqli_num_rows($activities_query) > 0) {
                                        echo '<div class="row">';
                                        // Get currently selected activities for this package
                                        $selected_activities_query = mysqli_query($conn, "SELECT activity_id FROM package_activities WHERE package_id = '{$p['package_id']}'");
                                        $selected_activities = [];
                                        while($selected_activity = mysqli_fetch_assoc($selected_activities_query)) {
                                            $selected_activities[] = $selected_activity['activity_id'];
                                        }
                                        
                                        while($activity = mysqli_fetch_assoc($activities_query)) {
                                            $is_selected = in_array($activity['activity_id'], $selected_activities) ? 'checked' : '';
                                            echo '<div class="col-md-4 mb-2">';
                                            echo '<div class="form-check">';
                                            echo '<input class="form-check-input" type="checkbox" name="selected_activities[]" value="'.$activity['activity_id'].'" id="activity_'.$p['package_id'].'_'.$activity['activity_id'].'" '.$is_selected.'>';
                                            echo '<label class="form-check-label" for="activity_'.$p['package_id'].'_'.$activity['activity_id'].'">'.$activity['name'].'</label>';
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
                                    <textarea name="activities" class="form-control" rows="3"><?php echo htmlspecialchars($p['activities']); ?></textarea>
                                    <small class="form-text text-muted">Add any package-specific activity information not covered by the selected activities</small>
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
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="update_package" class="btn btn-primary">Update Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal<?php echo $p['package_id']; ?>" tabindex="-1" aria-labelledby="notificationModalLabel<?php echo $p['package_id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel<?php echo $p['package_id']; ?>">Send Notification: <?php echo $p['name']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" name="package_id" value="<?php echo $p['package_id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Notification Type *</label>
                            <select name="notification_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="delayed">Trip Delayed</option>
                                <option value="cancelled">Trip Cancelled</option>
                                <option value="scheduled">Trip Scheduled</option>
                                <option value="info">Information</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" name="notification_title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="notification_message" class="form-control" rows="4" required></textarea>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="send_notification" class="btn btn-warning">Send Notification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php include 'includes/admin_footer.php'; ?>

<script>
// Dynamic schedule fields
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
    
    // Search functionality
    const searchInput = document.getElementById('packageSearch');
    const packageItems = document.querySelectorAll('.package-item');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            packageItems.forEach(function(item) {
                const packageName = item.getAttribute('data-name');
                if (packageName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Sort functionality
    const sortSelect = document.getElementById('sortPackages');
    const packagesContainer = document.getElementById('packagesContainer');
    
    if (sortSelect && packagesContainer) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const items = Array.from(packageItems);
            
            items.sort(function(a, b) {
                switch(sortValue) {
                    case 'newest':
                        return 0; // Already sorted by DB query
                    case 'oldest':
                        return 0; // Would need to reverse DB query
                    case 'name':
                        const nameA = a.getAttribute('data-name');
                        const nameB = b.getAttribute('data-name');
                        return nameA.localeCompare(nameB);
                    case 'price-low':
                        const priceA = parseFloat(a.getAttribute('data-price'));
                        const priceB = parseFloat(b.getAttribute('data-price'));
                        return priceA - priceB;
                    case 'price-high':
                        const priceA2 = parseFloat(a.getAttribute('data-price'));
                        const priceB2 = parseFloat(b.getAttribute('data-price'));
                        return priceB2 - priceA2;
                    default:
                        return 0;
                }
            });
            
            // Re-append sorted items
            items.forEach(function(item) {
                packagesContainer.appendChild(item);
            });
        });
    }
    
    // Filter by bookings
    const filterSelect = document.getElementById('filterBookings');
    
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;
            
            packageItems.forEach(function(item) {
                const bookingCount = parseInt(item.getAttribute('data-bookings'));
                
                switch(filterValue) {
                    case 'all':
                        item.style.display = 'block';
                        break;
                    case 'booked':
                        item.style.display = bookingCount > 0 ? 'block' : 'none';
                        break;
                    case 'unbooked':
                        item.style.display = bookingCount === 0 ? 'block' : 'none';
                        break;
                }
            });
        });
    }
});
</script>

</body>
</html>