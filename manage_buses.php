<?php
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Buses";

// Handle Add Bus
if (isset($_POST['add_bus'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Image upload
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = "uploads/";
        move_uploaded_file($tmp_name, $upload_dir.$image);
    }

    $insert = "INSERT INTO buses (name, type, capacity, description, image) VALUES ('$name', '$type', '$capacity', '$description', '$image')";
    
    if (mysqli_query($conn, $insert)) {
        $success = "Bus added successfully!";
    } else {
        $error = "Error adding bus: " . mysqli_error($conn);
    }
}

// Handle Update Bus
if (isset($_POST['update_bus'])) {
    $bus_id = $_POST['bus_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Update image if provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = "uploads/";
        move_uploaded_file($tmp_name, $upload_dir.$image);
        $update = "UPDATE buses SET name='$name', type='$type', capacity='$capacity', description='$description', image='$image' WHERE bus_id='$bus_id'";
    } else {
        $update = "UPDATE buses SET name='$name', type='$type', capacity='$capacity', description='$description' WHERE bus_id='$bus_id'";
    }

    if (mysqli_query($conn, $update)) {
        $success = "Bus updated successfully!";
    } else {
        $error = "Error updating bus: " . mysqli_error($conn);
    }
}

// Handle Delete Bus
if (isset($_POST['delete_bus'])) {
    $bus_id = $_POST['bus_id'];
    $delete = "DELETE FROM buses WHERE bus_id='$bus_id'";
    if (mysqli_query($conn, $delete)) {
        $success = "Bus deleted successfully!";
    } else {
        $error = "Error deleting bus: " . mysqli_error($conn);
    }
}

// Fetch all buses
$buses = mysqli_query($conn, "SELECT * FROM buses ORDER BY name");

include 'includes/admin_header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="section-title">Manage Buses</h1>
            <p class="lead">Add, update, or remove buses used for travel packages.</p>
        </div>
    </div>
    
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
    
    <!-- Add Bus Form -->
    <div class="card form-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add New Bus</h5>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bus Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="AC">AC</option>
                            <option value="Non-AC">Non-AC</option>
                            <option value="Sleeper">Sleeper</option>
                            <option value="Semi-Sleeper">Semi-Sleeper</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Capacity *</label>
                        <input type="number" name="capacity" class="form-control" min="1" required>
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
                <div class="d-grid">
                    <button type="submit" name="add_bus" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>Add Bus
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Existing Buses -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Existing Buses</h5>
        </div>
        <div class="card-body">
            <?php if(mysqli_num_rows($buses) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($bus = mysqli_fetch_assoc($buses)): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($bus['image'])): ?>
                                        <img src="uploads/<?php echo $bus['image']; ?>" alt="<?php echo $bus['name']; ?>" class="img-fluid" style="max-height: 50px;">
                                    <?php else: ?>
                                        <i class="bi bi-bus" style="font-size: 2rem;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $bus['name']; ?></td>
                                <td><?php echo $bus['type']; ?></td>
                                <td><?php echo $bus['capacity']; ?> seats</td>
                                <td><?php echo substr($bus['description'], 0, 50) . (strlen($bus['description']) > 50 ? '...' : ''); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Update Button -->
                                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $bus['bus_id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Delete Form -->
                                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this bus?')">
                                            <input type="hidden" name="bus_id" value="<?php echo $bus['bus_id']; ?>">
                                            <button type="submit" name="delete_bus" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-bus" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3">No buses found</h4>
                    <p class="text-muted">Add your first bus using the form above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Update Modals -->
<?php 
// Reset the buses result set
mysqli_data_seek($buses, 0);
while($bus = mysqli_fetch_assoc($buses)) { 
?>
<!-- Update Modal for Bus <?php echo $bus['bus_id']; ?> -->
<div class="modal fade" id="updateModal<?php echo $bus['bus_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $bus['bus_id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel<?php echo $bus['bus_id']; ?>">Update Bus: <?php echo $bus['name']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="bus_id" value="<?php echo $bus['bus_id']; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bus Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($bus['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="AC" <?php echo $bus['type'] == 'AC' ? 'selected' : ''; ?>>AC</option>
                                <option value="Non-AC" <?php echo $bus['type'] == 'Non-AC' ? 'selected' : ''; ?>>Non-AC</option>
                                <option value="Sleeper" <?php echo $bus['type'] == 'Sleeper' ? 'selected' : ''; ?>>Sleeper</option>
                                <option value="Semi-Sleeper" <?php echo $bus['type'] == 'Semi-Sleeper' ? 'selected' : ''; ?>>Semi-Sleeper</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacity *</label>
                            <input type="number" name="capacity" class="form-control" min="1" value="<?php echo $bus['capacity']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                            <?php if(!empty($bus['image'])): ?>
                                <small class="form-text text-muted">Current image: <?php echo $bus['image']; ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($bus['description']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_bus" class="btn btn-primary">Update Bus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php include 'includes/admin_footer.php'; ?>