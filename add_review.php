<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['USER_ID'];
$package_id = isset($_GET['package_id']) ? intval($_GET['package_id']) : 0;

// Handle review submission
if (isset($_POST['submit_review'])) {
    $package_id = intval($_POST['package_id']);
    $rating = intval($_POST['rating']);
    $review_text = $conn->real_escape_string($_POST['review_text']);
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $error = "Rating must be between 1 and 5.";
    }
    // Validate review text
    else if (strlen($review_text) < 10) {
        $error = "Review text must be at least 10 characters long.";
    }
    else {
        // Check if user has already reviewed this package
        $check_query = "SELECT * FROM reviews WHERE user_id = '$user_id' AND package_id = '$package_id'";
        $check_result = $conn->query($check_query);
        
        // Check if query was successful before checking num_rows
        if ($check_result) {
            if ($check_result->num_rows > 0) {
                $error = "You have already submitted a review for this package.";
            } else {
                // Insert review without booking ID
                $insert = "INSERT INTO reviews (user_id, package_id, rating, review_text) 
                           VALUES ('$user_id', '$package_id', '$rating', '$review_text')";
                
                if ($conn->query($insert)) {
                    $success = "Review submitted successfully! Thank you for your feedback.";
                } else {
                    $error = "Error submitting review: " . $conn->error;
                }
            }
        } else {
            $error = "Error checking existing reviews: " . $conn->error;
        }
    }
}

$page_title = "Add Review - Changatham";
include 'includes/header.php';
?>

<style>
    .review-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .review-header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #00695c, #004d40);
        border-radius: 15px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .review-header h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        font-weight: 600;
        color: #00695c;
        margin-bottom: 10px;
        display: block;
        font-size: 1.1rem;
    }
    
    .form-control {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1.05rem;
        transition: all 0.3s;
        background: white;
    }
    
    .form-control:focus {
        border-color: #00695c;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 105, 92, 0.15);
    }
    
    .rating-stars {
        display: flex;
        gap: 10px;
        margin: 10px 0;
    }
    
    .star {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .star.selected {
        color: #ffc107;
    }
    
    .star:hover {
        color: #ffc107;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #00695c, #004d40);
        color: white;
        border: none;
        padding: 16px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 10px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        letter-spacing: 0.5px;
    }
    
    .btn-submit:hover {
        background: linear-gradient(135deg, #004d40, #00695c);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
    
    .alert {
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 1.1rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    @media (max-width: 768px) {
        .review-container {
            padding: 15px;
        }
        
        .review-header {
            padding: 15px;
        }
        
        .review-header h2 {
            font-size: 1.8rem;
        }
    }
</style>

<div class="review-container">
    <div class="review-header">
        <h2><i class="bi bi-star-fill me-2"></i> Add Your Review</h2>
        <p>Share your experience with our travel packages</p>
    </div>
    
    <?php if(isset($success)){ echo '<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i> '.$success.'</div>'; } ?>
    <?php if(isset($error)){ echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> '.$error.'</div>'; } ?>
    
    <?php if(!isset($success)): ?>
    <div class="review-form-card">
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="package_id"><i class="bi bi-bag me-2"></i> Select Package <span class="text-danger">*</span></label>
                <select name="package_id" id="package_id" class="form-control" required>
                    <option value="">Select a package</option>
                    <?php
                    // Fetch packages from database
                    $packages_query = "SELECT package_id, name FROM packages ORDER BY name";
                    $packages_result = mysqli_query($conn, $packages_query);
                    while($package = mysqli_fetch_assoc($packages_result)) {
                        $selected = ($package_id == $package['package_id']) ? 'selected' : '';
                        echo '<option value="'.$package['package_id'].'" '.$selected.'>'.htmlspecialchars($package['name']).'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="rating"><i class="bi bi-star me-2"></i> Rating <span class="text-danger">*</span></label>
                <div class="rating-stars">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <input type="hidden" name="rating" id="rating" value="0" required>
                <small class="form-text text-muted">Click on stars to rate (1 = Poor, 5 = Excellent)</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="review_text"><i class="bi bi-chat-square-text me-2"></i> Your Review <span class="text-danger">*</span></label>
                <textarea name="review_text" id="review_text" class="form-control" placeholder="Share your experience with this package..." rows="6" required></textarea>
                <small class="form-text text-muted">Please provide at least 10 characters</small>
            </div>
            
            <button type="submit" name="submit_review" class="btn-submit">
                <i class="bi bi-check-circle me-2"></i> Submit Review
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;
                
                // Update star classes
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });
            
            // Also add hover effect
            star.addEventListener('mouseover', function() {
                const value = this.getAttribute('data-value');
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.style.color = '#ffc107';
                    }
                });
            });
            
            star.addEventListener('mouseout', function() {
                stars.forEach(s => {
                    if (s.classList.contains('selected')) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        // Form validation
        const reviewForm = document.querySelector('form');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                const rating = parseInt(ratingInput.value);
                const reviewText = document.getElementById('review_text').value.trim();
                
                if (rating < 1) {
                    e.preventDefault();
                    alert('Please select a rating.');
                    return false;
                }
                
                if (reviewText.length < 10) {
                    e.preventDefault();
                    alert('Review text must be at least 10 characters long.');
                    return false;
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>