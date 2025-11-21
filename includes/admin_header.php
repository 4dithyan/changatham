<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['ROLE']) || $_SESSION['ROLE'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard - Changatham'; ?></title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00695c;
            --secondary: #004d40;
            --accent: #ffeb3b;
            --light: #f8f9fa;
            --dark: #212529;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            overflow-x: hidden;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 2px solid var(--accent);
        }

        .sidebar .logo h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            margin: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255, 235, 59, 0.2);
            color: var(--accent);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
        }

        .topbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar .page-title {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 1.6rem;
            color: var(--primary);
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 12px;
            border: 2px solid var(--accent);
        }

        .user-name {
            font-weight: 500;
            color: var(--dark);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                text-align: center;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .sidebar .logo h3 {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .user-info {
                margin-top: 15px;
            }
        }
    </style>
    <?php 
    // Additional page-specific CSS if needed
    if (isset($additional_css)) {
        echo '<style>' . $additional_css . '</style>';
    }
    ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 sidebar p-0">
                <div class="logo">
                    <h3>Changatham</h3>
                </div>
                <ul class="nav flex-column py-3">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_page.php') ? 'active' : ''; ?>" href="admin_page.php">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_packages.php') ? 'active' : ''; ?>" href="manage_packages.php">
                            <i class="bi bi-bag"></i> <span>Manage Packages</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_bookings.php') ? 'active' : ''; ?>" href="manage_bookings.php">
                            <i class="bi bi-book"></i> <span>Manage Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_package.php') ? 'active' : ''; ?>" href="add_package.php">
                            <i class="bi bi-plus-circle"></i> <span>Add Package</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_activities.php') ? 'active' : ''; ?>" href="manage_activities.php">
                            <i class="bi bi-activity"></i> <span>Activities</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_drivers.php') ? 'active' : ''; ?>" href="manage_drivers.php">
                            <i class="bi bi-person-badge"></i> <span>Manage Drivers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_guides.php') ? 'active' : ''; ?>" href="manage_guides.php">
                            <i class="bi bi-person-circle"></i> <span>Manage Guides</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_buses.php') ? 'active' : ''; ?>" href="manage_buses.php">
                            <i class="bi bi-bus-front"></i> <span>Manage Buses</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_gallery.php') ? 'active' : ''; ?>" href="admin_gallery.php">
                            <i class="bi bi-images"></i> <span>Manage Gallery</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_users.php') ? 'active' : ''; ?>" href="admin_users.php">
                            <i class="bi bi-people"></i> <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_package_report.php') ? 'active' : ''; ?>" href="admin_package_report.php">
                            <i class="bi bi-file-earmark-bar-graph"></i> <span>Package Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'admin_update_dates.php') ? 'active' : ''; ?>" href="admin_update_dates.php">
                            <i class="bi bi-calendar-date"></i> <span>Update Dates</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> <span>Logout (<?php echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Admin'; ?>)</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9 main-content">
                <!-- Top Navbar -->
                <div class="topbar d-flex justify-content-between align-items-center">
                    <h4 class="page-title"><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?></h4>
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php 
                            $user_name = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : $_SESSION['EMAIL'];
                            echo strtoupper(substr($user_name, 0, 1));
                            ?>
                        </div>
                        <div class="user-name">
                            <?php echo $user_name; ?>
                        </div>
                    </div>
                </div>