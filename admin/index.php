<?php
session_start();
include("../includes/admin-auth.php"); // Checks if admin is logged in
include("../config/database.php");

// Initialize filter variables
$property_type = $_GET['property_type'] ?? '';
$location = $_GET['location'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$min_area = $_GET['min_area'] ?? '';
$max_area = $_GET['max_area'] ?? '';
$bathroom_count = $_GET['bathroom_count'] ?? '';
$property_age = $_GET['property_age'] ?? '';

// Build the SQL query with filters
$sql = "SELECT * FROM properties WHERE 1=1";
$params = [];

// Add filters to the query (using your actual column names)
if (!empty($property_type)) {
    $sql .= " AND property_type = ?";  // Changed from LIKE to exact match
    $params[] = $property_type;
}

if (!empty($location)) {
    $sql .= " AND (property_city LIKE ? OR property_sector LIKE ? OR property_address LIKE ?)";
    $params[] = '%' . $location . '%';
    $params[] = '%' . $location . '%';
    $params[] = '%' . $location . '%';
}

if (!empty($min_price)) {
    $sql .= " AND property_price >= ?";
    $params[] = $min_price;
}

if (!empty($max_price)) {
    $sql .= " AND property_price <= ?";
    $params[] = $max_price;
}

if (!empty($min_area)) {
    $sql .= " AND build_up_area >= ?";
    $params[] = $min_area;
}

if (!empty($max_area)) {
    $sql .= " AND build_up_area <= ?";
    $params[] = $max_area;
}

if (!empty($bathroom_count)) {
    $sql .= " AND bathroom_count >= ?";
    $params[] = $bathroom_count;
}

if (!empty($property_age)) {
    $sql .= " AND property_age <= ?";
    $params[] = $property_age;
}

$sql .= " ORDER BY created_at DESC";

// Execute the query
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties = [];
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
         .dashboard-container {
            margin-top: 60px;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #666;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        
        .results-count {
            margin-bottom: 20px;
            padding: 10px;
            background: #2866a3ff;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
          <div class="logo"><a href="/">Apna Heaven</a></div>
          <div class="nav-toggle" id="navToggle"><i class="fas fa-bars"></i></div>
          <ul class="nav-menu">
            <!-- <li><a href="#" class="nav-link">Cities</a></li>
            <li><a href="#" class="nav-link">Resale Homes</a></li>
            <li><a href="#" class="nav-link">Sell Property</a></li>
            <li><a href="#" class="nav-link">Resale Apartments</a></li>
            <li><a href="#" class="nav-link">Resale Villas</a></li>
            <li><a href="#" class="nav-link">Commercial Resale</a></li> -->
            <li><a href="./add-listing.html" class="nav-link nav-button">List Your Property</a></li>
          </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <!-- Filter Sidebar -->
        <aside class="filter-sidebar">
            <div class="filter-section">
                <h3>Filters</h3>
                
                <form id="filterForm">
                    <div class="filter-group">
                        <label>Property Type</label>
                        <select name="property_type" id="property_type">
                            <option value="">Select Type</option>
                            <option value="1 BHK" <?= $property_type == '1 BHK' ? 'selected' : '' ?>>1 BHK</option>
                            <option value="2 BHK" <?= $property_type == '2 BHK' ? 'selected' : '' ?>>2 BHK</option>
                            <option value="3 BHK" <?= $property_type == '3 BHK' ? 'selected' : '' ?>>3 BHK</option>
                            <option value="4 BHK" <?= $property_type == '4 BHK' ? 'selected' : '' ?>>4 BHK</option>
                            <option value="5 BHK" <?= $property_type == '5 BHK' ? 'selected' : '' ?>>5 BHK</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Location</label>
                        <input type="text" name="location" id="location" 
                               placeholder="Enter city, sector, or address" 
                               value="<?= htmlspecialchars($location) ?>">
                    </div>

                    <div class="filter-group">
                        <label>Price Range (₹)</label>
                        <div class="range-inputs">
                            <input type="number" name="min_price" id="min_price" 
                                   placeholder="Min Price" value="<?= htmlspecialchars($min_price) ?>">
                            <span>to</span>
                            <input type="number" name="max_price" id="max_price" 
                                   placeholder="Max Price" value="<?= htmlspecialchars($max_price) ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Area (sq.ft)</label>
                        <div class="range-inputs">
                            <input type="number" name="min_area" id="min_area" 
                                   placeholder="Min Area" value="<?= htmlspecialchars($min_area) ?>">
                            <span>to</span>
                            <input type="number" name="max_area" id="max_area" 
                                   placeholder="Max Area" value="<?= htmlspecialchars($max_area) ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Bathrooms</label>
                        <select name="bathroom_count" id="bathroom_count">
                            <option value="">Any</option>
                            <option value="1" <?= $bathroom_count == '1' ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= $bathroom_count == '2' ? 'selected' : '' ?>>2</option>
                            <option value="3" <?= $bathroom_count == '3' ? 'selected' : '' ?>>3</option>
                            <option value="4" <?= $bathroom_count == '4' ? 'selected' : '' ?>>4</option>
                            <option value="4" <?= $bathroom_count == '4' ? 'selected' : '' ?>>5</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Property Age (years)</label>
                        <input type="number" name="property_age" id="property_age" 
                               placeholder="Max age in years" 
                               value="<?= htmlspecialchars($property_age) ?>">
                    </div>

                    <button type="submit" class="apply-filters">Apply Filters</button>
                    <button type="button" class="reset-filters" id="resetFilters">Reset</button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <!-- Results Count -->
            <div class="results-count">
                Found <?= count($properties) ?> properties
                <?php if (!empty(array_filter([$property_type, $location, $min_price, $max_price, $min_area, $max_area, $bathroom_count, $property_age]))): ?>
                    <span style="color: #007bff;">(Filtered)</span>
                <?php endif; ?>
            </div>
            
            <!-- Loading indicator -->
            <div class="loading" id="loading">
                <i class="fas fa-spinner fa-spin"></i> Loading properties...
            </div>
            
            <div class="properties-grid" id="propertiesGrid">
                <?php if (empty($properties)): ?>
                    <div class="no-results">
                        <i class="fas fa-search" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                        <h3>No properties found</h3>
                        <p>Try adjusting your filters to see more results.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($properties as $property): ?> 
                    <div class="property-card">
                        
                        <a href="./property-detail.php?id=<?= $property['id'] ?>" class="view-details-btn">
                        <img src="../<?= htmlspecialchars($property['property_images']) ?>" 
                             alt="<?= htmlspecialchars($property['property_title']) ?>" 
                             class="project-image">
                        <div class="property-info">
                            <h3><?php echo htmlspecialchars($property['property_title']); ?></h3>
                            <p class="location">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= htmlspecialchars($property['property_sector']) ?>, 
                                <?= htmlspecialchars($property['property_city']) ?>
                            </p>
                            <p class="price">₹ <?= number_format($property['property_price']) ?></p>
                            <div class="property-features">
                                <span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['property_type']); ?></span>
                                <span><i class="fas fa-vector-square"></i> <?php echo htmlspecialchars($property['build_up_area']); ?> sq.ft</span>
                                <?php if (!empty($property['bathroom_count'])): ?>
                                <span><i class="fas fa-bath"></i> <?php echo htmlspecialchars($property['bathroom_count']); ?> Bath</span>
                                <?php endif; ?>
                                <?php if (!empty($property['property_age'])): ?>
                                <span><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($property['property_age']); ?> yrs old</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        </a>
                        
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Navigation toggle functionality
        document.getElementById("navToggle").addEventListener("click", function () {
            document.querySelector(".nav-menu").classList.toggle("active");
            this.querySelector("i").classList.toggle("fa-bars");
            this.querySelector("i").classList.toggle("fa-times");
        });

        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const resetButton = document.getElementById('resetFilters');
            const loading = document.getElementById('loading');
            const propertiesGrid = document.getElementById('propertiesGrid');

            // Handle form submission
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });

            // Handle reset button
            resetButton.addEventListener('click', function() {
                // Clear all form fields
                filterForm.reset();
                
                // Redirect to page without any query parameters
                window.location.href = window.location.pathname;
            });

            // Function to apply filters
            function applyFilters() {
                // Show loading indicator
                loading.style.display = 'block';
                propertiesGrid.style.display = 'none';

                // Get form data
                const formData = new FormData(filterForm);
                const params = new URLSearchParams();

                // Add non-empty values to params
                for (let [key, value] of formData.entries()) {
                    if (value.trim() !== '') {
                        params.append(key, value);
                    }
                }

                // Update URL and reload page
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.location.href = newUrl;
            }

            // Auto-apply filters on input change (optional - for real-time filtering)
            const filterInputs = filterForm.querySelectorAll('input, select');
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Uncomment the line below if you want real-time filtering
                    // setTimeout(() => applyFilters(), 500);
                });
            });
        });
    </script>
</body>
</html>