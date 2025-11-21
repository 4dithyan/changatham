<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - Package Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Common CSS -->
  <link href="includes/common.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background: #f9f9f9;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(90deg, #004d40, #052205ff);
    }
    .navbar-brand, .navbar-nav .nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .navbar-nav .nav-link:hover {
      color: #ffdd57 !important;
    }

    /* Section 1 */
    .header {
      background: #ffffffff;
      color: #070202ff;
      text-align: center;
      padding: 150px 20px;
      position: relative;
    }
    .header h1 {
      font-size: 60px;
      margin-bottom: 15px;
      font-weight: bold;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
    }
    .header p {
      font-size: 20px;
      color: #bd9c18ff;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
    }

    /* Section 2 */
    .stats {
      background: #fff;
      padding: 60px 20px;
    }
    .stat-box {
      text-align: center;
      padding: 30px;
      border-radius: 15px;
      background: #f1f1f1;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-box:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }
    .stat-box i {
      font-size: 55px;
      margin-bottom: 15px;
      display: block;
    }
    .stat-number {
      font-size: 30px;
      font-weight: bold;
      color: #222;
    }
    .stat-text {
      font-size: 18px;
      color: #555;
    }

    /* Section 3 */
    .footer {
      background: #000;
      color: #fff;
      padding: 60px 20px;
      text-align: center;
      position: relative;
    }
    .footer h4 {
      margin-bottom: 20px;
      font-size: 22px;
      font-weight: bold;
    }
    .footer p {
      margin: 5px 0;
      font-size: 16px;
      color: #ccc;
    }
    .footer a {
      text-decoration: none;
      color: #fff;
    }
    .footer a:hover {
      color: #ffdd57;
    }
    .social-icons {
      margin-top: 15px;
    }
    .social-icons a {
      font-size: 28px;
      margin: 0 10px;
      display: inline-block;
      transition: color 0.3s ease;
    }
    .social-icons a:hover {
      color: #ffdd57;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold mx-auto" href="home.php">Package Booking</a>
      <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="packages.php">Packages</a></li>
          <li class="nav-item"><a class="nav-link" href="home.php#about">About Us</a></li>
          <?php if(isset($_SESSION['EMAIL'])): ?>
            <li class="nav-item"><a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bookings.php') ? 'active' : ''; ?>" href="bookings.php">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="home.php#about">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout (<?php echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'User'; ?>)</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" style="background: linear-gradient(rgba(0, 77, 64, 0.8), rgba(0, 77, 64, 0.8)), #ffffffff; color: #070202ff; text-align: center; padding: 150px 20px;">
    <div class="container">
      <h1 class="display-3 fw-bold" style="text-shadow: 2px 2px 8px rgba(0,0,0,0.7);">Contact Us</h1>
      <p class="lead" style="color: #bd9c18ff; text-shadow: 1px 1px 5px rgba(0,0,0,0.7);">We'd love to hear from you 🌴</p>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="contact-form py-5" style="background-color: white;">
    <div class="container">
      <h2 class="section-title">Get In Touch</h2>
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="card card-modern">
            <div class="card-body p-4">
              <form>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Your Name">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Your Email">
                  </div>
                </div>
                <div class="mb-3">
                  <label for="subject" class="form-label">Subject</label>
                  <input type="text" class="form-control" id="subject" placeholder="Subject">
                </div>
                <div class="mb-3">
                  <label for="message" class="form-label">Message</label>
                  <textarea class="form-control" id="message" rows="5" placeholder="Your Message"></textarea>
                </div>
                <button type="submit" class="btn btn-modern w-100">Send Message</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Info Section -->
  <section class="contact-info py-5" style="background: linear-gradient(135deg, #e0f7fa, #bbdefb);">
    <div class="container">
      <h2 class="section-title">Contact Information</h2>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card card-modern text-center h-100">
            <div class="card-body">
              <div class="feature-icon mx-auto mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="bi bi-geo-alt text-white" style="font-size: 2rem;"></i>
              </div>
              <h4>Our Location</h4>
              <p class="text-muted">Aluva, Kerala, India</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card card-modern text-center h-100">
            <div class="card-body">
              <div class="feature-icon mx-auto mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="bi bi-telephone text-white" style="font-size: 2rem;"></i>
              </div>
              <h4>Phone Number</h4>
              <p class="text-muted">+91 98765 43210</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card card-modern text-center h-100">
            <div class="card-body">
              <div class="feature-icon mx-auto mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                <i class="bi bi-envelope text-white" style="font-size: 2rem;"></i>
              </div>
              <h4>Email Address</h4>
              <p class="text-muted">info@changatham.com</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 2: Stats -->
  <div class="stats container">
    <div class="row g-4">
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-signpost text-success"></i>
          <div class="stat-number">10+</div>
          <div class="stat-text">Trips</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-globe-americas text-primary"></i>
          <div class="stat-number">50+</div>
          <div class="stat-text">Packages</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-people-fill text-danger"></i>
          <div class="stat-number">1000+</div>
          <div class="stat-text">Happy Customers</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-tree-fill text-warning"></i>
          <div class="stat-number">20+</div>
          <div class="stat-text">Outdoor Activities</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Section 3: Footer -->
  <div class="footer">
    <div class="container">
      <h4>Contact Information</h4>
      <p>Phone: +91 471 2321132</p>
      <p>Email: <a href="mailto:packagebooking@gmail.com">packagebooking@gmail.com</a></p>

      <!-- Social Media Icons -->
      <div class="social-icons">
        <a href="https://www.instagram.com/packagebooking" target="_blank"><i class="bi bi-instagram"></i></a>
        <a href="https://www.facebook.com/PackageBooking" target="_blank"><i class="bi bi-facebook"></i></a>
        <a href="https://youtube.com/@packagebooking" target="_blank"><i class="bi bi-youtube"></i></a>
      </div>
    </div>
  </div>

  <!-- WhatsApp Floating Icon -->
  <a href="https://wa.me/919876543210" class="whatsapp-float" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
