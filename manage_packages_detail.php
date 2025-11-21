<?php
include 'config.php';

// Function to display all current packages
function displayCurrentPackages($conn) {
    echo "<h1>Current Packages in Database</h1>\n";
    
    $package_query = mysqli_query($conn, "SELECT * FROM packages");
    
    if(mysqli_num_rows($package_query) > 0){
        echo "<p>Total packages: " . mysqli_num_rows($package_query) . "</p>\n";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;'>\n";
        
        while($package = mysqli_fetch_assoc($package_query)){
            echo "<div style='border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f9f9f9;'>\n";
            echo "<h3>" . htmlspecialchars($package['name']) . "</h3>\n";
            echo "<p><strong>Destination:</strong> " . htmlspecialchars($package['destination']) . "</p>\n";
            echo "<p><strong>Price:</strong> Rs. " . number_format($package['price'], 2) . "</p>\n";
            echo "<p><strong>Duration:</strong> " . htmlspecialchars($package['duration']) . "</p>\n";
            echo "<p><strong>Available Slots:</strong> " . $package['available_slots'] . "</p>\n";
            echo "<p><strong>Food Type:</strong> " . htmlspecialchars($package['food_type']) . "</p>\n";
            echo "<p><strong>Accommodation:</strong> " . htmlspecialchars($package['accommodation']) . "</p>\n";
            echo "<p><strong>Transportation:</strong> " . htmlspecialchars($package['transportation']) . "</p>\n";
            echo "<p><strong>Image:</strong> " . htmlspecialchars($package['image']) . "</p>\n";
            
            // Display associated activities
            $activities_query = mysqli_query($conn, "SELECT a.name FROM activities a JOIN package_activities pa ON a.activity_id = pa.activity_id WHERE pa.package_id = " . $package['package_id']);
            if(mysqli_num_rows($activities_query) > 0) {
                echo "<p><strong>Activities:</strong> ";
                $activity_names = [];
                while($activity = mysqli_fetch_assoc($activities_query)) {
                    $activity_names[] = $activity['name'];
                }
                echo implode(", ", $activity_names) . "</p>\n";
            }
            
            // Display associated drivers
            $drivers_query = mysqli_query($conn, "SELECT d.name FROM drivers d JOIN package_drivers pd ON d.driver_id = pd.driver_id WHERE pd.package_id = " . $package['package_id']);
            if(mysqli_num_rows($drivers_query) > 0) {
                echo "<p><strong>Drivers:</strong> ";
                $driver_names = [];
                while($driver = mysqli_fetch_assoc($drivers_query)) {
                    $driver_names[] = $driver['name'];
                }
                echo implode(", ", $driver_names) . "</p>\n";
            }
            
            // Display associated guides
            $guides_query = mysqli_query($conn, "SELECT g.name FROM guides g JOIN package_guides pg ON g.guide_id = pg.guide_id WHERE pg.package_id = " . $package['package_id']);
            if(mysqli_num_rows($guides_query) > 0) {
                echo "<p><strong>Guides:</strong> ";
                $guide_names = [];
                while($guide = mysqli_fetch_assoc($guides_query)) {
                    $guide_names[] = $guide['name'];
                }
                echo implode(", ", $guide_names) . "</p>\n";
            }
            
            // Display food details
            if(!empty($package['breakfast'])) {
                echo "<p><strong>Breakfast:</strong> " . htmlspecialchars($package['breakfast']) . "</p>\n";
            }
            if(!empty($package['lunch'])) {
                echo "<p><strong>Lunch:</strong> " . htmlspecialchars($package['lunch']) . "</p>\n";
            }
            if(!empty($package['dinner'])) {
                echo "<p><strong>Dinner:</strong> " . htmlspecialchars($package['dinner']) . "</p>\n";
            }
            
            echo "</div>\n";
        }
        echo "</div>\n";
    } else {
        echo "<p>No packages found in the database.</p>\n";
    }
}

// Function to add new random packages
function addRandomPackages($conn, $count = 8) {
    echo "<h1>Adding $count New Random Packages</h1>\n";
    
    // Get list of available images
    $images = array(
        '2325386-2800x1866-desktop-hd-porsche-911-gt3-rs-wallpaper-photo.jpg',
        'IMG-20250930-WA0194.jpg',
        'anime-car-city.jpg',
        'backwaters_beach.jpg',
        'black-cat-black-background-hat-suit-cigar-scary-3840x2160-6597.jpg',
        'dark-style-sky-nighttime.jpg',
        'ellappatty.jpg',
        'kerala_culture.jpg',
        'munnar.jpg',
        'pak_ellappetty.jpg',
        'pak_ellappetty1.jpg',
        'pak_vattavada.webp',
        'wallhaven-7p53ve_1920x1080.png',
        'wayanad.jpg',
        'wildlife_safari.jpg',
        'yoga.jpg'
    );

    // Get list of destinations
    $destinations = array(
        'Munnar', 'Wayanad', 'Alleppey', 'Kochi', 'Trivandrum', 
        'Varkala', 'Thekkady', 'Kovalam', 'Thrissur', 'Palakkad'
    );

    // Get list of accommodations
    $accommodations = array(
        '3 Star Hotel', '4 Star Hotel', '5 Star Hotel', 'Resort', 
        'Beach Resort', 'Heritage Hotel', 'Homestay', 'Eco Lodge', 
        'Houseboat', 'Cottage', 'Villa', 'Boutique Hotel'
    );

    // Get list of food types
    $food_types = array(
        'Vegetarian', 'Non-Vegetarian', 'Vegan', 'Jain', 
        'Multi-Cuisine', 'Kerala Specialities', 'South Indian', 
        'North Indian', 'Chinese', 'Continental'
    );

    // Get list of transportation types
    $transportation_types = array(
        'AC Bus', 'Non-AC Bus', 'AC SUV', 'Sedan Car', 
        'Innova', 'Private Taxi', 'Jeep Safari', 'Houseboat', 
        'Flight', 'Train'
    );

    // Get list of activities (IDs 1-10 exist in the database)
    $activities = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    
    // Get list of drivers (IDs 1-4 exist in the database)
    $drivers = array(1, 2, 3, 4);
    
    // Get list of guides (IDs 1-3 exist in the database)
    $guides = array(1, 2, 3);
    
    // Get list of buses (IDs 1-3 exist in the database)
    $buses = array(1, 2, 3);

    for ($i = 1; $i <= $count; $i++) {
        // Randomly select values
        $destination = $destinations[array_rand($destinations)];
        $name = $destination . " Experience Tour " . (7 + $i);
        $price = rand(3000, 25000);
        $duration_days = rand(2, 8);
        $duration_nights = $duration_days - 1;
        $duration = $duration_days . " Days " . $duration_nights . " Nights";
        $description = "Experience the beauty of " . $destination . " with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.";
        $food_type = $food_types[array_rand($food_types)];
        $accommodation = $accommodations[array_rand($accommodations)];
        $available_slots = rand(5, 20);
        $pickup_point = $destinations[array_rand($destinations)];
        $dropoff_point = $destinations[array_rand($destinations)];
        $transportation = $transportation_types[array_rand($transportation_types)];
        $image = $images[array_rand($images)];
        
        // Create food details
        $breakfast = "Continental and Indian breakfast buffet";
        $lunch = "Multi-cuisine lunch with vegetarian and non-vegetarian options";
        $dinner = "Traditional Kerala Sadya dinner with a variety of dishes";
        
        // Escape strings to prevent SQL injection
        $destination = mysqli_real_escape_string($conn, $destination);
        $name = mysqli_real_escape_string($conn, $name);
        $description = mysqli_real_escape_string($conn, $description);
        $food_type = mysqli_real_escape_string($conn, $food_type);
        $accommodation = mysqli_real_escape_string($conn, $accommodation);
        $pickup_point = mysqli_real_escape_string($conn, $pickup_point);
        $dropoff_point = mysqli_real_escape_string($conn, $dropoff_point);
        $transportation = mysqli_real_escape_string($conn, $transportation);
        $image = mysqli_real_escape_string($conn, $image);
        $breakfast = mysqli_real_escape_string($conn, $breakfast);
        $lunch = mysqli_real_escape_string($conn, $lunch);
        $dinner = mysqli_real_escape_string($conn, $dinner);
        
        // Create random schedule
        $day1_schedule = "Arrival at " . $destination . " and check-in at accommodation. Evening leisure activities.";
        $day2_schedule = "Full day sightseeing tour of " . $destination . " including major attractions and cultural sites.";
        $day3_schedule = "Adventure activities and local experiences in " . $destination . ".";
        
        // Escape schedule strings
        $day1_schedule = mysqli_real_escape_string($conn, $day1_schedule);
        $day2_schedule = mysqli_real_escape_string($conn, $day2_schedule);
        $day3_schedule = mysqli_real_escape_string($conn, $day3_schedule);
        
        // Create random food menu
        $food_menu = "Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - " . $food_type . "\nDay 3: Breakfast, Lunch - Local specialities";
        $food_menu = mysqli_real_escape_string($conn, $food_menu);
        
        // Create random stay details
        $stay_details = "Day 1-3: " . $accommodation . " in " . $destination . " (3 Star)";
        $stay_details = mysqli_real_escape_string($conn, $stay_details);
        
        // Create random transportation details
        $transportation_details = $transportation . " with experienced driver. All transfers and sightseeing by private vehicle.";
        $transportation_details = mysqli_real_escape_string($conn, $transportation_details);
        
        // Create random activities string
        $random_activities = array();
        for ($j = 0; $j < 3; $j++) {
            $random_activities[] = $activities[array_rand($activities)];
        }
        $activities_string = implode(", ", $random_activities);
        $activities_string = mysqli_real_escape_string($conn, $activities_string);
        
        // Prepare the insert query
        $insert_query = "INSERT INTO packages (
            destination, name, price, duration, description, food_type, accommodation, 
            available_slots, pickup_point, dropoff_point, transportation, image, 
            day1_schedule, day2_schedule, day3_schedule, food_menu, stay_details, 
            transportation_details, activities, breakfast, lunch, dinner
        ) VALUES (
            '$destination', '$name', $price, '$duration', '$description', '$food_type', '$accommodation',
            $available_slots, '$pickup_point', '$dropoff_point', '$transportation', '$image',
            '$day1_schedule', '$day2_schedule', '$day3_schedule', '$food_menu', '$stay_details',
            '$transportation_details', '$activities_string', '$breakfast', '$lunch', '$dinner'
        )";
        
        if (mysqli_query($conn, $insert_query)) {
            $package_id = mysqli_insert_id($conn);
            echo "✓ Package '$name' inserted successfully with ID: $package_id<br>\n";
            
            // Assign random activities to this package (3 random activities)
            $selected_activities = array_rand(array_flip($activities), 3);
            foreach ($selected_activities as $activity_id) {
                $insert_activity_query = "INSERT INTO package_activities (package_id, activity_id) VALUES ($package_id, $activity_id)";
                if (mysqli_query($conn, $insert_activity_query)) {
                    echo "&nbsp;&nbsp;✓ Activity ID $activity_id assigned to package $package_id<br>\n";
                } else {
                    echo "&nbsp;&nbsp;✗ Error assigning activity: " . mysqli_error($conn) . "<br>\n";
                }
            }
            
            // Assign random driver to this package
            $driver_id = $drivers[array_rand($drivers)];
            $insert_driver_query = "INSERT INTO package_drivers (package_id, driver_id) VALUES ($package_id, $driver_id)";
            if (mysqli_query($conn, $insert_driver_query)) {
                echo "&nbsp;&nbsp;✓ Driver ID $driver_id assigned to package $package_id<br>\n";
            } else {
                echo "&nbsp;&nbsp;✗ Error assigning driver: " . mysqli_error($conn) . "<br>\n";
            }
            
            // Assign random guide to this package
            $guide_id = $guides[array_rand($guides)];
            $insert_guide_query = "INSERT INTO package_guides (package_id, guide_id) VALUES ($package_id, $guide_id)";
            if (mysqli_query($conn, $insert_guide_query)) {
                echo "&nbsp;&nbsp;✓ Guide ID $guide_id assigned to package $package_id<br>\n";
            } else {
                echo "&nbsp;&nbsp;✗ Error assigning guide: " . mysqli_error($conn) . "<br>\n";
            }
            
            // Assign random bus to this package
            $bus_id = $buses[array_rand($buses)];
            $insert_bus_query = "INSERT INTO package_buses (package_id, bus_id) VALUES ($package_id, $bus_id)";
            if (mysqli_query($conn, $insert_bus_query)) {
                echo "&nbsp;&nbsp;✓ Bus ID $bus_id assigned to package $package_id<br>\n";
            } else {
                echo "&nbsp;&nbsp;✗ Error assigning bus: " . mysqli_error($conn) . "<br>\n";
            }
        } else {
            echo "✗ Error inserting package: " . mysqli_error($conn) . "<br>\n";
        }
        
        echo "<br>\n";
    }
}

// Display current packages
displayCurrentPackages($conn);

// Add new random packages
addRandomPackages($conn, 8);

echo "<br><a href='packages.php'>View All Packages</a>\n";
?>