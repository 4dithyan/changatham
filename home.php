<?php
session_start(); // Start session to check login status

$page_title = "Changatham By Package Booking";
include 'includes/header.php';
?>

<style>
  /* Hero Slider */
  .hero-slider {
    position: relative;
    height: 70vh; /* Reduced from 80vh */
    overflow: hidden;
  }
  
  .slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
    background-size: cover;
    background-position: center;
    animation: kenburns 20s infinite;
  }
  
  .slide.active {
    opacity: 1;
  }
  
  @keyframes kenburns {
    0% {
      transform: scale(1) translate(0, 0);
    }
    50% {
      transform: scale(1.1) translate(-1%, -1%);
    }
    100% {
      transform: scale(1) translate(0, 0);
    }
  }
  
  .slide-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    z-index: 2;
    width: 80%;
    max-width: 800px;
  }
  
  .slide-content h1 {
    font-family: 'Playfair Display', serif;
    font-size: 4.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    letter-spacing: 1px;
    animation: fadeInUp 1s ease;
  }
  
  .slide-content p {
    font-size: 1.5rem;
    margin-top: 10px;
    margin-bottom: 30px;
    animation: fadeInUp 1s ease 0.3s both;
  }
  
  .hero-btn {
    background-color: var(--accent);
    color: var(--primary);
    border: none;
    padding: 12px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 30px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    animation: fadeInUp 1s ease 0.6s both;
  }
  
  .hero-btn:hover {
    background-color: white;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
  }
  
  .slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 77, 64, 0.3); /* Further reduced transparency from 0.5 to 0.3 */
    z-index: 1;
  }
  
  /* Slider Indicators */
  .slider-indicators {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    z-index: 3;
  }
  
  .indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    margin: 0 5px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  .indicator.active {
    background: var(--accent);
  }
  
  /* Separator Section */
  .separator-section {
    padding: 60px 20px;
    background: linear-gradient(135deg, #e0f7fa, #bbdefb);
    text-align: center;
  }
  
  .separator-section h2 {
    font-family: 'Playfair Display', serif;
    color: var(--primary);
    margin-bottom: 20px;
    font-size: 2.5rem;
  }
  
  .separator-section p {
    font-size: 1.2rem;
    max-width: 800px;
    margin: 0 auto 30px;
    color: #333; /* Improved text visibility */
  }
  
  /* Enhanced button styling for better visibility */
  .btn-accent-large {
    background-color: var(--accent);
    color: var(--primary);
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    border-radius: 50px;
    margin: 10px 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2); /* Enhanced shadow for better visibility */
    border: none;
    text-decoration: none;
    display: inline-block;
    border: 2px solid var(--primary); /* Added border for better definition */
  }
  
  .btn-accent-large:hover {
    background-color: #fff;
    color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3); /* Enhanced shadow on hover */
  }
  
  /* Parallax Section */
  .parallax-section {
    height: 500px;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: white;
  }
  
  .parallax-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 77, 64, 0.8);
  }
  
  .parallax-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 800px;
    padding: 20px;
  }
  
  .parallax-content h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3rem;
    margin-bottom: 20px;
  }
  
  .parallax-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
  }
  
  /* Booking & Login Section */
  .booking-login {
    padding: 80px 20px;
    text-align: center;
    background: linear-gradient(135deg, #e0f7fa, #bbdefb);
  }

  .booking-login h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    margin-bottom: 30px;
    color: var(--primary);
  }

  .btn-modern {
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    border-radius: 50px;
    margin: 10px 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: none;
  }

  .btn-success-modern {
    background: linear-gradient(45deg, #00695c, #004d40);
    color: white;
  }

  .btn-primary-modern {
    background: linear-gradient(45deg, #1976d2, #0d47a1);
    color: white;
  }

  .btn-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  }

  /* Features Section */
  .features {
    padding: 80px 20px;
    background-color: white;
  }
  
  .section-title {
    font-family: 'Playfair Display', serif;
    text-align: center;
    margin-bottom: 50px;
    color: var(--primary);
    font-size: 2.5rem;
  }
  
  .feature-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid #e0e0e0;
  }
  
  .feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
  }
  
  .feature-icon {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 20px;
  }
  
  .feature-card h3 {
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--secondary);
  }
  
  /* Testimonials */
  .testimonials {
    padding: 80px 20px;
    background-color: white;
  }
  
  .testimonial-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    margin: 20px;
    border-left: 4px solid var(--accent);
  }
  
  .testimonial-text {
    font-style: italic;
    margin-bottom: 20px;
  }
  
  .testimonial-author {
    display: flex;
    align-items: center;
  }
  
  .author-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
  }
  
  /* Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* About Section */
  .about-section {
    background: linear-gradient(135deg, #e0f7fa, #bbdefb);
  }
  
  .about-section .section-title {
    color: var(--primary);
    font-family: 'Playfair Display', serif;
  }
  
  .about-section p {
    color: #333;
    font-size: 1rem;
  }
  
  .about-section .lead {
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }
  
  /* Gallery Section */
  .gallery-section {
    padding: 60px 20px;
    background-color: #f8f9fa;
  }
  
  .gallery-container {
    position: relative;
    padding: 0 40px;
  }
  
  .gallery-wrapper {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    padding: 10px 0;
    width: 100%;
    position: relative;
  }
  
  .gallery-wrapper::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera*/
  }
  
  .gallery-card {
    flex: 0 0 auto;
    width: 300px;
    margin-right: 20px;
    transition: all 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    background: white;
  }
  
  .gallery-card:last-child {
    margin-right: 0;
  }
  
  .gallery-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  }
  
  .gallery-card img {
    transition: transform 0.3s ease;
    height: 220px;
    object-fit: cover;
    width: 100%;
  }
  
  .gallery-card:hover img {
    transform: scale(1.05);
  }
  
  /* Navigation arrows */
  .gallery-nav-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  
  .gallery-nav-arrow:hover {
    background: white;
    transform: translateY(-50%) scale(1.1);
  }
  
  .gallery-nav-arrow i {
    font-size: 1.2rem;
    color: var(--primary);
  }
  
  .left-arrow {
    left: 0;
  }
  
  .right-arrow {
    right: 0;
  }
  
  .view-more-btn {
    text-align: center;
    margin-top: 30px;
  }
  
  /* Add a subtle indicator that this is a horizontal scroll */
  .scroll-hint {
    text-align: center;
    margin-top: 10px;
    color: #6c757d;
    font-size: 0.9rem;
    padding: 5px 0;
  }
  
  .scroll-hint i {
    margin: 0 5px;
    animation: bounce 2s infinite;
  }
  
  @keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
      transform: translateX(0);
    }
    40% {
      transform: translateX(-5px);
    }
    60% {
      transform: translateX(5px);
    }
  }
  
  /* Add gradient overlays to indicate more content */
  .gallery-wrapper::before,
  .gallery-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 30px;
    pointer-events: none;
    z-index: 10;
  }
  
  .gallery-wrapper::before {
    left: 0;
    background: linear-gradient(to right, rgba(248, 249, 250, 1), rgba(248, 249, 250, 0));
  }
  
  .gallery-wrapper::after {
    right: 0;
    background: linear-gradient(to left, rgba(248, 249, 250, 1), rgba(248, 249, 250, 0));
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .hero-slider {
      height: 70vh; /* Ensure consistent height on mobile */
    }
    
    .slide-content h1 {
      font-size: 2.5rem;
    }
    
    .slide-content p {
      font-size: 1.1rem;
    }
    
    .navbar {
      top: 0;
      background-color: var(--primary);
    }
    
    .navbar-nav .nav-link {
      margin: 5px 0;
    }
    
    .parallax-section {
      height: 400px;
    }
    
    .gallery-container {
      padding: 0 20px;
    }
    
    .gallery-nav-arrow {
      width: 30px;
      height: 30px;
    }
    
    .gallery-nav-arrow i {
      font-size: 1rem;
    }
  }
</style>

<?php if (isset($_SESSION['SHOW_WELCOME']) && $_SESSION['SHOW_WELCOME']): ?>
  <div class="container-fluid">
    <div class="alert alert-success alert-dismissible fade show text-center mt-3" role="alert">
      <strong>Welcome, <?php echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'User'; ?>!</strong> Your account has been successfully created. We're excited to have you join us!
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  <?php 
  // Clear the welcome flag so it doesn't show again
  unset($_SESSION['SHOW_WELCOME']);
  endif; ?>
  
  <?php if (isset($_SESSION['RETURNING_USER']) && $_SESSION['RETURNING_USER']): ?>
  <div class="container-fluid">
    <div class="alert alert-info text-center mt-3 mb-0 rounded-0" role="alert">
      <div class="container">
        <strong>Welcome back, <?php echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'User'; ?>!</strong> 
        <span class="d-none d-md-inline">We're glad to see you again. Explore our latest travel packages and adventures.</span>
      </div>
    </div>
  </div>
  <?php 
  // Clear the returning user flag so it doesn't show again
  unset($_SESSION['RETURNING_USER']);
  endif; ?>

<!-- Hero Slider Section -->
<section class="hero-slider">
  <!-- Slide 1 -->
  <div class="slide active" style="background-image: url('images/green2.png');">
    <div class="slide-overlay"></div>
    <div class="slide-content">
      <h1>Discover Kerala</h1>
      <p>Experience the beauty of God's Own Country with our curated travel packages</p>
      <a href="packages.php" class="btn hero-btn">Explore Packages</a>
    </div>
  </div>
  
  <!-- Slide 2 -->
  <div class="slide" style="background-image: url('images/green1.png');">
    <div class="slide-overlay"></div>
    <div class="slide-content">
      <h1>Unforgettable Adventures</h1>
      <p>Connect with strangers and create lifelong friendships in Kerala's most beautiful locations</p>
      <a href="packages.php" class="btn hero-btn">Explore Packages</a>
    </div>
  </div>
  
  <!-- Slide 3 -->
  <div class="slide" style="background-image: url('images/frnd1.png');">
    <div class="slide-overlay"></div>
    <div class="slide-content">
      <h1>Changatham Experience</h1>
      <p>Where every journey becomes unforgettable through meaningful connections</p>
      <a href="#about" class="btn hero-btn">Learn More</a>
    </div>
  </div>
  
  <!-- Slide 4 -->
  <div class="slide" style="background-image: url('images/green4.png');">
    <div class="slide-overlay"></div>
    <div class="slide-content">
      <h1>Nature's Beauty Awaits</h1>
      <p>Explore Kerala's pristine landscapes and rich cultural heritage</p>
      <a href="packages.php" class="btn hero-btn">Book Your Trip</a>
    </div>
  </div>
  
  <!-- Slider Indicators -->
  <div class="slider-indicators">
    <div class="indicator active" data-slide="0"></div>
    <div class="indicator" data-slide="1"></div>
    <div class="indicator" data-slide="2"></div>
    <div class="indicator" data-slide="3"></div>
  </div>
</section>

<!-- Separator Section -->
<section class="separator-section">
  <div class="container">
    <h2>Experience the Magic of Kerala</h2>
    <p>From lush tea gardens to pristine backwaters, Kerala offers some of the most breathtaking landscapes in the world. Our carefully crafted packages ensure you experience the best of what God's Own Country has to offer.</p>
    <a href="packages.php" class="btn btn-accent-large">View Our Packages</a>
  </div>
</section>

<!-- Parallax Section -->
<section class="parallax-section" style="background-image: url('images/vision1.jpg');">
  <div class="parallax-overlay"></div>
  <div class="parallax-content">
    <h2>Why Choose Changatham?</h2>
    <p>At Changatham, we believe travel is not just about visiting places, but about creating meaningful connections. Our unique strangers' camp initiative transforms your Kerala journey into an unforgettable experience of friendship and discovery.</p>
  </div>
</section>

<!-- Features Section -->
<section class="features">
  <div class="container">
    <h2 class="section-title">Why Choose Us</h2>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="feature-card card-modern">
          <div class="feature-icon">
            <i class="bi bi-currency-rupee"></i>
          </div>
          <h3>Best Price Guarantee</h3>
          <p>We offer the best prices with no hidden charges and flexible payment options.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card card-modern">
          <div class="feature-icon">
            <i class="bi bi-headset"></i>
          </div>
          <h3>24/7 Support</h3>
          <p>Our dedicated support team is available round the clock to assist you.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card card-modern">
          <div class="feature-icon">
            <i class="bi bi-book"></i>
          </div>
          <h3>Easy Booking</h3>
          <p>Simple and secure booking process with instant confirmation.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section class="about-section py-5" id="about" style="background-color: #f8f9fa;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="section-title" style="text-align: left; margin-bottom: 30px;">About Changatham</h2>
        <p class="lead">
          Changatham is a unique strangers' camp initiative by <b>Package Booking</b>, where every journey through
          Wayanad, Idukki, Munnar, and Vagamon becomes unforgettable.
        </p>
        <p>
          We connect travellers, turning strangers into lifelong friends while exploring Kerala's natural beauty.
        </p>
        <a href="packages.php" class="btn btn-modern mt-3">Explore Our Packages</a>
      </div>
      <div class="col-lg-6">
        <img src="images/vision.jpg" alt="Kerala Travel" class="img-fluid rounded shadow" style="border-radius: 15px;">
      </div>
    </div>
    
    <div class="row mt-5">
      <div class="col-lg-12 text-center">
        <h3 class="section-title">Our Vision</h3>
        <p class="lead">
          To make Kerala not just a destination, but an emotion of togetherness, nature, and friendship.
          Every Changatham trip is about breathtaking landscapes and meaningful human connections.
        </p>
        <img src="images/vision1.jpg" alt="Kerala Nature" class="img-fluid rounded shadow mt-4" style="border-radius: 15px; max-width: 85%;">
      </div>
    </div>
    
    <!-- Gallery Section -->
    <div class="row mt-5">
      <div class="col-lg-12 text-center">
        <h3 class="section-title">Explore Beautiful Kerala</h3>
        <div class="gallery-container">
          <div class="gallery-wrapper mt-4" id="galleryWrapper">
            <?php
            // Fetch gallery images from database - limit to 6 images on home page
            include 'config.php';
            $gallery_query = "SELECT * FROM gallery ORDER BY id ASC LIMIT 6";
            $gallery_result = mysqli_query($conn, $gallery_query);
            
            if (mysqli_num_rows($gallery_result) > 0) {
                while ($image = mysqli_fetch_assoc($gallery_result)) {
                    echo '<div class="card gallery-card">
                            <img src="' . $image['image_path'] . '" alt="' . $image['title'] . '" class="card-img-top">
                            <div class="card-body p-3">
                              <h5 class="card-title mb-0">' . $image['title'] . '</h5>
                            </div>
                          </div>';
                }
            } else {
                // Fallback to static images if database is empty
                echo '<div class="card gallery-card">
                        <img src="images/wayanad.jpg" alt="Wayanad Hills" class="card-img-top">
                        <div class="card-body p-3">
                          <h5 class="card-title mb-0">Wayanad Hills</h5>
                        </div>
                      </div>
                      <div class="card gallery-card">
                        <img src="images/vagamo.jpg" alt="Vagamon" class="card-img-top">
                        <div class="card-body p-3">
                          <h5 class="card-title mb-0">Vagamon</h5>
                        </div>
                      </div>
                      <div class="card gallery-card">
                        <img src="images/munnar.jpg" alt="Munnar Tea Estates" class="card-img-top">
                        <div class="card-body p-3">
                          <h5 class="card-title mb-0">Munnar Tea Estates</h5>
                        </div>
                      </div>
                      <div class="card gallery-card">
                        <img src="images/backwaters.jpg" alt="Kerala Backwaters" class="card-img-top">
                        <div class="card-body p-3">
                          <h5 class="card-title mb-0">Kerala Backwaters</h5>
                        </div>
                      </div>
                      <div class="card gallery-card">
                        <img src="images/beach.jpg" alt="Kerala Beach" class="card-img-top">
                        <div class="card-body p-3">
                          <h5 class="card-title mb-0">Kerala Beach</h5>
                        </div>
                      </div>
                      <div class="card gallery-card">
                        <img src="images/forest.jpg" alt="Athirappally Waterfalls" class="card-img-top">
                        <div class="card-body p-3">
                          <h5 class="card-title mb-0">Athirappally Waterfalls</h5>
                        </div>
                      </div>';
            }
            ?>
          </div>
          
          <!-- Navigation Arrows -->
          <div class="gallery-nav-arrow left-arrow" id="leftArrow">
            <i class="bi bi-chevron-left"></i>
          </div>
          <div class="gallery-nav-arrow right-arrow" id="rightArrow">
            <i class="bi bi-chevron-right"></i>
          </div>
        </div>
        
        <div class="scroll-hint">
          <i class="bi bi-arrow-left"></i> Scroll horizontally to explore <i class="bi bi-arrow-right"></i>
        </div>
        
        <div class="view-more-btn">
          <a href="gallery.php" class="btn btn-modern btn-primary-modern">View More</a>
        </div>
        
        <?php if(isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin'): ?>
          <div class="mt-3">
            <a href="admin_gallery.php" class="btn btn-modern btn-primary-modern">Manage Gallery</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Booking & Login -->
<section class="booking-login">
  <div class="container">
    <h2>Ready for Your Next Adventure?</h2>
    <p>Join thousands of happy travelers who have explored Kerala with us</p>
    
    <?php if(isset($_SESSION['EMAIL'])): ?>
      <?php if(isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin'): ?>
        <!-- For admins, show link to admin dashboard -->
        <a href="admin_page.php" class="btn btn-modern btn-primary-modern">Admin Dashboard</a>
      <?php else: ?>
        <!-- For regular users, show booking option -->
        <a href="packages.php" class="btn-accent-large">Book Now</a>
      <?php endif; ?>
    <?php else: ?>
      <!-- For non-logged-in users, show both options -->
      <a href="packages.php" class="btn-accent-large">Book Now</a>
      <a href="login.php" class="btn btn-modern btn-primary-modern">Login</a>
    <?php endif; ?>
  </div>
</section>

<!-- Testimonials -->
<section class="testimonials">
  <div class="container">
    <h2 class="section-title">What Our Travelers Say</h2>
    <div class="row">
      <?php
      // Fetch reviews from database
      include 'config.php';
      $reviews_query = "SELECT r.*, u.name as user_name, p.name as package_name 
                        FROM reviews r 
                        JOIN register u ON r.user_id = u.user_id 
                        JOIN packages p ON r.package_id = p.package_id 
                        ORDER BY r.created_at DESC 
                        LIMIT 4";
      $reviews_result = $conn->query($reviews_query);
      
      // Check if query was successful and we have results
      if ($reviews_result && $reviews_result->num_rows > 0) {
          while($review = $reviews_result->fetch_assoc()) {
              echo '<div class="col-md-6 mb-4">';
              echo '<div class="testimonial-card card-modern">';
              echo '<div class="card-body">';
              echo '<div class="testimonial-rating mb-2">';
              for($i = 1; $i <= 5; $i++) {
                  if($i <= $review['rating']) {
                      echo '<i class="bi bi-star-fill text-warning"></i>';
                  } else {
                      echo '<i class="bi bi-star text-muted"></i>';
                  }
              }
              echo '</div>';
              echo '<p class="testimonial-text">"' . htmlspecialchars($review['review_text']) . '"</p>';
              echo '<div class="testimonial-author d-flex align-items-center">';
              // Use a default avatar if user image is not available
              echo '<div class="author-img rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #00695c; color: white; font-weight: bold;">' . strtoupper(substr($review['user_name'], 0, 1)) . '</div>';
              echo '<div>';
              echo '<h5>' . htmlspecialchars($review['user_name']) . '</h5>';
              echo '<p class="mb-0">Reviewed: ' . htmlspecialchars($review['package_name']) . '</p>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
          }
      } else {
          // Fallback to static testimonials if no reviews in database or query failed
          echo '<div class="col-md-6 mb-4">';
          echo '<div class="testimonial-card card-modern">';
          echo '<div class="card-body">';
          echo '<p class="testimonial-text">"The Wayanad tour was absolutely amazing! The accommodations were excellent and the guide was very knowledgeable. Highly recommend Changatham for anyone visiting Kerala."</p>';
          echo '<div class="testimonial-author d-flex align-items-center">';
          echo '<img src="images/trip1.jpg" class="author-img rounded-circle me-3" alt="Anita Rao">';
          echo '<div>';
          echo '<h5>Anita Rao</h5>';
          echo '<p class="mb-0">Bangalore, India</p>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '<div class="col-md-6 mb-4">';
          echo '<div class="testimonial-card card-modern">';
          echo '<div class="card-body">';
          echo '<p class="testimonial-text">"Our family trip to Munnar was perfectly organized. Everything from transportation to sightseeing was seamless. Will definitely book with them again!"</p>';
          echo '<div class="testimonial-author d-flex align-items-center">';
          echo '<img src="images/trip3.jpg" class="author-img rounded-circle me-3" alt="Rajesh Kumar">';
          echo '<div>';
          echo '<h5>Rajesh Kumar</h5>';
          echo '<p class="mb-0">Chennai, India</p>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
      }
      ?>
    </div>
    <div class="text-center mt-4">
      <a href="reviews.php" class="btn btn-modern">View All Reviews</a>
    </div>
  </div>
</section>

<!-- Custom JavaScript for Hero Slider and Smooth Scrolling -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Hero slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');
    const totalSlides = slides.length;
    
    function showSlide(index) {
      // Hide all slides
      slides.forEach(slide => slide.classList.remove('active'));
      indicators.forEach(indicator => indicator.classList.remove('active'));
      
      // Show current slide
      slides[index].classList.add('active');
      indicators[index].classList.add('active');
    }
    
    function nextSlide() {
      currentSlide = (currentSlide + 1) % totalSlides;
      showSlide(currentSlide);
    }
    
    // Auto slide change every 5 seconds
    setInterval(nextSlide, 5000);
    
    // Indicator click events
    indicators.forEach((indicator, index) => {
      indicator.addEventListener('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
      });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          window.scrollTo({
            top: targetElement.offsetTop - 80, // Adjust for fixed navbar
            behavior: 'smooth'
          });
        }
      });
    });
    
    // Scroll to section function for buttons
    window.scrollToSection = function(sectionId) {
      const targetElement = document.getElementById(sectionId) || document.querySelector('.' + sectionId + '-section') || document.querySelector('[id="' + sectionId + '"]');
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80,
          behavior: 'smooth'
        });
      } else {
        // Fallback to packages page if section not found
        window.location.href = 'packages.php';
      }
    };
    
    // Gallery horizontal scroll with mouse wheel
    const galleryWrapper = document.getElementById('galleryWrapper');
    if (galleryWrapper) {
      // Handle mouse wheel scrolling
      galleryWrapper.addEventListener('wheel', function(e) {
        if (e.deltaY !== 0) {
          e.preventDefault();
          this.scrollLeft += e.deltaY;
        }
      });
      
      // Navigation arrows
      const leftArrow = document.getElementById('leftArrow');
      const rightArrow = document.getElementById('rightArrow');
      
      if (leftArrow) {
        leftArrow.addEventListener('click', function() {
          galleryWrapper.scrollLeft -= 320; // Scroll by one card width + margin
        });
      }
      
      if (rightArrow) {
        rightArrow.addEventListener('click', function() {
          galleryWrapper.scrollLeft += 320; // Scroll by one card width + margin
        });
      }
      
      // Hide arrows when at the start or end of scroll
      function updateArrowsVisibility() {
        if (leftArrow) {
          leftArrow.style.display = galleryWrapper.scrollLeft > 0 ? 'flex' : 'none';
        }
        if (rightArrow) {
          rightArrow.style.display = galleryWrapper.scrollLeft < (galleryWrapper.scrollWidth - galleryWrapper.clientWidth) ? 'flex' : 'none';
        }
      }
      
      // Initial check
      updateArrowsVisibility();
      
      // Update on scroll
      galleryWrapper.addEventListener('scroll', updateArrowsVisibility);
    }
    
    // Ensure gallery wrapper has proper width for horizontal scrolling
    if (galleryWrapper) {
      // Force reflow to ensure proper width calculation
      setTimeout(() => {
        galleryWrapper.style.overflowX = 'auto';
        galleryWrapper.style.webkitOverflowScrolling = 'touch';
      }, 100);
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
