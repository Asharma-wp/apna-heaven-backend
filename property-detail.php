<?php
// MUST be at the VERY TOP, before any HTML
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to signup page if not logged in
    header("Location: index.php");
    exit(); // Stop script execution
}

// Include database configuration
require_once __DIR__ . '/config/database.php';

// Get property ID from URL
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($property_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch property details from database
try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND user_id = ?");
    $stmt->execute([$property_id, $_SESSION['user_id']]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$property) {
        $_SESSION['error_message'] = "Property not found or you don't have permission to view it";
        header("Location: dashboard.php");
        exit();
    }
    
    // Process property images
    $property_images = explode(',', $property['property_images']);
    $main_image = !empty($property_images[0]) ? $property_images[0] : 'assets/default-property.jpg';
    
    // Process features, amenities, location highlights if they exist
    $features = !empty($property['property_features']) ? explode(',', $property['property_features']) : [];
    $amenities = !empty($property['amenities']) ? explode(',', $property['amenities']) : [];
    $location_highlights = !empty($property['location_highlights']) ? explode(',', $property['location_highlights']) : [];
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading property details";
    header("Location: dashboard.php");
    exit();
}

// Function to format price with commas
function formatPrice($price) {
    return '₹ ' . number_format((float)$price);
}

// Function to calculate price per sqft
function pricePerSqft($price, $area) {
    if ($area <= 0) return 'N/A';
    $per_sqft = $price / $area;
    return '₹ ' . number_format($per_sqft, 2) . ' per sq ft';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($property['property_title']) ?> - Apna Heaven</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/dashboard.css" />
  </head>
  <body>
    <header class="header">
      <nav class="nav-container">
        <div class="logo"><a href="/">Apna Heaven</a></div>
        <div class="nav-toggle" id="navToggle"><i class="fas fa-bars"></i></div>
        <ul class="nav-menu">
          <li><a href="#" class="nav-link">Cities</a></li>
          <li><a href="#" class="nav-link">Resale Homes</a></li>
          <li><a href="#" class="nav-link">Sell Property</a></li>
          <li><a href="#" class="nav-link">Resale Apartments</a></li>
          <li><a href="#" class="nav-link">Resale Villas</a></li>
          <li><a href="#" class="nav-link">Commercial Resale</a></li>
          <li><a href="./add-listing.php" class="nav-link nav-button">List Your Property</a></li>
        </ul>
      </nav>
    </header>

    <div class="property-details-container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <a href="/">Home</a>
        <span class="separator">/</span>
        <a href="#"><?= htmlspecialchars($property['property_city']) ?></a>
        <span class="separator">/</span>
        <a href="#"><?= htmlspecialchars($property['property_sector']) ?></a>
        <span class="separator">/</span>
        <a href="#"><?= htmlspecialchars($property['property_title']) ?></a>
      </nav>

      <!-- Property Hero Section -->
      <section class="property-hero">
        <div class="property-images">
          <div class="main-image">
            <img src="<?= htmlspecialchars($main_image) ?>" alt="<?= htmlspecialchars($property['property_title']) ?>" id="mainImage">
            <div class="image-overlay">
              <button class="view-all-btn" id="viewAllBtn">
                <i class="fas fa-images"></i> View All Photos (<?= count($property_images) ?>)
              </button>
            </div>
          </div>
          <div class="thumbnail-images">
            <?php foreach ($property_images as $index => $image): ?>
              <img src="<?= htmlspecialchars($image) ?>" alt="Thumbnail <?= $index + 1 ?>" class="thumbnail <?= $index === 0 ? 'active' : '' ?>">
            <?php endforeach; ?>
          </div>
        </div>

        <div class="property-summary">
          <div class="property-header">
            <h1 class="property-title"><?= htmlspecialchars($property['property_title']) ?></h1>
            <p class="property-location">
              <i class="fas fa-map-marker-alt"></i>
              <?= htmlspecialchars($property['property_sector']) ?>, <?= htmlspecialchars($property['property_city']) ?>
            </p>
          </div>

          <div class="property-highlights" style="color: black;">
            <div class="highlight-item">
              <i class="fas fa-bed"></i>
              <span><?= htmlspecialchars($property['bathroom_count']) ?> BHK</span>
            </div>
            <div class="highlight-item">
              <i class="fas fa-bath"></i>
              <span><?= htmlspecialchars($property['bathroom_count']) ?> Bathrooms</span>
            </div>
            <div class="highlight-item">
              <i class="fas fa-expand-arrows-alt"></i>
              <span><?= htmlspecialchars($property['build_up_area']) ?> sq ft</span>
            </div>
            <div class="highlight-item">
              <i class="fas fa-building"></i>
              <span><?= htmlspecialchars($property['building_floor']) ?> Floor</span>
            </div>
          </div>

          <div class="property-price">
            <div class="price-main"><?= formatPrice($property['property_price']) ?></div>
            <div class="price-per-sqft"><?= pricePerSqft($property['property_price'], $property['build_up_area']) ?></div>
          </div>

          <div class="property-actions">
            <button class="action-btn primary">
              <i class="fas fa-phone"></i>
              Contact Seller
            </button>
            <button class="action-btn secondary">
              <i class="fas fa-heart"></i>
              Save Property
            </button>
            <button class="action-btn secondary">
              <i class="fas fa-share-alt"></i>
              Share
            </button>
          </div>

          <div class="verified-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Verified Listing</span>
          </div>
        </div>
      </section>

      <!-- Property Details Content -->
      <div class="property-content">
        <div class="main-content">
          <!-- Property Description -->
          <section class="property-section">
            <h2 class="section-title">Property Description</h2>
            <div class="property-description">
              <p><?= nl2br(htmlspecialchars($property['property_description'])) ?></p>
            </div>
          </section>

          <!-- Property Features -->
          <?php if (!empty($features)): ?>
          <section class="property-section" style="color: black;">
            <h2 class="section-title">Property Features</h2>
            <div class="features-grid">
              <?php foreach ($features as $feature): ?>
                <div class="feature-item">
                  <!-- <i class="fas fa-check-circle"></i> -->
                  <span><?= htmlspecialchars(trim($feature)) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
          <?php endif; ?>

          <!-- Amenities -->
          <?php if (!empty($amenities)): ?>
          <section class="property-section" style="color: black;">
            <h2 class="section-title">Amenities</h2>
            <div class="amenities-grid">
              <?php foreach ($amenities as $amenity): ?>
                <div class="amenity-item">
                  <!-- <i class="fas fa-check-circle"></i> -->
                  <span><?= htmlspecialchars(trim($amenity)) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
          <?php endif; ?>

          <!-- Location -->
          <?php if (!empty($location_highlights)): ?>
          <section class="property-section">
            <h2 class="section-title">Location Highlights</h2>
            <div class="location-info">
              <?php foreach ($location_highlights as $highlight): ?>
                <div class="location-item">
                  <!-- <i class="fas fa-map-marker-alt"></i> -->
                  <div>
                    <h4><?= htmlspecialchars(trim($highlight)) ?></h4>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
          <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Contact Form -->
          <div class="contact-card">
            <h3>Interested in this property?</h3>
            <form class="contact-form" method="POST" action="send-inquiry.php">
              <input type="hidden" name="property_id" value="<?= $property_id ?>">
              <input type="text" name="name" placeholder="Your Name" required>
              <input type="email" name="email" placeholder="Your Email" required>
              <input type="tel" name="phone" placeholder="Your Phone" required>
              <textarea name="message" placeholder="Message (Optional)"></textarea>
              <button type="submit" class="submit-btn">Send Inquiry</button>
            </form>
          </div>

          <!-- Property Details Card -->
          <div class="details-card">
            <h3>Property Details</h3>
            <div class="detail-row">
              <span>Property Type:</span>
              <span><?= htmlspecialchars($property['property_type']) ?></span>
            </div>
            <div class="detail-row">
              <span>BHK:</span>
              <span><?= htmlspecialchars($property['bathroom_count']) ?> BHK</span>
            </div>
            <div class="detail-row">
              <span>Built-up Area:</span>
              <span><?= htmlspecialchars($property['build_up_area']) ?> sq ft</span>
            </div>
            <div class="detail-row">
              <span>Floor:</span>
              <span><?= htmlspecialchars($property['building_floor']) ?></span>
            </div>
            <div class="detail-row">
              <span>Facing:</span>
              <span><?= htmlspecialchars($property['property_facing']) ?></span>
            </div>
            <div class="detail-row">
              <span>Age:</span>
              <span><?= htmlspecialchars($property['property_age']) ?> years</span>
            </div>
            <div class="detail-row">
              <span>Parking:</span>
              <span>1 Covered</span> <!-- You can add parking to your database if needed -->
            </div>
            <div class="detail-row">
              <span>Furnishing:</span>
              <span>Semi-Furnished</span> <!-- You can add furnishing to your database if needed -->
            </div>
          </div>

          <!-- Similar Properties -->
          <?php
          // Fetch similar properties (same city, same property type)
          try {
              $similar_stmt = $pdo->prepare("
                  SELECT id, property_title, property_city, property_sector, property_price, property_images 
                  FROM properties 
                  WHERE property_city = ? AND property_type = ? AND id != ? AND user_id = ?
                  LIMIT 2
              ");
              $similar_stmt->execute([
                  $property['property_city'],
                  $property['property_type'],
                  $property_id,
                  $_SESSION['user_id']
              ]);
              $similar_properties = $similar_stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
              $similar_properties = [];
              error_log("Error fetching similar properties: " . $e->getMessage());
          }
          ?>
          
          <?php if (!empty($similar_properties)): ?>
          <div class="similar-properties">
            <h3>Your Similar Properties</h3>
            <?php foreach ($similar_properties as $similar): 
                $similar_images = explode(',', $similar['property_images']);
                $similar_main_image = !empty($similar_images[0]) ? $similar_images[0] : 'assets/default-property.jpg';
            ?>
            <div class="similar-property">
              <a href="property-detail.php?id=<?= $similar['id'] ?>">
                <img src="<?= htmlspecialchars($similar_main_image) ?>" alt="<?= htmlspecialchars($similar['property_title']) ?>">
                <div class="similar-info">
                  <h4><?= htmlspecialchars($similar['property_title']) ?></h4>
                  <p><?= htmlspecialchars($similar['property_sector']) ?>, <?= htmlspecialchars($similar['property_city']) ?></p>
                  <p class="similar-price"><?= formatPrice($similar['property_price']) ?></p>
                </div>
              </a>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <script>
      // Navigation toggle
      document.getElementById("navToggle").addEventListener("click", function () {
        document.querySelector(".nav-menu").classList.toggle("active");
        this.querySelector("i").classList.toggle("fa-bars");
        this.querySelector("i").classList.toggle("fa-times");
      });

      // Image gallery functionality
      const thumbnails = document.querySelectorAll('.thumbnail');
      const mainImage = document.getElementById('mainImage');

      thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
          // Remove active class from all thumbnails
          thumbnails.forEach(t => t.classList.remove('active'));
          // Add active class to clicked thumbnail
          this.classList.add('active');
          // Update main image
          mainImage.src = this.src;
        });
      });

      // View all photos button
      document.getElementById('viewAllBtn').addEventListener('click', function() {
        alert('This would open a photo gallery in a real implementation');
      });
    </script>
  </body>
</html>