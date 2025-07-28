<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user's properties from database
try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties = [];
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Apna Heaven - Resale Property Hub</title>
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

    <div class="container">
      <div class="headline">Your Resale Property Destination</div>
      <div class="subtext">
        Verified listings, trusted sellers, and best resale deals – 
        <span style="color: #cf8bff">All in One Place</span>
      </div>

      <div class="options">
        <button class="option-btn"><i class="fa fa-home"></i> Flats</button>
        <button class="option-btn"><i class="fa fa-building"></i> Apartments</button>
        <button class="option-btn"><i class="fa fa-villa"></i> Villas</button>
        <button class="option-btn"><i class="fa fa-store"></i> Commercial</button>
        <button class="option-btn"><i class="fa fa-map"></i> Plots</button>
        <button class="option-btn"><i class="fa fa-warehouse"></i> Warehouses</button>
        <button class="option-btn"><i class="fa fa-user"></i> Agents</button>
      </div>

      <div class="search-box">
        <div class="search-row">
          <select class="input">
            <option>Select City</option>
          </select>
          <input class="input" type="text" placeholder="Search by Area, Project, or Seller" />
        </div>
        <div class="search-row">
          <!-- <select class="input"><option>Budget</option></select> -->
          <select class="input"><option>Property Type</option></select>
          <select class="input"><option>BHK</option></select>
          <button class="btn">Search Resale</button>
        </div>
      </div>

      <section class="hot-projects">
        <h2 class="section-title">Featured Resale Properties</h2>
        <p class="section-description">
          Browse our top resale listings across premium locations in India. Find your next home with ready-to-move options and attractive deals.
        </p>

        <div class="projects-slider-wrapper">
          <?php if (!empty($properties)): ?>
    <button class="slider-arrow left" id="sliderLeft"><i class="fa fa-chevron-left"></i></button> <?php endif; ?>
    <div class="projects-slider" id="projectsSlider">
        <?php if (!empty($properties)): ?>
            <?php foreach ($properties as $index => $property): 
                // Get the first image if multiple images are stored
                $images = explode(',', $property['property_images']);
                $first_image = !empty($images[0]) ? $images[0] : 'default-property-image.jpg';
            ?>
                <div class="project-card">
                    <div class="project-number"><?= $index + 1 ?></div>
                    <a href="./property-detail.php?id=<?= $property['id'] ?>">
                        <img src="<?= htmlspecialchars($first_image) ?>" alt="<?= htmlspecialchars($property['property_title']) ?>" class="project-image">
                        <div class="project-details">
                            <h3 class="project-title"><?= htmlspecialchars($property['property_title']) ?></h3>
                            <p class="project-location">
                                <?= htmlspecialchars($property['property_sector']) ?>, 
                                <?= htmlspecialchars($property['property_city']) ?>
                            </p>
                            <p class="project-price">₹ <?= number_format($property['property_price']) ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-properties">
                <p>You haven't listed any properties yet.</p>
                <a href="add-listing.php" class="btn">Add Your First Property</a>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($properties)): ?> <button class="slider-arrow right" id="sliderRight"><i class="fa fa-chevron-right"></i></button> <?php endif; ?>
        
</div>
      </section>
    </div>

    <script>
      document.getElementById("navToggle").addEventListener("click", function () {
        document.querySelector(".nav-menu").classList.toggle("active");
        this.querySelector("i").classList.toggle("fa-bars");
        this.querySelector("i").classList.toggle("fa-times");
      });

      const slider = document.getElementById('projectsSlider');
      const leftBtn = document.getElementById('sliderLeft');
      const rightBtn = document.getElementById('sliderRight');
      const cardWidth = 320;

      leftBtn.addEventListener('click', () => {
        slider.scrollBy({ left: -cardWidth, behavior: 'smooth' });
      });
      rightBtn.addEventListener('click', () => {
        slider.scrollBy({ left: cardWidth, behavior: 'smooth' });
      });
    </script>
  </body>
</html>
