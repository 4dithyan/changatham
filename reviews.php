<?php
session_start();
include 'config.php'; // Database connection

$page_title = "Customer Reviews - Changatham";
include 'includes/header.php';

// Fetch all reviews from database with user and package information
$reviews_query = "SELECT r.*, u.name as user_name, p.name as package_name 
                  FROM reviews r 
                  JOIN register u ON r.user_id = u.user_id 
                  JOIN packages p ON r.package_id = p.package_id 
                  ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_query);
?>

<style>
    .reviews-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .reviews-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 20px;
        background: linear-gradient(135deg, #00695c, #004d40);
        border-radius: 15px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .reviews-header h1 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .review-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        padding: 25px;
        margin-bottom: 30px;
        border-left: 4px solid var(--accent);
        transition: all 0.3s ease;
    }
    
    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .reviewer-info {
        display: flex;
        align-items: center;
    }
    
    .reviewer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 15px;
        font-size: 1.2rem;
    }
    
    .reviewer-details h5 {
        margin: 0;
        color: var(--primary);
        font-weight: 600;
    }
    
    .reviewer-details p {
        margin: 0;
        color: #666;
        font-size: 0.9rem;
    }
    
    .review-rating {
        display: flex;
        gap: 3px;
    }
    
    .review-rating i {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .review-content {
        line-height: 1.8;
        color: #444;
    }
    
    .review-date {
        text-align: right;
        color: #888;
        font-size: 0.9rem;
        margin-top: 15px;
    }
    
    .no-reviews {
        text-align: center;
        padding: 50px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .btn-add-review {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .btn-add-review:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        color: white;
    }
    
    .section-title {
        font-family: 'Playfair Display', serif;
        text-align: center;
        margin-bottom: 30px;
        color: var(--primary);
        font-size: 2rem;
    }
    
    @media (max-width: 768px) {
        .reviews-container {
            padding: 15px;
        }
        
        .reviews-header {
            padding: 15px;
        }
        
        .review-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .review-date {
            text-align: left;
            margin-top: 10px;
        }
    }
</style>

<div class="reviews-container">
    <div class="reviews-header">
        <h1><i class="bi bi-star-fill me-2"></i> Customer Reviews</h1>
        <p>See what our travelers have to say about their experiences</p>
    </div>
    
    <div class="text-center mb-4">
        <?php if(isset($_SESSION['EMAIL'])): ?>
            <a href="add_review.php" class="btn-add-review">
                <i class="bi bi-plus-circle me-1"></i> Add Your Review
            </a>
        <?php else: ?>
            <a href="login.php" class="btn-add-review">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login to Add Review
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($reviews_result && mysqli_num_rows($reviews_result) > 0): ?>
        <div class="row">
            <?php while($review = mysqli_fetch_assoc($reviews_result)): ?>
            <div class="col-lg-6">
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                            </div>
                            <div class="reviewer-details">
                                <h5><?php echo htmlspecialchars($review['user_name']); ?></h5>
                                <p><?php echo htmlspecialchars($review['package_name']); ?></p>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $review['rating']): ?>
                                    <i class="bi bi-star-fill"></i>
                                <?php else: ?>
                                    <i class="bi bi-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="review-content">
                        <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                    </div>
                    <div class="review-date">
                        <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-reviews">
            <i class="bi bi-star fs-1 mb-3 d-block"></i>
            <h3>No Reviews Yet</h3>
            <p>Be the first to share your experience with us!</p>
            <?php if(isset($_SESSION['EMAIL'])): ?>
                <a href="add_review.php" class="btn-add-review">
                    <i class="bi bi-plus-circle me-1"></i> Add Your Review
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-add-review">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login to Add Review
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>