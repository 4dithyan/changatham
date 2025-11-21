<?php
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "Manage Gallery";
include 'includes/admin_header.php';

// Handle image upload
if (isset($_POST['add_image'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/gallery/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Insert into database
                $insert_query = "INSERT INTO gallery (title, image_path, description) VALUES ('$title', '$target_file', '$description')";
                if (mysqli_query($conn, $insert_query)) {
                    // Redirect to prevent form resubmission
                    header("Location: admin_gallery.php?success=1");
                    exit();
                } else {
                    $error_message = "Database error: " . mysqli_error($conn);
                }
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error_message = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $error_message = "Please select an image to upload.";
    }
}

// Handle image update
if (isset($_POST['update_image'])) {
    $id = intval($_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $update_query = "UPDATE gallery SET title='$title', description='$description' WHERE id=$id";
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Image updated successfully!";
    } else {
        $error_message = "Database error: " . mysqli_error($conn);
    }
}

// Handle image deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get image path to delete the file
    $select_query = "SELECT image_path FROM gallery WHERE id = $id";
    $result = mysqli_query($conn, $select_query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Delete the file from server
        if (file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
        
        // Delete from database
        $delete_query = "DELETE FROM gallery WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            // Redirect to prevent duplicate actions
            header("Location: admin_gallery.php?deleted=1");
            exit();
        } else {
            $error_message = "Database error: " . mysqli_error($conn);
        }
    }
}

// Handle success messages from redirects
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Image uploaded successfully!";
}

if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success_message = "Image deleted successfully!";
}

// Fetch all gallery images
$gallery_query = "SELECT * FROM gallery ORDER BY id DESC";
$gallery_result = mysqli_query($conn, $gallery_query);
?>

<div class="container-fluid mt-4">
    <!-- Alerts -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Add New Image Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add New Gallery Image</h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="add_image" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i> Add Image
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gallery Images -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gallery Images</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($gallery_result) > 0): ?>
                        <div class="row g-4">
                            <?php while ($image = mysqli_fetch_assoc($gallery_result)): ?>
                                <div class="col-md-6 col-lg-4 col-xl-3">
                                    <div class="card h-100">
                                        <img src="<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title"><?php echo $image['title']; ?></h6>
                                            <p class="card-text flex-grow-1"><?php echo $image['description']; ?></p>
                                            <div class="mt-auto">
                                                <small class="text-muted">Added: <?php echo date('M j, Y', strtotime($image['created_at'])); ?></small>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $image['id']; ?>">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </button>
                                            <a href="admin_gallery.php?delete=<?php echo $image['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?')">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $image['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $image['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?php echo $image['id']; ?>">Edit Gallery Image</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Title</label>
                                                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($image['title']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($image['description']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Current Image</label>
                                                        <img src="<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="update_image" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-images" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="mt-3">No gallery images found. Add your first image above.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>