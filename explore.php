<?php
// explore.php

$activities = [
  [
    "link" => "yoga.php",
    "img"  => "images/yoga.jpg",
    "alt"  => "Yoga & Meditation",
    "name" => "Yoga & Meditation"
  ],
  [
    "link" => "adventure.php",
    "img"  => "images/zip.jpg",
    "alt"  => "Adventure",
    "name" => "Adventure"
  ],
  [
    "link" => "campfire.php",
    "img"  => "images/camp.jpg",
    "alt"  => "Campfire",
    "name" => "Campfire"
  ],
  [
    "link" => "djparty.php",
    "img"  => "images/dj.jpg",
    "alt"  => "DJ Party",
    "name" => "DJ Party"
  ],
  [
    "link" => "sightseeing.php",
    "img"  => "images/sight2.jpg",
    "alt"  => "Sightseeing",
    "name" => "Sightseeing"
  ],
  [
    "link" => "trekking.php",
    "img"  => "images/trekking2.jpg",
    "alt"  => "Trekking",
    "name" => "Trekking"
  ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Explore by Activities</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: #f8f9fa;
      color: #333;
    }

    /* Navigation Bar */
    nav {
      background: #222;
      padding: 12px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    nav .logo {
      color: #fff;
      font-size: 20px;
      font-weight: bold;
      text-transform: uppercase;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 25px;
      margin: 0;
      padding: 0;
    }
    nav ul li {
      display: inline;
    }
    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s;
    }
    nav ul li a:hover {
      color: #28a745;
    }

    /* Section Heading */
    .activities-section {
      text-align: center;
      padding: 50px 20px;
    }
    .activities-section h2 {
      font-size: 36px;
      font-weight: bold;
      color: #222;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    .zigzag-line svg {
      width: 200px;
      height: 20px;
      margin: auto;
      display: block;
    }

    /* Activity Grid */
    .activity-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 30px;
      margin-top: 50px;
      padding: 0 40px;
    }
    .activity-box {
      display: block;
      background: #fff;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
      text-decoration: none;
      color: #333;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .activity-box:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    }
    .activity-box img {
      width: 50%;
      height: 250px;
      object-fit: cover;
    }
    .activity-box p {
      margin: 15px 0;
      font-size: 18px;
      font-weight: 600;
    }

    /* WhatsApp Floating Button */
    .whatsapp-float {
      position: fixed;
      width: 55px;
      height: 55px;
      bottom: 20px;
      right: 20px;
      background-color: #25d366;
      color: #fff;
      border-radius: 50%;
      text-align: center;
      font-size: 28px;
      box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
      z-index: 1000;
      transition: transform 0.3s ease;
    }
    .whatsapp-float:hover {
      transform: scale(1.1);
    }
    .whatsapp-float i {
      margin-top: 13px;
    }

    /* Responsive */
    @media (max-width: 600px) {
      .activity-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav>
    <div class="logo">Explore</div>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
  </nav>

  <!-- Heading -->
  <section class="activities-section">
    <h2>Explore by Activities</h2>
    <div class="zigzag-line">
      <svg viewBox="0 0 100 10" preserveAspectRatio="none">
        <path d="M0 5 Q 5 0, 10 5 T 20 5 T 30 5 T 40 5 T 50 5 T 60 5 T 70 5 T 80 5 T 90 5 T 100 5"
              fill="transparent"
              stroke="#000000"
              stroke-width="2"/>
      </svg>
    </div>

    <!-- Activity Grid -->
    <div class="activity-grid">
      <?php foreach ($activities as $act): ?>
        <a href="<?= $act['link']; ?>" class="activity-box">
          <img src="<?= $act['img']; ?>" alt="<?= $act['alt']; ?>" />
          <p><?= $act['name']; ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- WhatsApp Floating Button -->
  <a href="https://wa.me/919876543210" target="_blank" class="whatsapp-float">
    <i class="fab fa-whatsapp"></i>
  </a>

</body>
</html>