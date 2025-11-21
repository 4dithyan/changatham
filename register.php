<?php
session_start();
include 'config.php'; // Database connection

// If the user is already logged in, redirect them
if (isset($_SESSION['EMAIL'])) {
    // If they're admin, go to admin page, otherwise go to home
    if (isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin') {
        header("Location: admin_page.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

$registration_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm  = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Simplified password rule: min 6 characters
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if user already exists
        $check = $conn->prepare("SELECT * FROM register WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "User already exists! Please log in.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Default role is 'user' for all new registrations
            $role = 'user';

            // Insert into register table
            $stmt = $conn->prepare("INSERT INTO register (name, email, user_ph, password, ROLE) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);

            if ($stmt->execute()) {
                // Automatically log in the user after registration
                $_SESSION['ROLE'] = $role;
                $_SESSION['EMAIL'] = $email;
                $_SESSION['USER_ID'] = $conn->insert_id;
                $_SESSION['USER_NAME'] = $name;  // Store user's name in session
                $_SESSION['SHOW_WELCOME'] = true; // Flag to show welcome message
                
                // Redirect to home page
                header("Location: home.php");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}

$page_title = "Register - Changatham";
include 'includes/header.php';
?>
<style>
        :root {
            --primary: #00695c;
            --secondary: #004d40;
            --accent: #ffeb3b;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        .register-section {
            padding: 60px 0;
            background: linear-gradient(rgba(0, 105, 92, 0.3), rgba(0, 77, 64, 0.3)), url('https://images.unsplash.com/photo-1663597675745-96a3f784369e?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170') !important;
            background-size: cover !important;
            background-position: center center !important;
            background-attachment: fixed !important;
            background-repeat: no-repeat !important;
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 45px;
            width: 100%;
            max-width: 600px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
            padding: 10px 0;
        }
        
        .register-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
            font-size: 2.5rem;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e1e5ee;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.3rem rgba(0, 105, 92, 0.2);
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 15px;
            color: white;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 18px rgba(0, 105, 92, 0.4);
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }
        
        .alert {
            border-radius: 12px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
        
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .login-link a:hover {
            text-decoration: underline;
            color: var(--accent);
        }
        
        .password-requirements {
            font-size: 0.9rem;
            color: #666;
            margin-top: 8px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .register-section {
                padding: 40px 0;
                min-height: calc(100vh - 150px);
            }
            
            .register-container {
                margin: 0 15px;
                padding: 15px 25px;
            }
            
            .register-header h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .register-section {
                padding: 30px 15px;
            }
            
            .register-container {
                margin: 0 10px;
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <main>
        <section class="register-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="register-container">
                            <div class="register-header">
                                <h2><i class="fas fa-user-plus"></i> Register</h2>
                                <p style="margin: 0; font-size: 0.9rem;">Create your account to get started</p>
                            </div>

                            <?php 
                            if (isset($error)) {
                                echo "<div class='alert alert-danger alert-dismissible fade show'>$error <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                            }
                            if ($registration_success) {
                                echo "<div class='alert alert-success alert-dismissible fade show'>Registration successful! You can now <a href='login.php'>login</a>. <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                            }
                            ?>

                            <form action="" method="POST">
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-user"></i> Full Name</label>
                                    <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                    <input type="email" name="email" class="form-control" required placeholder="Enter your email address">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-phone"></i> Phone</label>
                                    <input type="text" name="phone" class="form-control" required maxlength="10" placeholder="Enter 10-digit phone number">
                                    <div class="password-requirements">
                                        Please enter a 10-digit phone number
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                                    <input type="password" name="password" class="form-control" required placeholder="Create a password">
                                    <div class="password-requirements">
                                        Password must be at least 6 characters long
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm your password">
                                </div>
                                <button type="submit" class="btn btn-register w-100">Create Account</button>
                            </form>

                            <div class="login-link">
                                <p>Already have an account? <a href="login.php">Login</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Phone number validation - only allow 10 digits
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.querySelector('input[name="phone"]');
            
            phoneInput.addEventListener('input', function(e) {
                // Remove any non-digit characters
                let value = e.target.value.replace(/\D/g, '');
                
                // Limit to 10 digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                
                // Update the input value
                e.target.value = value;
            });
            
            // Optional: Add validation on form submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const phoneValue = phoneInput.value;
                
                if (phoneValue.length !== 10) {
                    e.preventDefault();
                    alert('Please enter a valid 10-digit phone number.');
                    phoneInput.focus();
                    return false;
                }
            });
        });
    </script>
    
   
</body>
</html>