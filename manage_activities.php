<?php
session_start();
include 'config.php'; // Database connection

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Activities";

// Check if activities table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'activities'");
if (mysqli_num_rows($table_check) == 0) {
    $error = "Activities table does not exist. Please run the database update script first.";
} else {
    // Handle Add Activity
    if (isset($_POST['add_activity'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        // Image upload
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = "uploads/";
        move_uploaded_file($tmp_name, $upload_dir.$image);

        $insert = "INSERT INTO activities (name, description, image) VALUES ('$name','$description','$image')";
        if (mysqli_query($conn, $insert)) {
            $success = "Activity added successfully!";
        } else {
            $error = "Error adding activity: " . mysqli_error($conn);
        }
    }

    // Handle Delete Activity
    if (isset($_POST['delete_activity'])) {
        $activity_id = $_POST['activity_id'];
        $delete = "DELETE FROM activities WHERE activity_id='$activity_id'";
        if (mysqli_query($conn, $delete)) {
            $success = "Activity deleted successfully!";
        } else {
            $error = "Error deleting activity: " . mysqli_error($conn);
        }
    }

    // Handle Update Activity
    if (isset($_POST['update_activity'])) {
        $activity_id = $_POST['activity_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Update image if provided
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $upload_dir = "uploads/";
            move_uploaded_file($tmp_name, $upload_dir.$image);
            $update = "UPDATE activities SET name='$name', description='$description', image='$image' WHERE activity_id='$activity_id'";
        } else {
            $update = "UPDATE activities SET name='$name', description='$description' WHERE activity_id='$activity_id'";
        }

        if (mysqli_query($conn, $update)) {
            $success = "Activity updated successfully!";
        } else {
            $error = "Error updating activity: " . mysqli_error($conn);
        }
    }

    // Fetch all activities
    $activities = mysqli_query($conn, "SELECT * FROM activities ORDER BY activity_id DESC");
}

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

<?php if (isset($error) && strpos($error, 'Activities table does not exist') !== false): ?>
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Database Setup Required</h5>
        </div>
        <div class="card-body">
            <p>The activities table does not exist in your database. Please run the database update script to create the required tables.</p>
            <p>You can do this in one of the following ways:</p>
            <ol>
                <li>Visit <a href="update_database.php">update_database.php</a> in your browser</li>
                <li>Run the script from command line: <code>php update_database.php</code></li>
            </ol>
            <p>After running the script, refresh this page to manage activities.</p>
        </div>
    </div>
<?php else: ?>
    <!-- Add Activity Form -->
    <div class="card form-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add New Activity</h5>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Activity Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" name="add_activity" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Add Activity
                </button>
            </form>
        </div>
    </div>

    <!-- Activities List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Activities</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php while($activity = mysqli_fetch_assoc($activities)) { ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 activity-card">
                            <?php if(!empty($activity['image'])): ?>
                                <img src="uploads/<?php echo $activity['image']; ?>" alt="<?php echo $activity['name']; ?>" class="card-img-top activity-img">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $activity['name']; ?></h5>
                                <p><?php echo substr($activity['description'], 0, 100) . '...'; ?></p>

                                <form method="post" class="mb-2">
                                    <input type="hidden" name="activity_id" value="<?php echo $activity['activity_id']; ?>">
                                    <button type="submit" name="delete_activity" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this activity?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>

                                <button class="btn btn-primary w-100 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#update-<?php echo $activity['activity_id']; ?>">
                                    <i class="bi bi-pencil"></i> Update
                                </button>
                                <div class="collapse" id="update-<?php echo $activity['activity_id']; ?>">
                                    <div class="card card-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="activity_id" value="<?php echo $activity['activity_id']; ?>">
                                            
                                            <div class="mb-2">
                                                <label class="form-label">Activity Name</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo $activity['name']; ?>" required>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?php echo $activity['description']; ?></textarea>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <label class="form-label">Image (Leave empty to keep current)</label>
                                                <input type="file" name="image" class="form-control">
                                                <?php if(!empty($activity['image'])): ?>
                                                    <small>Current: <?php echo $activity['image']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <button type="submit" name="update_activity" class="btn btn-success w-100">
                                                <i class="bi bi-check-circle"></i> Update Activity
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>