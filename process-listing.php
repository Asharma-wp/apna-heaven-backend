<?php
session_start();

// 1. Check login status
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_error'] = "Please login to list a property";
    header("Location: index.php");
    exit();
}

// 2. Include database configuration
require_once __DIR__ . '/config/database.php';

// 3. Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_errors'] = ["Invalid form submission"];
    header("Location: add-listing.php");
    exit();
}
function getFeatureIcons() {
    return [
        // Your exact form values for features
        'fully_furnished' => '🛋️',
        'modular_kitchen' => '🍽️',
        'balcony' => '🏠',
        'car_parking' => '🚗',
        'air_conditioned' => '❄️',
        'lift_access' => '🛗',
        'power_backup' => '⚡',
        'vastu_compliant' => '🕉️'
    ];
}

function getAmenityIcons() {
    return [
        // Your exact form values for amenities
        'swimming_pool' => '🏊‍♂️',
        'gymnasium' => '💪',
        'club_house' => '🏛️',
        'garden' => '🌿',
        'security' => '🔒',
        'play_area' => '🛝',
        'covered_parking' => '🚗',
        'power_backup' => '⚡'
    ];
}

function getLocationHighlightIcons() {
    return [
        // Your exact form values for location highlights
        'near_metro' => '🚇',
        'near_market' => '🛍️',
        'near_school' => '🏫',
        'near_hospital' => '🏥',
        'near_park' => '🏞️',
        'near_bus' => '🚌'
    ];
}


// 4. Initialize variables
$errors = [];
$uploaded_images = [];

// 5. Validate required fields (updated with new fields)
$required_fields = [
    'property_title' => 'Property Title',
    'property_address' => 'Property Address',
    'property_sector' => 'Property Sector',
    'property_city' => 'Property City',
    'property_type' => 'Property Type',
    'bathroom_count' => 'Bathroom Count',
    'build_up_area' => 'Build Up Area',
    'property_age' => 'Property Age', 
    'building_floor' => 'Building Floor',
    'property_price' => 'Property Price',
    'property_description' => 'Property Description',
    'property_facing' => 'Property Facing'
];

foreach ($required_fields as $field => $name) {
    if (empty($_POST[$field]) || trim($_POST[$field]) === '') {
        $errors[] = "$name is required";
    }
}

// 6. Validate fields
if (!empty($_POST['property_title']) && !is_string($_POST['property_title'])) {
    $errors[] = "Property title must be a valid Name";
}

if (!empty($_POST['property_age']) && !is_numeric($_POST['property_age'])) {
    $errors[] = "Property age must be a valid number";
}

if (!empty($_POST['building_floor']) && !is_numeric($_POST['building_floor'])) {
    $errors[] = "Building floor must be a valid number";
}

if (!empty($_POST['property_price']) && (!is_numeric($_POST['property_price']) || $_POST['property_price'] <= 0)) {
    $errors[] = "Property price must be a valid positive number";
}

// 7. Validate file uploads
if (empty($_FILES['property_images']['name'][0])) {
    $errors[] = "At least one property image is required";
} else {
    // Validate image files
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $max_file_size = 5 * 1024 * 1024; // 5MB

    foreach ($_FILES['property_images']['tmp_name'] as $key => $tmp_name) {
        if (!empty($tmp_name)) {
            $file_name = $_FILES['property_images']['name'][$key];
            $file_size = $_FILES['property_images']['size'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $errors[] = "Invalid file type for image: $file_name. Only JPG, JPEG, PNG, GIF allowed.";
            }

            if ($file_size > $max_file_size) {
                $errors[] = "Image file too large: $file_name. Maximum 5MB allowed.";
            }
        }
    }
}

// 8. Handle errors
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: add-listing.php");
    exit();
}

// 9. Process valid submission
try {
    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . "/uploads/properties/";
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Failed to create upload directory");
        }
    }

    // Process file uploads
    foreach ($_FILES['property_images']['tmp_name'] as $key => $tmp_name) {
        if (!empty($tmp_name)) {
            $file_ext = strtolower(pathinfo($_FILES['property_images']['name'][$key], PATHINFO_EXTENSION));
            $new_name = uniqid('img_', true) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_name;
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                $uploaded_images[] = "uploads/properties/" . $new_name; // Store relative path
            } else {
                throw new Exception("Failed to upload image: " . $_FILES['property_images']['name'][$key]);
            }
        }
    }

    // Validate that at least one image was uploaded successfully
    if (empty($uploaded_images)) {
        throw new Exception("No images were uploaded successfully");
    }

    // Prepare data for database insertion
    // Transform before saving
function transformToDisplayWithIcons($featuresArray, $type = 'features') {
    $iconMaps = [
        'features' => getFeatureIcons(),
        'amenities' => getAmenityIcons(),
        'location_highlights' => getLocationHighlightIcons()
    ];
    
    $icons = $iconMaps[$type] ?? [];
    $result = [];
    
    foreach ($featuresArray as $feature) {
        $displayName = str_replace('_', ' ', ucwords($feature));
        $icon = $icons[$feature] ?? '📍'; // Default icon if not found
        $result[] = $icon . ' ' . $displayName;
    }
    return $result;
}

$features = !empty($_POST['features']) && is_array($_POST['features']) ? 
            implode(", ", transformToDisplayWithIcons($_POST['features'], 'features')) : '';

$amenities = !empty($_POST['amenities']) && is_array($_POST['amenities']) ? 
            implode(", ", transformToDisplayWithIcons($_POST['amenities'], 'amenities')) : '';

$location_highlights = !empty($_POST['location_highlights']) && is_array($_POST['location_highlights']) ? 
            implode(", ", transformToDisplayWithIcons($_POST['location_highlights'], 'location_highlights')) : '';

    $property_images = implode(",", $uploaded_images);

    // Check what columns exist in your table
    try {
        // First, let's try to get the table structure
        $stmt = $pdo->query("DESCRIBE properties");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Build dynamic INSERT query based on existing columns
        $insert_columns = [
            'user_id', 
            'property_title',
            'property_address',
            'property_sector',
            'property_city',
            'property_type',
            'bathroom_count',
            'build_up_area', 
            'property_age', 
            'building_floor', 
            'property_price', 
            'property_description', 
            'property_facing'
        ];
        
        $insert_values = [
            $_SESSION['user_id'],
            trim($_POST['property_title']),
            trim($_POST['property_address']),
            trim($_POST['property_sector']),
            trim($_POST['property_city']),
            trim($_POST['property_type']),
            trim($_POST['bathroom_count']),
            trim($_POST['build_up_area']),
            (int)$_POST['property_age'],
            (int)$_POST['building_floor'],
            (float)$_POST['property_price'],
            trim($_POST['property_description']),
            trim($_POST['property_facing'])
        ];
        
        // Add optional columns if they exist
        if (in_array('property_features', $columns)) {
            $insert_columns[] = 'property_features';
            $insert_values[] = $features;
        }
        
        if (in_array('amenities', $columns)) {
            $insert_columns[] = 'amenities';
            $insert_values[] = $amenities;
        }
        
        if (in_array('location_highlights', $columns)) {
            $insert_columns[] = 'location_highlights';
            $insert_values[] = $location_highlights;
        }
        
        if (in_array('property_images', $columns)) {
            $insert_columns[] = 'property_images';
            $insert_values[] = $property_images;
        }
        
        // Build the SQL query
        $placeholders = str_repeat('?,', count($insert_columns) - 1) . '?';
        $sql = "INSERT INTO properties (" . implode(', ', $insert_columns) . ") VALUES ($placeholders)";
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($insert_values);
        
    } catch (PDOException $e) {
        // If the dynamic approach fails, try basic insertion with new required fields
        $stmt = $pdo->prepare("INSERT INTO properties (
            user_id, 
            property_title,
            property_address,
            property_sector,
            property_city,
            property_type,
            bathroom_count,
            build_up_area, 
            property_age, 
            building_floor, 
            property_price, 
            property_description, 
            property_facing
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $_SESSION['user_id'],
            trim($_POST['property_title']),
            trim($_POST['property_address']),
            trim($_POST['property_sector']),
            trim($_POST['property_city']),
            trim($_POST['property_type']),
            trim($_POST['bathroom_count']),
            trim($_POST['build_up_area']),
            (int)$_POST['property_age'],
            (int)$_POST['building_floor'],
            (float)$_POST['property_price'],
            trim($_POST['property_description']),
            trim($_POST['property_facing'])
        ]);
        
        // If we couldn't save images in the database, save them in a separate table or log them
        if ($success && !empty($uploaded_images)) {
            // Get the last inserted property ID
            $property_id = $pdo->lastInsertId();
            
            // Try to create/use a property_images table
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS property_images (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    property_id INT NOT NULL,
                    image_path VARCHAR(500) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                
                // Insert images into separate table
                foreach ($uploaded_images as $image_path) {
                    $img_stmt = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                    $img_stmt->execute([$property_id, $image_path]);
                }
            } catch (PDOException $img_e) {
                // If we can't create the images table, at least log the images
                error_log("Property images could not be saved to database for property ID: $property_id. Images: " . implode(', ', $uploaded_images));
            }
        }
    }

    if ($success) {
        // Clear any previous form data
        unset($_SESSION['form_data']);
        
        $_SESSION['success_message'] = "Property listed successfully!";
        
        // Create a simple success page if listing-success.php doesn't exist
        if (!file_exists(__DIR__ . '/listing-success.php')) {
            $_SESSION['success_message'] = "Property listed successfully! <a href='add-listing.php'>Add Another Property</a>";
            header("Location: add-listing.php");
        } else {
            header("Location: listing-success.php");
        }
        exit();
    } else {
        throw new Exception("Database insertion failed");
    }

} catch (Exception $e) {
    // Cleanup uploaded images on failure
    foreach ($uploaded_images as $image) {
        $full_path = __DIR__ . "/" . $image;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }
    
    $_SESSION['form_errors'] = ["Error: " . $e->getMessage()];
    $_SESSION['form_data'] = $_POST;
    header("Location: add-listing.php");
    exit();
}