<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];

// Fetch all user's notifications
$notifications_query = "SELECT n.*, p.name as package_name, un.is_read, un.id as user_notification_id
                       FROM user_notifications un 
                       JOIN notifications n ON un.notification_id = n.id 
                       JOIN packages p ON n.package_id = p.package_id 
                       WHERE un.user_id = '$user_id' 
                       ORDER BY n.created_at DESC";
$notifications_result = mysqli_query($conn, $notifications_query);

// Mark all notifications as read when user visits this page
$update_read_status = "UPDATE user_notifications SET is_read = 1 WHERE user_id = '$user_id'";
mysqli_query($conn, $update_read_status);

// Count unread notifications
$unread_count_query = "SELECT COUNT(*) as unread_count 
                      FROM user_notifications 
                      WHERE user_id = '$user_id' AND is_read = 0";
$unread_count_result = mysqli_query($conn, $unread_count_query);
$unread_count = mysqli_fetch_assoc($unread_count_result)['unread_count'];

$page_title = "My Notifications - Changatham";
include 'includes/dashboard_header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="section-title">My Notifications</h1>
            <p class="lead">Stay updated with the latest information about your booked packages.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-header">
                    <h4 class="mb-0">All Notifications</h4>
                </div>
                <div class="card-body">
                    <?php if(mysqli_num_rows($notifications_result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Package</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($notification = mysqli_fetch_assoc($notifications_result)): ?>
                                    <tr>
                                        <td><?php echo $notification['package_name']; ?></td>
                                        <td><?php echo $notification['title']; ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo $notification['type'] == 'delayed' ? 'bg-warning' : 
                                                     ($notification['type'] == 'cancelled' ? 'bg-danger' : 
                                                     ($notification['type'] == 'scheduled' ? 'bg-success' : 'bg-info')); 
                                            ?>">
                                                <?php echo ucfirst($notification['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y, H:i', strtotime($notification['created_at'])); ?></td>
                                        <td>
                                            <?php if($notification['is_read'] == 0): ?>
                                                <span class="badge bg-warning">Unread</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Read</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            <div class="notification-message bg-light p-3 rounded">
                                                <?php echo nl2br(htmlspecialchars($notification['message'])); ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">No notifications yet</h3>
                            <p class="text-muted">You'll see notifications here when there are updates about your booked packages.</p>
                            <a href="packages.php" class="btn btn-modern mt-3">Browse Packages</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>