<?php
// Script to send package date reminders to users
include 'config.php';

// Function to send package date reminders
function sendPackageReminders($conn) {
    // Get packages that are starting in the next 7 days
    $reminder_query = "SELECT b.user_id, b.booking_id, p.package_id, p.name as package_name, p.start_date, r.name as user_name, r.email
                      FROM bookings b
                      JOIN packages p ON b.package_id = p.package_id
                      JOIN register r ON b.user_id = r.user_id
                      WHERE b.status = 'Confirmed' 
                      AND p.start_date IS NOT NULL
                      AND p.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                      AND b.user_id NOT IN (
                          SELECT un.user_id 
                          FROM user_notifications un
                          JOIN notifications n ON un.notification_id = n.id
                          WHERE n.title = 'Package Reminder'
                          AND n.package_id = p.package_id
                          AND DATE(n.created_at) = CURDATE()
                      )";
    
    $reminder_result = mysqli_query($conn, $reminder_query);
    
    $notifications_sent = 0;
    
    while ($reminder = mysqli_fetch_assoc($reminder_result)) {
        $user_id = $reminder['user_id'];
        $package_name = $reminder['package_name'];
        $start_date = $reminder['start_date'];
        $package_id = $reminder['package_id'];
        
        // Calculate days until start
        $start_date_obj = new DateTime($start_date);
        $today = new DateTime();
        $interval = $today->diff($start_date_obj);
        $days_until = $interval->days;
        
        // Create notification
        $title = "Package Reminder";
        $message = "Your package '$package_name' is starting in $days_until day" . ($days_until != 1 ? "s" : "") . " on " . date('d M Y', strtotime($start_date)) . ". Please prepare accordingly.";
        $type = "info";
        
        $insert_notification = "INSERT INTO notifications (package_id, title, message, type) VALUES ('$package_id', '$title', '$message', '$type')";
        
        if (mysqli_query($conn, $insert_notification)) {
            $notification_id = mysqli_insert_id($conn);
            
            // Create user notification
            $insert_user_notification = "INSERT INTO user_notifications (user_id, notification_id) VALUES ('$user_id', '$notification_id')";
            if (mysqli_query($conn, $insert_user_notification)) {
                $notifications_sent++;
            }
        }
    }
    
    return $notifications_sent;
}

// Run the function
$notifications_sent = sendPackageReminders($conn);

// Output result
echo "Package reminders sent: " . $notifications_sent . "\n";
?>