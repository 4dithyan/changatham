<?php
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission
if (isset($_POST['update_dates'])) {
    $package_id = intval($_POST['package_id']);
    $start_date = !empty($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : 'NULL';
    $end_date = !empty($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : 'NULL';
    $daily_start_time = !empty($_POST['daily_start_time']) ? mysqli_real_escape_string($conn, $_POST['daily_start_time']) : 'NULL';
    $daily_end_time = !empty($_POST['daily_end_time']) ? mysqli_real_escape_string($conn, $_POST['daily_end_time']) : 'NULL';
    
    // Handle NULL values properly in SQL
    $start_date = !empty($_POST['start_date']) ? "'$start_date'" : 'NULL';
    $end_date = !empty($_POST['end_date']) ? "'$end_date'" : 'NULL';
    $daily_start_time = !empty($_POST['daily_start_time']) ? "'$daily_start_time'" : 'NULL';
    $daily_end_time = !empty($_POST['daily_end_time']) ? "'$daily_end_time'" : 'NULL';
    
    $update_query = "UPDATE packages SET 
                     start_date=$start_date, 
                     end_date=$end_date, 
                     daily_start_time=$daily_start_time, 
                     daily_end_time=$daily_end_time 
                     WHERE package_id=$package_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success = "Package dates and times updated successfully.";
    } else {
        $error = "Error updating package: " . mysqli_error($conn);
    }
}

// Fetch all packages
$packages_query = "SELECT package_id, name, start_date, end_date, daily_start_time, daily_end_time FROM packages ORDER BY package_id";
$packages_result = mysqli_query($conn, $packages_query);

$page_title = "Update Package Dates & Times";
include 'includes/admin_header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="section-title">Update Package Dates & Times</h1>
            <p class="lead">Update date and time information for all travel packages</p>
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
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Update Package Date & Time Information</h5>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Select Package *</label>
                        <select name="package_id" class="form-select" required>
                            <option value="">Choose a package</option>
                            <?php 
                            mysqli_data_seek($packages_result, 0);
                            while($package = mysqli_fetch_assoc($packages_result)) {
                                echo '<option value="'.$package['package_id'].'">'.$package['name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
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
                
                <div class="d-grid">
                    <button type="submit" name="update_dates" class="btn btn-primary btn-lg">
                        <i class="bi bi-calendar-check me-2"></i>Update Package Dates & Times
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Current Package Date & Time Information</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Daily Start Time</th>
                            <th>Daily End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($packages_result, 0);
                        while($package = mysqli_fetch_assoc($packages_result)) {
                            echo '<tr>';
                            echo '<td>'.$package['name'].'</td>';
                            echo '<td>'.(!empty($package['start_date']) ? date('d M Y', strtotime($package['start_date'])) : 'Not specified').'</td>';
                            echo '<td>'.(!empty($package['end_date']) ? date('d M Y', strtotime($package['end_date'])) : 'Not specified').'</td>';
                            echo '<td>'.(!empty($package['daily_start_time']) ? date('g:i A', strtotime($package['daily_start_time'])) : 'Not specified').'</td>';
                            echo '<td>'.(!empty($package['daily_end_time']) ? date('g:i A', strtotime($package['daily_end_time'])) : 'Not specified').'</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>