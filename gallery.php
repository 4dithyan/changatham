<?php
session_start();

$page_title = "Gallery - Changatham";
include 'config.php';
include 'includes/header.php';
?>

<style>
  /* Gallery Page Styles */
  .gallery-page {
    padding: 80px 20px 60px;
    background-color: #f8f9fa;
  }
  
  .section-title {
    font-family: 'Playfair Display', serif;
    text-align: center;
    margin-bottom: 50px;
    color: var(--primary);
    font-size: 2.5rem;
  }
  
  .gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
  }
  
  .gallery-item {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    cursor: pointer;
  }
  
  .gallery-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
  }
  
  .gallery-img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  
  .gallery-item:hover .gallery-img {
    transform: scale(1.05);
  }
  
  .gallery-content {
    padding: 20px;
  }
  
  .gallery-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--primary);
  }
  
  .gallery-description {
    color: #666;
    margin-bottom: 0;
  }
  
  .gallery-date {
    font-size: 0.85rem;
    color: #999;
    margin-top: 10px;
  }
  
  /* Lightbox Styles */
  .lightbox {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
  }
  
  .lightbox-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    max-height: 80%;
    border-radius: 10px;
  }
  
  .lightbox-caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
  }
  
  .lightbox-title {
    font-size: 1.5rem;
    color: white;
    margin-bottom: 10px;
  }
  
  .lightbox-description {
    font-size: 1rem;
    color: #ccc;
  }
  
  .close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
  }
  
  .close:hover,
  .close:focus {
    color: #bbb;
    text-decoration: none;
  }
  
  .prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    padding: 16px;
    margin-top: -50px;
    color: white;
    font-weight: bold;
    font-size: 20px;
    transition: 0.3s;
    border-radius: 0 3px 3px 0;
    user-select: none;
    -webkit-user-select: none;
  }
  
  .next {
    right: 0;
    border-radius: 3px 0 0 3px;
  }
  
  .prev:hover, .next:hover {
    background-color: rgba(0,0,0,0.8);
  }
  
  @media (max-width: 768px) {
    .gallery-grid {
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .section-title {
      font-size: 2rem;
    }
    
    .lightbox-content {
      width: 90%;
    }
    
    .lightbox-caption {
      width: 90%;
    }
  }
</style>

<section class="gallery-page">
  <div class="container">
    <h2 class="section-title">Explore Beautiful Kerala</h2>
    
    <div class="gallery-grid">
      <?php
      // Fetch all gallery images from database
      $gallery_query = "SELECT * FROM gallery ORDER BY id DESC";
      $gallery_result = mysqli_query($conn, $gallery_query);
      
      if (mysqli_num_rows($gallery_result) > 0) {
          $gallery_items = [];
          while ($image = mysqli_fetch_assoc($gallery_result)) {
              $gallery_items[] = $image;
              echo '<div class="gallery-item" onclick="openLightbox(' . $image['id'] . ')">';
              echo '  <img src="' . $image['image_path'] . '" alt="' . $image['title'] . '" class="gallery-img" data-id="' . $image['id'] . '">';
              echo '  <div class="gallery-content">';
              echo '    <h3 class="gallery-title">' . $image['title'] . '</h3>';
              if (!empty($image['description'])) {
                  echo '    <p class="gallery-description">' . substr($image['description'], 0, 100) . (strlen($image['description']) > 100 ? '...' : '') . '</p>';
              }
              echo '    <div class="gallery-date">Added: ' . date('M j, Y', strtotime($image['created_at'])) . '</div>';
              echo '  </div>';
              echo '</div>';
          }
      } else {
          echo '<div class="col-12 text-center">';
          echo '  <p>No gallery images available at the moment.</p>';
          echo '</div>';
      }
      ?>
    </div>
  </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="lightbox">
  <span class="close" onclick="closeLightbox()">&times;</span>
  <img class="lightbox-content" id="lightbox-img">
  <div class="lightbox-caption">
    <h3 class="lightbox-title" id="lightbox-title"></h3>
    <p class="lightbox-description" id="lightbox-description"></p>
  </div>
  <a class="prev" onclick="changeImage(-1)">&#10094;</a>
  <a class="next" onclick="changeImage(1)">&#10095;</a>
</div>

<script>
// Lightbox functionality
let currentIndex = 0;
let galleryItems = [];

// Get gallery items from PHP
<?php if (isset($gallery_items)): ?>
galleryItems = <?php echo json_encode($gallery_items); ?>;
<?php endif; ?>

function openLightbox(id) {
    // Find the index of the clicked item
    for (let i = 0; i < galleryItems.length; i++) {
        if (galleryItems[i].id == id) {
            currentIndex = i;
            break;
        }
    }
    
    document.getElementById('lightbox-img').src = galleryItems[currentIndex].image_path;
    document.getElementById('lightbox-title').innerText = galleryItems[currentIndex].title;
    document.getElementById('lightbox-description').innerText = galleryItems[currentIndex].description || '';
    
    document.getElementById('lightbox').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentIndex += direction;
    
    if (currentIndex >= galleryItems.length) {
        currentIndex = 0;
    } else if (currentIndex < 0) {
        currentIndex = galleryItems.length - 1;
    }
    
    document.getElementById('lightbox-img').src = galleryItems[currentIndex].image_path;
    document.getElementById('lightbox-title').innerText = galleryItems[currentIndex].title;
    document.getElementById('lightbox-description').innerText = galleryItems[currentIndex].description || '';
}

// Close lightbox when clicking outside the image
document.getElementById('lightbox').addEventListener('click', function(event) {
    if (event.target === this) {
        closeLightbox();
    }
});

// Close lightbox with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLightbox();
    }
});
</script>

<?php include 'includes/footer.php'; ?>