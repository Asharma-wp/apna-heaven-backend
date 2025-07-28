// Update your submitForm function to actually submit the form
function submitForm() {
    // Collect all form data
    const formData = {
        fullName: document.getElementById('fullName').value,
        companyName: document.getElementById('companyName').value,
        phoneNumber: document.getElementById('phoneNumber').value,
        email: document.getElementById('email').value,
        address: document.getElementById('address').value,
        password: document.getElementById('password').value, // You'll need to add this field
        sector: document.getElementById('sector').value,
        transactionType: document.getElementById('transactionType').value,
        propertyCategory: document.getElementById('propertyCategory').value,
        property_types: []
    };

    // Get selected property types
    const commercialCheckboxes = document.querySelectorAll('input[name="commercialProperty"]:checked');
    const residentCheckboxes = document.querySelectorAll('input[name="residentProperty"]:checked');
    
    commercialCheckboxes.forEach(checkbox => formData.property_types.push(checkbox.value));
    residentCheckboxes.forEach(checkbox => formData.property_types.push(checkbox.value));

    // Here you would typically send this data to your server
    // For now, we'll just show the success message
    document.getElementById('registrationForm').style.display = 'none';
    document.getElementById('successMessage').style.display = 'block';
    
    // In a real implementation, you would:
    // 1. Validate the data
    // 2. Send it to your server via fetch or form submission
    // 3. Handle the response
}

// Add password field to your registration form
// <div class="form-group">
//     <label>Password</label>
//     <input type="password" placeholder="Create password" id="password">
//     <i class="fas fa-lock input-icon"></i>
// </div>
// <div class="form-group">
//     <label>Confirm Password</label>
//     <input type="password" placeholder="Confirm password" id="confirmPassword">
//     <i class="fas fa-lock input-icon"></i>
// </div>