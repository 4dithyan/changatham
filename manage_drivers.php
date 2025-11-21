<?php
session_start();
include 'config.php';

// Select the database
mysqli_select_db($conn, $database);

// Check if user is admin
if (!isset($_SESSION['EMAIL']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Drivers - Admin Panel";

// Handle adding a new driver
if (isset($_POST['add_driver'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $insert = "INSERT INTO drivers (name, phone) VALUES ('$name', '$phone')";
    if (mysqli_query($conn, $insert)) {
        $_SESSION['success'] = "Driver added successfully!";
    } else {
        $_SESSION['error'] = "Error adding driver: " . mysqli_error($conn);
    }
    // Redirect to prevent form resubmission
    header("Location: manage_drivers.php");
    exit();
}

// Handle deleting a driver
if (isset($_GET['delete_driver'])) {
    $driver_id = intval($_GET['delete_driver']);
    $delete = "DELETE FROM drivers WHERE driver_id = $driver_id";
    if (mysqli_query($conn, $delete)) {
        $_SESSION['success'] = "Driver deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting driver: " . mysqli_error($conn);
    }
    // Redirect to prevent URL manipulation
    header("Location: manage_drivers.php");
    exit();
}

// Fetch messages from session and clear them
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['success']);
unset($_SESSION['error']);

// Fetch all drivers
$drivers = mysqli_query($conn, "SELECT * FROM drivers ORDER BY name");

include 'includes/admin_header.php';
?>

                <div class="container mt-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Add Driver Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Add New Driver</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Driver Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="submit" name="add_driver" class="btn btn-primary w-100">Add Driver</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Drivers List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Drivers List</h5>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($drivers) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($driver = mysqli_fetch_assoc($drivers)): ?>
                                                <tr>
                                                    <td><?php echo $driver['driver_id']; ?></td>
                                                    <td><?php echo $driver['name']; ?></td>
                                                    <td><?php echo $driver['phone']; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($driver['created_at'])); ?></td>
                                                    <td>
                                                        <a href="?delete_driver=<?php echo $driver['driver_id']; ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this driver?')">
                                                            Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No drivers found. Add your first driver using the form above.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

<?php include 'includes/admin_footer.php'; ?>