<?php
// feedback.php

// Simple feedback submission handler (no database, just demo)
// In real project, save to database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $location = htmlspecialchars($_POST["location"]);
    $message = htmlspecialchars($_POST["message"]);
    $rating = htmlspecialchars($_POST["rating"]);
    $success = "Thank you, $name! Your feedback has been submitted with a $rating-star rating.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guest Feedback - Package Booking</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: "Segoe UI", sans-serif; background: #f8f9fa; }
    nav { background: #00695c; padding: 15px 40px; }
    nav a { color: #fff; margin: 0 12px; text-decoration: none; }
    nav a:hover { color: #ffc107; }
    nav .logo { color: #ffc107; font-weight: bold; font-size: 22px; }
    .testimonial-card { background: #fff; }
    footer { background: #222; color: #fff; padding: 20px; text-align: center; margin-top: 50px; }

    /* Star Rating Style */
    .star-rating {
      direction: rtl;
      display: flex;
      justify-content: center;
      gap: 5px;
      font-size: 28px;
      cursor: pointer;
    }
    .star-rating input {
      display: none;
    }
    .star-rating label {
      color: #ccc;
      transition: color 0.3s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: #ffc107;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="d-flex justify-content-between align-items-center">
    <div class="logo">Package Booking</div>
    <div>
      <a href="home.php">Home</a>
      <a href="about.php">About</a>
      <a href="explore.php">Explore</a>
      <a href="feedback.php">Feedback</a>
      <a href="contact.php">Contact</a>
    </div>
  </nav>

  <!-- Hero -->
  <header class="text-center py-5 bg-dark text-white">
    <h1>Customer Feedback</h1>
    <p>We value your experiences and suggestions</p>
  </header>

  <!-- Feedback Form -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Share Your Experience</h2>
      <?php if (!empty($success)): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
      <?php endif; ?>
      <form method="POST" class="p-4 shadow rounded bg-white">
        <div class="mb-3">
          <label class="form-label">Your Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Your Feedback</label>
          <textarea name="message" rows="4" class="form-control" required></textarea>
        </div>
        <div class="mb-3 text-center">
          <label class="form-label d-block">Rating</label>
          <div class="star-rating">
            <input type="radio" id="star5" name="rating" value="5" required><label for="star5"><i class="fas fa-star"></i></label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4"><i class="fas fa-star"></i></label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3"><i class="fas fa-star"></i></label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2"><i class="fas fa-star"></i></label>
            <input type="radio" id="star1" name="rating" value="1"><label for="star1"><i class="fas fa-star"></i></label>
          </div>
        </div>
        <button type="submit" class="btn btn-warning w-100">Submit Feedback</button>
      </form>
    </div>
  </section>

  <!-- Guest Stories Carousel -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="section-title text-center mb-5">
        <h2 class="display-5">Customer Stories</h2>
        <p class="lead text-muted">Hear what our valued customers say</p>
      </div>
      
      <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner px-4">
          
          <!-- Testimonial 1 -->
          <div class="carousel-item active">
            <div class="testimonial-card p-4 shadow-sm rounded-4">
              <div class="stars mb-3">
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
              </div>
              <p class="fs-5">"Our stay at Package Booking trip was absolutely perfect. The staff went above and beyond to make our trip special."</p>
              <h5 class="mt-3 mb-0">Amrutha PJ</h5>
              <small class="text-muted">Kerala, India</small>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="carousel-item">
            <div class="testimonial-card p-4 shadow-sm rounded-4">
              <div class="stars mb-3">
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
                <i class="fas fa-star text-warning"></i>
              </div>
              <p class="fs-5">"The attention to detail was remarkable. Our suite had the most breathtaking view. Truly a five-star experience."</p>
              <h5 class="mt-3 mb-0">Devika Binu</h5>
              <small class="text-muted">Kerala, India</small>
            </div>
          </div>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Package Booking. All Rights Reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>