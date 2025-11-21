<?php
// Start session at the very beginning
session_start();

$page_title = "About Us - Changatham";
include 'includes/header.php';
?>

<style>
  /* Hero Section */
  .hero {
    background: url('images/hero.jpg') no-repeat center center/cover;
    color: #fff;
    text-align: center;
    padding: 120px 20px;
    position: relative;
  }
  .hero::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background:  #004d40;
  }
  .hero-content {
    position: relative;
    z-index: 1;
  }
  .hero h1 {
    font-size: 2.8rem;
    margin: 0;
  }
  .hero p {
    font-size: 1.2rem;
    margin-top: 10px;
    color: #f1f1f1;
  }

  /* About Section */
  .about {
    max-width: 1100px;
    margin: 60px auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    align-items: center;
  }
  .about img {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
  }
  .about h2 {
    margin-top: 0;
    font-size: 2rem;
    color: #00695c;
  }
  .about p {
    color: #333;
    font-size: 1rem;
  }

  /* Vision Section */
  .vision {
    background: #f9f9f9;
    padding: 60px 20px;
    text-align: center;
  }
  .vision h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #00695c;
  }
  .vision p {
    max-width: 700px;
    margin: auto;
    font-size: 1rem;
    color: #444;
  }
  .vision img {
    margin-top: 25px;
    max-width: 85%;
    border-radius: 15px;
    box-shadow: 0 6px 14px rgba(0,0,0,0.2);
  }

  /* Gallery Section */
  .gallery {
    max-width: 1150px;
    margin: 60px auto;
    padding: 0 20px;
    text-align: center;
  }
  .gallery h2 {
    color: #00695c;
    margin-bottom: 25px;
    font-size: 2rem;
  }
  .gallery-images {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }
  .gallery-images img {
    width: 100%;
    border-radius: 12px;
    height: 220px;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
  }
  .gallery-images img:hover {
    transform: scale(1.05);
  }

  @media (max-width: 768px) {
    .about {
      grid-template-columns: 1fr;
    }
    .hero h1 {
      font-size: 2rem;
    }
  }
</style>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(rgba(0, 77, 64, 0.8), rgba(0, 77, 64, 0.8)), url('images/hero.jpg') no-repeat center center/cover; color: #fff; text-align: center; padding: 120px 20px;">
  <div class="container">
    <h1 class="display-4 fw-bold">About Changatham</h1>
    <p class="lead">Travel Together, Connect Forever</p>
  </div>
</section>

<!-- About Section -->
<section class="about py-5" style="background-color: white;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="section-title" style="text-align: left; margin-bottom: 30px;">Who We Are</h2>
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
  </div>
</section>

<!-- Vision Section -->
<section class="vision py-5" style="background: linear-gradient(135deg, #e0f7fa, #bbdefb);">
  <div class="container text-center">
    <h2 class="section-title">Our Vision</h2>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <p class="lead">
          To make Kerala not just a destination, but an emotion of togetherness, nature, and friendship.
          Every Changatham trip is about breathtaking landscapes and meaningful human connections.
        </p>
      </div>
    </div>
    <img src="images/vision1.jpg" alt="Kerala Nature" class="img-fluid rounded shadow mt-4" style="border-radius: 15px; max-width: 85%;">
  </div>
</section>

<!-- Gallery Section -->
<section class="gallery py-5" style="background-color: white;">
  <div class="container">
    <h2 class="section-title">Explore Beautiful Kerala</h2>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="card card-modern border-0">
          <img src="images/wayanad.jpg" alt="Wayanad Hills" class="card-img-top rounded" style="height: 220px; object-fit: cover;">
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card card-modern border-0">
          <img src="images/vagamo.jpg" alt="Vagamon" class="card-img-top rounded" style="height: 220px; object-fit: cover;">
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card card-modern border-0">
          <img src="images/munnar.jpg" alt="Munnar Tea Estates" class="card-img-top rounded" style="height: 220px; object-fit: cover;">
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="card card-modern border-0">
          <img src="images/backwaters.jpg" alt="Kerala Backwaters" class="card-img-top rounded" style="height: 220px; object-fit: cover;">
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>