<?php
session_start();
include 'config.php'; // Database connection

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$page_title = "User Management - Admin Panel";

// Handle user role update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];
    
    // Prevent users from demoting themselves
    if ($user_id == $_SESSION['USER_ID'] && $new_role != 'admin') {
        $error = "You cannot demote yourself!";
    } else {
        $stmt = $conn->prepare("UPDATE register SET ROLE = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        
        if ($stmt->execute()) {
            $success = "User role updated successfully!";
        } else {
            $error = "Error updating user role: " . $conn->error;
        }
    }
}

// Handle new admin creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_admin'])) {
    $name = mysqli_real_escape_string($conn, $_POST['admin_name']);
    $email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $phone = mysqli_real_escape_string($conn, $_POST['admin_phone']);
    $password = mysqli_real_escape_string($conn, $_POST['admin_password']);
    
    // Check if user already exists
    $check = $conn->prepare("SELECT * FROM register WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $error = "User with this email already exists!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'admin';
        
        $stmt = $conn->prepare("INSERT INTO register (name, email, user_ph, password, ROLE) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $success = "New admin user created successfully!";
        } else {
            $error = "Error creating admin user: " . $conn->error;
        }
    }
}

include 'includes/admin_header.php';
?>

                    <!-- Alerts -->
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Create New Admin Form -->
                    <div class="form-section">
                        <h5><i class="bi bi-person-plus"></i> Create New Admin</h5>
                        <form method="POST">
                            <input type="hidden" name="create_admin" value="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="admin_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="admin_email" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="admin_phone" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="admin_password" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-person-plus"></i> Create Admin
                            </button>
                        </form>
                    </div>

                    <!-- All Users Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">All Users</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped recent-table">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch all users
                                        $query = "SELECT user_id, name, email, user_ph, ROLE FROM register ORDER BY user_id DESC";
                                        $result = mysqli_query($conn, $query);

                                        if(mysqli_num_rows($result) > 0) {
                                            while($user = mysqli_fetch_assoc($result)) {
                                                echo "<tr>
                                                        <td>{$user['user_id']}</td>
                                                        <td>{$user['name']}</td>
                                                        <td>{$user['email']}</td>
                                                        <td>{$user['user_ph']}</td>
                                                        <td>
                                                            <span class='badge bg-" . ($user['ROLE'] == 'admin' ? 'danger' : 'primary') . "'>
                                                                {$user['ROLE']}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <form method='POST' class='d-inline'>
                                                                <input type='hidden' name='user_id' value='{$user['user_id']}'>
                                                                <select name='new_role' class='form-select form-select-sm d-inline w-auto'>";
                                                
                                                // Show role options
                                                $roles = ['user', 'admin'];
                                                foreach ($roles as $role) {
                                                    $selected = ($role == $user['ROLE']) ? 'selected' : '';
                                                    echo "<option value='$role' $selected>" . ucfirst($role) . "</option>";
                                                }
                                                
                                                echo "          </select>
                                                                <input type='hidden' name='update_role' value='1'>
                                                                <button type='submit' class='btn btn-sm btn-outline-primary'>
                                                                    <i class='bi bi-arrow-repeat'></i> Update
                                                                </button>
                                                            </form>
                                                        </td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/admin_footer.php'; ?>