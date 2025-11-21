<?php
session_start();
include 'config.php';

// If user is already logged in, redirect to home page
if (isset($_SESSION['EMAIL'])) {
    if (isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin') {
        header("Location: admin_page.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

$error = '';

// Handle login form submission
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Check if user exists
    $query = "SELECT * FROM register WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['USER_ID'] = $user['user_id'];
            $_SESSION['EMAIL'] = $user['email'];
            $_SESSION['USER_NAME'] = $user['name'];
            $_SESSION['ROLE'] = $user['ROLE'];
            
            // Redirect based on role
            if ($user['ROLE'] === 'admin') {
                header("Location: admin_page.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}

$page_title = "Login - Changatham";
include 'includes/header.php';
?>

<style>
    .login-section {
        padding: 60px 0;
        background: linear-gradient(rgba(0, 105, 92, 0.5), rgba(0, 77, 64, 0.5)), url('https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1332') !important;
        background-size: cover !important;
        background-position: center center !important;
        background-attachment: fixed !important;
        background-repeat: no-repeat !important;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
    }
    
    .login-container {
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }
    
    .login-header {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        padding: 10px 30px;
        text-align: center;
    }
    
    .login-header h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 2.2rem;
    }
    
    .login-body {
        padding: 40px;
    }
    
    .form-control {
        padding: 14px 18px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        transition: all 0.3s;
        font-size: 1rem;
    }
    
    .form-control:focus {
        border-color: #00695c;
        box-shadow: 0 0 0 4px rgba(0, 105, 92, 0.15);
    }
    
    .btn-login {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        border: none;
        padding: 14px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s;
        width: 100%;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
    
    .btn-login:hover {
        background: linear-gradient(135deg, #004d40, #00695c);
        transform: translateY(-3px);
        box-shadow: 0 7px 18px rgba(0, 0, 0, 0.2);
    }
    
    .alert {
        border-radius: 10px;
    }
    
    .logo {
        width: 50px;
        height: 50px;
        margin: 0 auto 10px;
    }
    
    .form-label {
        font-weight: 500;
        color: #00695c;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .links {
        text-align: center;
        margin-top: 30px;
    }
    
    .links a {
        color: #00695c;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .links a:hover {
        text-decoration: underline;
        color: #004d40;
    }
    
    @media (max-width: 768px) {
        .login-section {
            padding: 40px 0;
            min-height: calc(100vh - 150px);
        }
        
        .login-body {
            padding: 30px;
        }
        
        .login-header {
            padding: 8px 20px;
        }
        
        .login-header h2 {
            font-size: 1.6rem;
        }
    }
    
    @media (max-width: 576px) {
        .login-section {
            padding: 30px 15px;
        }
        
        .login-container {
            margin: 0;
        }
        
        .login-body {
            padding: 25px;
        }
        
        .login-header {
            padding: 8px 15px;
        }
    }
</style>
</head>
<body>
    <main>
        <section class="login-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="login-container">
                            <div class="login-header">
                                <img src="images/packagebookinglogo.png" alt="Package Booking Logo" class="logo">
                                <h2>Welcome Back</h2>
                                <p style="margin: 0; font-size: 0.9rem;">Please login to your account</p>
                            </div>
                            <div class="login-body">
                                <?php if ($error): ?>
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="post">
                                    <div class="mb-4">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" name="login" class="btn btn-login">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Login to Your Account
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="links">
                                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                                    <p><a href="home.php">Back to Home</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>