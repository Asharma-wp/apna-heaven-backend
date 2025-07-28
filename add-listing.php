<?php
// MUST be at the VERY TOP, before any HTML
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to signup page if not logged in
    header("Location: index.php");
    exit(); // Stop script execution
}

// Display error messages if any
$errors = $_SESSION['form_errors'] ?? [];
$success_message = $_SESSION['success_message'] ?? '';
$form_data = $_SESSION['form_data'] ?? [];

// Clear session messages after displaying
unset($_SESSION['form_errors']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Listing</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <style>
        .selected {
            background-color: #4CAF50 !important;
            color: white !important;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .form-input.error {
            border-color: #dc3545;
        }
    </style>
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
            <li><a href="#" class="nav-link nav-button">List Your Property</a></li>
          </ul>
        </nav>
    </header>
    
    <div class="container">
        <section class="property-form-section">
            <div class="property-form-main" style="border-radius: 20px;">
                <div class="property-form-header">
                    <h3>Add Property Details</h3>
                    <div class="form-help-link">Need help? <a href="#"><i class="fa fa-phone"></i> Get a callback</a></div>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form class="property-form-fields" method="POST" action="process-listing.php" enctype="multipart/form-data">
                    <input type="text" class="form-input" name="property_title" 
                           placeholder="Name of Property*" 
                           value="<?php echo htmlspecialchars($form_data['property_title'] ?? ''); ?>" 
                           required />
                
                    <!-- Property Address Section -->
                    <div class="form-label">Property Address*</div>
                    <input type="text" class="form-input" name="property_address" 
                           placeholder="Full Address (House No, Building, Street, Area)*" 
                           value="<?php echo htmlspecialchars($form_data['property_address'] ?? ''); ?>" 
                           required />
                    
                    <div style="display: flex; gap: 15px; margin-bottom: 16px;">
                      <input type="text" class="form-input" name="property_sector" 
                               placeholder="Sector*" 
                               value="<?php echo htmlspecialchars($form_data['property_sector'] ?? ''); ?>" 
                               style="flex: 1;" required />
                        <input type="text" class="form-input" name="property_city" 
                               placeholder="City*" 
                               value="<?php echo htmlspecialchars($form_data['property_city'] ?? ''); ?>" 
                               style="flex: 1;" required />
                    </div>
                    
                    <!-- Property Type (BHK) Section -->
                    <div class="form-label">Property Type (BHK)*</div>
                    <div class="furnish-btn-group" id="bhkBtnGroup">
                        <button type="button" class="furnish-btn" data-value="1 BHK">1 BHK</button>
                        <button type="button" class="furnish-btn" data-value="2 BHK">2 BHK</button>
                        <button type="button" class="furnish-btn" data-value="3 BHK">3 BHK</button>
                        <button type="button" class="furnish-btn" data-value="4 BHK">4 BHK</button>
                        <button type="button" class="furnish-btn" data-value="5+ BHK">5+ BHK</button>
                    </div>
                    <input type="hidden" name="property_type" id="propertyTypeInput" 
                           value="<?php echo htmlspecialchars($form_data['property_type'] ?? ''); ?>" required>
                    
                    <!-- Bathroom Count Section -->
                    <div class="form-label">Number of Bathrooms*</div>
                    <div class="furnish-btn-group" id="bathroomBtnGroup">
                        <button type="button" class="furnish-btn" data-value="1">1</button>
                        <button type="button" class="furnish-btn" data-value="2">2</button>
                        <button type="button" class="furnish-btn" data-value="3">3</button>
                        <button type="button" class="furnish-btn" data-value="4">4</button>
                        <button type="button" class="furnish-btn" data-value="5+">5+</button>
                    </div>
                    <input type="hidden" name="bathroom_count" id="bathroomCountInput" 
                           value="<?php echo htmlspecialchars($form_data['bathroom_count'] ?? ''); ?>" required>

                    <!-- Existing Form Fields -->
                    
                
                    <input type="text" class="form-input" name="build_up_area" 
                           placeholder="Build Up Area*" 
                           value="<?php echo htmlspecialchars($form_data['build_up_area'] ?? ''); ?>" 
                           required />
                    
                    <input type="number" class="form-input" name="property_age" 
                           placeholder="Age of Property (in years)*" 
                           value="<?php echo htmlspecialchars($form_data['property_age'] ?? ''); ?>" 
                           min="0" required />
                    
                    <input type="number" class="form-input" name="building_floor" 
                           placeholder="Building Floor*" 
                           value="<?php echo htmlspecialchars($form_data['building_floor'] ?? ''); ?>" 
                           min="0" required />
                    
                    <div class="form-label">Property Price*</div>
                    <input type="number" class="form-input" name="property_price" 
                           placeholder="Enter property price in INR" 
                           value="<?php echo htmlspecialchars($form_data['property_price'] ?? ''); ?>" 
                           min="1" required />
                    
                    <div class="form-label">Property Description*</div>
                    <textarea class="form-input" name="property_description" 
                              placeholder="Describe your property..." 
                              rows="4" style="resize: vertical;" required><?php echo htmlspecialchars($form_data['property_description'] ?? ''); ?></textarea>
                    
                    <div class="form-label">Property Facing*</div>
                    <div class="furnish-btn-group" id="facingBtnGroup">
                        <button type="button" class="furnish-btn" data-value="North">North</button>
                        <button type="button" class="furnish-btn" data-value="South">South</button>
                        <button type="button" class="furnish-btn" data-value="East">East</button>
                        <button type="button" class="furnish-btn" data-value="West">West</button>
                        <button type="button" class="furnish-btn" data-value="North East">North East</button>
                        <button type="button" class="furnish-btn" data-value="North West">North West</button>
                        <button type="button" class="furnish-btn" data-value="South East">South East</button>
                        <button type="button" class="furnish-btn" data-value="South West">South West</button>
                    </div>
                    <input type="hidden" name="property_facing" id="propertyFacingInput" 
                           value="<?php echo htmlspecialchars($form_data['property_facing'] ?? ''); ?>" required>

                    <div class="form-label">Property Features</div>
                    <div class="checkbox-group furnish-btn">
                        <?php 
                        $features = ['fully_furnished', 'modular_kitchen', 'balcony', 'car_parking', 'air_conditioned', 'lift_access', 'power_backup', 'vastu_compliant'];
                        $selected_features = $form_data['features'] ?? [];
                        ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="fully_furnished" 
                                   <?php echo in_array('fully_furnished', $selected_features) ? 'checked' : ''; ?>> Fully Furnished
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="modular_kitchen" 
                                   <?php echo in_array('modular_kitchen', $selected_features) ? 'checked' : ''; ?>> Modular Kitchen
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="balcony" 
                                   <?php echo in_array('balcony', $selected_features) ? 'checked' : ''; ?>> Balcony
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="car_parking" 
                                   <?php echo in_array('car_parking', $selected_features) ? 'checked' : ''; ?>> Car Parking
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="air_conditioned" 
                                   <?php echo in_array('air_conditioned', $selected_features) ? 'checked' : ''; ?>> Air Conditioned
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="lift_access" 
                                   <?php echo in_array('lift_access', $selected_features) ? 'checked' : ''; ?>> Lift Access
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="power_backup" 
                                   <?php echo in_array('power_backup', $selected_features) ? 'checked' : ''; ?>> Power Backup
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="features[]" value="vastu_compliant" 
                                   <?php echo in_array('vastu_compliant', $selected_features) ? 'checked' : ''; ?>> Vastu Compliant
                        </label>
                    </div>

                    <div class="form-label">Amenities</div>
                    <div class="checkbox-group furnish-btn">
                        <?php 
                        $selected_amenities = $form_data['amenities'] ?? [];
                        ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="swimming_pool" 
                                   <?php echo in_array('swimming_pool', $selected_amenities) ? 'checked' : ''; ?>> Swimming Pool
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="gymnasium" 
                                   <?php echo in_array('gymnasium', $selected_amenities) ? 'checked' : ''; ?>> Gymnasium
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="club_house" 
                                   <?php echo in_array('club_house', $selected_amenities) ? 'checked' : ''; ?>> Club House
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="garden" 
                                   <?php echo in_array('garden', $selected_amenities) ? 'checked' : ''; ?>> Garden
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="security" 
                                   <?php echo in_array('security', $selected_amenities) ? 'checked' : ''; ?>> 24/7 Security
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="play_area" 
                                   <?php echo in_array('play_area', $selected_amenities) ? 'checked' : ''; ?>> Children's Play Area
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="covered_parking" 
                                   <?php echo in_array('covered_parking', $selected_amenities) ? 'checked' : ''; ?>> Covered Parking
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="power_backup" 
                                   <?php echo in_array('power_backup', $selected_amenities) ? 'checked' : ''; ?>> Power Backup
                        </label>
                    </div>

                    <div class="form-label">Location Highlights</div>
                    <div class="checkbox-group furnish-btn">
                        <?php 
                        $selected_locations = $form_data['location_highlights'] ?? [];
                        ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="location_highlights[]" value="near_metro" 
                                   <?php echo in_array('near_metro', $selected_locations) ? 'checked' : ''; ?>> Near Metro Station
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="location_highlights[]" value="near_market" 
                                   <?php echo in_array('near_market', $selected_locations) ? 'checked' : ''; ?>> Near Market
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="location_highlights[]" value="near_school" 
                                   <?php echo in_array('near_school', $selected_locations) ? 'checked' : ''; ?>> Near School
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="location_highlights[]" value="near_hospital" 
                                   <?php echo in_array('near_hospital', $selected_locations) ? 'checked' : ''; ?>> Near Hospital
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="location_highlights[]" value="near_park" 
                                   <?php echo in_array('near_park', $selected_locations) ? 'checked' : ''; ?>> Near Park
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="location_highlights[]" value="near_bus" 
                                   <?php echo in_array('near_bus', $selected_locations) ? 'checked' : ''; ?>> Near Bus Stop
                        </label>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-label">Upload Property Images*</div>
                    <input type="file" class="form-input" name="property_images[]" 
                           id="propertyImagesInput" accept="image/*" multiple required />
                    <div id="imagePreviewContainer" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;"></div>
                    <!-- End Image Upload -->
                    
                    <button type="submit" class="btn next-btn">Submit Property</button>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Handle BHK selection
        document.getElementById('bhkBtnGroup').addEventListener('click', function(e) {
            if (e.target.classList.contains('furnish-btn')) {
                e.preventDefault();
                
                // Remove selected class from all buttons
                const buttons = this.querySelectorAll('.furnish-btn');
                buttons.forEach(btn => btn.classList.remove('selected'));
                
                // Add selected class to clicked button
                e.target.classList.add('selected');
                
                // Update hidden input value
                const value = e.target.getAttribute('data-value') || e.target.textContent.trim();
                document.getElementById('propertyTypeInput').value = value;
            }
        });

        // Handle bathroom count selection
        document.getElementById('bathroomBtnGroup').addEventListener('click', function(e) {
            if (e.target.classList.contains('furnish-btn')) {
                e.preventDefault();
                
                // Remove selected class from all buttons
                const buttons = this.querySelectorAll('.furnish-btn');
                buttons.forEach(btn => btn.classList.remove('selected'));
                
                // Add selected class to clicked button
                e.target.classList.add('selected');
                
                // Update hidden input value
                const value = e.target.getAttribute('data-value') || e.target.textContent.trim();
                document.getElementById('bathroomCountInput').value = value;
            }
        });

        // Handle facing selection
        document.getElementById('facingBtnGroup').addEventListener('click', function(e) {
            if (e.target.classList.contains('furnish-btn')) {
                e.preventDefault();
                
                // Remove selected class from all buttons
                const buttons = this.querySelectorAll('.furnish-btn');
                buttons.forEach(btn => btn.classList.remove('selected'));
                
                // Add selected class to clicked button
                e.target.classList.add('selected');
                
                // Update hidden input value
                const value = e.target.getAttribute('data-value') || e.target.textContent.trim();
                document.getElementById('propertyFacingInput').value = value;
            }
        });

        // Set initial selected values if form data exists
        document.addEventListener('DOMContentLoaded', function() {
            // For BHK
            const selectedBHK = document.getElementById('propertyTypeInput').value;
            if (selectedBHK) {
                const buttons = document.querySelectorAll('#bhkBtnGroup .furnish-btn');
                buttons.forEach(btn => {
                    if (btn.getAttribute('data-value') === selectedBHK || btn.textContent.trim() === selectedBHK) {
                        btn.classList.add('selected');
                    }
                });
            }
            
            // For Bathroom count
            const selectedBathroom = document.getElementById('bathroomCountInput').value;
            if (selectedBathroom) {
                const buttons = document.querySelectorAll('#bathroomBtnGroup .furnish-btn');
                buttons.forEach(btn => {
                    if (btn.getAttribute('data-value') === selectedBathroom || btn.textContent.trim() === selectedBathroom) {
                        btn.classList.add('selected');
                    }
                });
            }
            
            // For Facing
            const selectedFacing = document.getElementById('propertyFacingInput').value;
            if (selectedFacing) {
                const buttons = document.querySelectorAll('#facingBtnGroup .furnish-btn');
                buttons.forEach(btn => {
                    if (btn.getAttribute('data-value') === selectedFacing || btn.textContent.trim() === selectedFacing) {
                        btn.classList.add('selected');
                    }
                });
            }
        });

        // Image upload preview
        document.getElementById('propertyImagesInput').addEventListener('change', function() {
            const previewContainer = document.getElementById('imagePreviewContainer');
            previewContainer.innerHTML = '';
            
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100px';
                            img.style.maxHeight = '100px';
                            img.style.borderRadius = '8px';
                            img.style.objectFit = 'cover';
                            previewContainer.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        // Form validation before submit
        document.querySelector('.property-form-fields').addEventListener('submit', function(e) {
            const facingValue = document.getElementById('propertyFacingInput').value;
            const bhkValue = document.getElementById('propertyTypeInput').value;
            const bathroomValue = document.getElementById('bathroomCountInput').value;
            
            if (!facingValue) {
                e.preventDefault();
                alert('Please select property facing direction');
                return false;
            }
            
            if (!bhkValue) {
                e.preventDefault();
                alert('Please select property type (BHK)');
                return false;
            }
            
            if (!bathroomValue) {
                e.preventDefault();
                alert('Please select number of bathrooms');
                return false;
            }
        });
    </script>
</body>
</html>