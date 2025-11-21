<!-- Footer -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-6 mb-4">
        <h4 class="footer-heading">Changatham-By Package Booking</h4>
        <p>Your trusted partner for exploring the beauty of Kerala. We specialize in curated travel experiences that showcase the best of God's Own Country.</p>
        <div class="social-icons">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h4 class="footer-heading">Quick Links</h4>
        <div class="footer-links">
          <a href="home.php">Home</a>
          <a href="packages.php">Packages</a>
          <a href="gallery.php">Gallery</a>
          <a href="reviews.php">Reviews</a>
          <a href="home.php#about">About Us</a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h4 class="footer-heading">Popular Packages</h4>
        <div class="footer-links">
          <?php
          // Include database connection
          $db_connected = false;
          if (file_exists('config.php')) {
              include 'config.php';
              // Test connection
              if ($conn && !$conn->connect_error) {
                  $db_connected = true;
                  // Fetch popular packages from database
                  $package_query = "SELECT package_id, name FROM packages ORDER BY package_id ASC LIMIT 5";
                  $package_result = mysqli_query($conn, $package_query);
                  
                  if ($package_result && mysqli_num_rows($package_result) > 0) {
                      while ($package = mysqli_fetch_assoc($package_result)) {
                          echo '<a href="package_detail.php?id=' . $package['package_id'] . '">' . htmlspecialchars($package['name']) . '</a>';
                      }
                  } else {
                      // Fallback to static links with correct package IDs
                      echo '<a href="package_detail.php?id=1">Wayanad Tour Package</a>';
                      echo '<a href="package_detail.php?id=2">Munnar Hill Station Tour</a>';
                      echo '<a href="package_detail.php?id=3">Alleppey Houseboat Experience</a>';
                  }
              }
          }
          
          // If database connection failed, use static links
          if (!$db_connected) {
              echo '<a href="package_detail.php?id=1">Wayanad Tour Package</a>';
              echo '<a href="package_detail.php?id=2">Munnar Hill Station Tour</a>';
              echo '<a href="package_detail.php?id=3">Alleppey Houseboat Experience</a>';
          }
          ?>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h4 class="footer-heading">Contact Us</h4>
        <div class="footer-links">
          <p><i class="bi bi-geo-alt me-2"></i> Aluva, Kerala, India</p>
          <p><i class="bi bi-telephone me-2"></i> +91 98765 43210</p>
          <p><i class="bi bi-envelope me-2"></i> packagebooking@gmail.com</p>
          <p><i class="bi bi-clock me-2"></i> Mon-Sun: 9:00 AM - 8:00 PM</p>
        </div>
      </div>
    </div>
    <div class="copyright">
      <p>&copy; 2025 Changatham by Package Booking. All Rights Reserved.</p>
    </div>
  </div>
</footer>

<!-- WhatsApp Floating Icon -->
<a href="https://wa.me/919876543210" class="whatsapp-float" target="_blank">
  <i class="bi bi-whatsapp"></i>
</a>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Additional page-specific JavaScript if needed
if (isset($additional_js)) {
    echo $additional_js;
}
?>