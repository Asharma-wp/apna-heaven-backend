<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        require_once __DIR__ . '/process/login.php';
    } elseif (isset($_POST['register'])) {
        require_once __DIR__ . '/process/register.php';
    }
}

// Display success/error messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login & Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --primary: #4f46e5;
      --primary-light: #e0e7ff;
      --primary-dark: #4338ca;
      --secondary: #10b981;
      --accent: #f59e0b;
      --text: #1f2937;
      --text-light: #6b7280;
      --border: #e5e7eb;
      --bg-light: #f9fafb;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.12);
      --radius: 16px;
      --radius-sm: 8px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1.6;
    }

    .container {
      width: 100%;
      max-width: 900px;
      background: white;
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
    }

    .form-wrapper {
      padding: 40px;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
      color: var(--text);
      font-size: 2rem;
      position: relative;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -12px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 4px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
      position: relative;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      font-size: 0.875rem;
      color: var(--text);
      position: relative;
    }

    label::after {
      content: '*';
      color: #ef4444;
      margin-left: 4px;
      font-weight: 600;
    }

    input, select {
      width: 100%;
      padding: 14px 50px 14px 16px;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      font-family: inherit;
      font-size: 0.9375rem;
      transition: var(--transition);
      background: var(--bg-light);
      -webkit-appearance: none;
    }

    input:focus, select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px var(--primary-light);
      background: white;
      transform: translateY(-2px);
    }

    input:hover, select:hover {
      border-color: var(--primary);
      background: white;
    }

    .input-icon {
      position: absolute;
      right: 16px;
      top: 42px;
      color: var(--text-light);
      pointer-events: none;
      font-size: 1.1rem;
      transition: var(--transition);
    }

    input:focus + .input-icon,
    select:focus + .input-icon {
      color: var(--primary);
      transform: scale(1.1);
    }

    button {
      padding: 16px 24px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      border: none;
      color: white;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-weight: 600;
      transition: var(--transition);
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 1rem;
      margin-top: 20px;
      position: relative;
      overflow: hidden;
    }

    button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: var(--transition);
    }

    button:hover::before {
      left: 100%;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
    }

    button:active {
      transform: translateY(-1px);
    }

    button:disabled {
      background: #d1d5db;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .link-toggle {
      text-align: center;
      margin-top: 25px;
      font-size: 0.9375rem;
      color: var(--text-light);
    }

    .link-toggle a {
      color: var(--primary);
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
      position: relative;
    }

    .link-toggle a:hover {
      color: var(--primary-dark);
    }

    .link-toggle a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--primary);
      transition: width 0.3s ease;
    }

    .link-toggle a:hover::after {
      width: 100%;
    }

    .property-options {
      display: none;
      margin-top: 15px;
      animation: fadeIn 0.4s ease;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      padding: 20px;
      border-radius: var(--radius-sm);
      border: 2px solid var(--border);
    }

    .property-options.active {
      display: block;
    }

    .checkbox-group {
      margin-top: 10px;
    }

    .checkbox-group > label {
      font-weight: 600;
      margin-bottom: 15px;
      color: var(--text);
      font-size: 0.9375rem;
    }

    .checkbox-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 12px;
    }

    .checkbox-option {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      border-radius: var(--radius-sm);
      transition: var(--transition);
      cursor: pointer;
      border: 2px solid transparent;
      background: white;
      position: relative;
      overflow: hidden;
    }

    .checkbox-option::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(79, 70, 229, 0.1), transparent);
      transition: var(--transition);
    }

    .checkbox-option:hover::before {
      left: 100%;
    }

    .checkbox-option:hover {
      border-color: var(--primary);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    }

    .checkbox-option.selected {
      background: linear-gradient(135deg, var(--primary-light) 0%, rgba(79, 70, 229, 0.1) 100%);
      border-color: var(--primary);
      box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
    }

    .checkbox-option input[type="checkbox"] {
      width: 20px;
      height: 20px;
      margin-right: 12px;
      cursor: pointer;
      accent-color: var(--primary);
      flex-shrink: 0;
    }

    .checkbox-option input[type="checkbox"]:focus {
      outline: none;
      box-shadow: 0 0 0 3px var(--primary-light);
    }

    .checkbox-option label {
      display: inline-block;
      margin-bottom: 0;
      cursor: pointer;
      font-weight: 500;
      font-size: 0.875rem;
      color: var(--text);
      flex: 1;
    }

    .success-message {
      display: none;
      text-align: center;
      padding: 40px;
    }

    .success-message i {
      font-size: 4rem;
      color: var(--secondary);
      margin-bottom: 20px;
      animation: bounce 1s ease-in-out;
    }

    .success-message h3 {
      font-size: 1.75rem;
      margin-bottom: 10px;
      color: var(--text);
      font-weight: 600;
    }

    .success-message p {
      color: var(--text-light);
      margin-bottom: 25px;
      font-size: 1.125rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-10px); }
      60% { transform: translateY(-5px); }
    }

    /* Tablet Styles */
    @media (max-width: 768px) {
      body {
        padding: 15px;
      }
      
      .container {
        max-width: 100%;
      }
      
      .form-wrapper {
        padding: 30px 25px;
      }
      
      h2 {
        font-size: 1.75rem;
        margin-bottom: 25px;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .form-group {
        margin-bottom: 18px;
      }
      
      input, select {
        padding: 12px 45px 12px 14px;
        font-size: 0.875rem;
      }
      
      .input-icon {
        top: 40px;
        font-size: 1rem;
      }
      
      button {
        padding: 14px 20px;
        font-size: 0.9375rem;
      }
      
      .checkbox-grid {
        grid-template-columns: 1fr;
      }
      
      .property-options {
        padding: 16px;
      }
    }

    /* Mobile Styles */
    @media (max-width: 480px) {
      body {
        padding: 10px;
      }
      
      .form-wrapper {
        padding: 25px 20px;
      }
      
      h2 {
        font-size: 1.5rem;
        margin-bottom: 20px;
      }
      
      h2::after {
        width: 60px;
        height: 3px;
      }
      
      .form-group {
        margin-bottom: 16px;
      }
      
      input, select {
        padding: 10px 40px 10px 12px;
        font-size: 0.8125rem;
      }
      
      .input-icon {
        top: 38px;
        font-size: 0.875rem;
        right: 14px;
      }
      
      button {
        padding: 12px 18px;
        font-size: 0.875rem;
        margin-top: 18px;
      }
      
      .success-message {
        padding: 25px 20px;
      }
      
      .success-message i {
        font-size: 3rem;
      }
      
      .success-message h3 {
        font-size: 1.5rem;
      }
      
      .success-message p {
        font-size: 1rem;
      }
      
      .checkbox-option {
        padding: 10px 12px;
      }
      
      .property-options {
        padding: 12px;
      }
      
      .link-toggle {
        font-size: 0.875rem;
      }
    }

    /* Extra Small Mobile */
    @media (max-width: 360px) {
      .form-wrapper {
        padding: 20px 15px;
      }
      
      h2 {
        font-size: 1.375rem;
      }
      
      input, select {
        padding: 8px 35px 8px 10px;
        font-size: 0.75rem;
      }
      
      .input-icon {
        top: 36px;
        right: 12px;
      }
      
      button {
        padding: 10px 15px;
        font-size: 0.8125rem;
      }
      
      .checkbox-option {
        padding: 8px 10px;
      }
      
      .property-options {
        padding: 10px;
      }
    }

    /* Desktop Large Styles */
    @media (min-width: 1024px) {
      .container {
        max-width: 1000px;
      }
      
      .form-wrapper {
        padding: 50px 60px;
      }
      
      h2 {
        font-size: 2.25rem;
        margin-bottom: 35px;
      }
      
      .form-grid {
        grid-template-columns: 1fr 1fr;
        gap: 25px;
      }
      
      .form-group {
        margin-bottom: 25px;
      }
      
      .checkbox-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      }
      
      .property-options {
        padding: 25px;
      }
    }
  
    
    /* Add these new styles for messages */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: var(--radius-sm);
      font-size: 0.9375rem;
      text-align: center;
    }
    
    .alert-success {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    
    .alert-error {
      background-color: #fee2e2;
      color: #b91c1c;
      border: 1px solid #fecaca;
    }
    
    .password-strength {
      margin-top: 5px;
      font-size: 0.75rem;
      color: var(--text-light);
    }
    
    .password-strength.weak {
      color: #ef4444;
    }
    
    .password-strength.medium {
      color: #f59e0b;
    }
    
    .password-strength.strong {
      color: #10b981;
    }
  </style>
</head>
<body>

<div class="container" id="loginForm">
    <div class="form-wrapper">
      <h2>Welcome Back</h2>
      
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      
      <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <input type="hidden" name="login" value="1">
        
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Enter your email" required>
          <i class="fas fa-envelope input-icon"></i>
        </div>
        
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
          <i class="fas fa-lock input-icon"></i>
        </div>
        
        <button type="submit">
          <i class="fas fa-sign-in-alt"></i>
          Login
        </button>
      </form>
      
      <div class="link-toggle">
        Don't have an account? <a onclick="showRegister()">Register here</a>
      </div>
    </div>
  </div>

  <div class="container" id="registerForm" style="display:none;">
    <div class="form-wrapper">
      <div id="registrationForm">
        <h2>Create Account</h2>
        
        <?php if ($error): ?>
          <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
          <input type="hidden" name="register" value="1">
          
          <div class="form-grid">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="full_name" placeholder="Enter full name" required>
              <i class="fas fa-user input-icon"></i>
            </div>
            
            <div class="form-group">
              <label>Company Name</label>
              <input type="text" name="company_name" placeholder="Enter company name">
              <i class="fas fa-building input-icon"></i>
            </div>
            
            <div class="form-group">
              <label>Phone Number</label>
              <input type="tel" name="phone_number" placeholder="Enter phone number" required>
              <i class="fas fa-phone input-icon"></i>
            </div>
            
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" placeholder="Enter email" required>
              <i class="fas fa-envelope input-icon"></i>
            </div>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="Create password" required 
                   oninput="checkPasswordStrength(this.value)">
            <i class="fas fa-lock input-icon"></i>
            <div id="passwordStrength" class="password-strength"></div>
          </div>
          
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm password" required>
            <i class="fas fa-lock input-icon"></i>
          </div>

          <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" placeholder="Enter address">
            <i class="fas fa-map-marker-alt input-icon"></i>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Deal In</label>
              <input type="text" name="deal_in" placeholder="Enter Which Sector Do You Deal in">
              <i class="fas fa-briefcase input-icon"></i>
            </div>
            
            <div class="form-group">
              <label>Transaction Type</label>
              <select name="transaction_type">
                <option value="">Select Transaction Type</option>
                <option value="Rent">Rent</option>
                <option value="Sell">Sell</option>
                <option value="Both">Both</option>
              </select>
              <i class="fas fa-exchange-alt input-icon"></i>
            </div>
          </div>

          <div class="form-group">
            <label>Property Category</label>
            <select name="property_category" onchange="showPropertyOptions()">
              <option value="">Select Property Category</option>
              <option value="commercial">Commercial</option>
              <option value="resident">Resident</option>
            </select>
            <i class="fas fa-home input-icon"></i>
          </div>

          <div class="property-options" id="commercialOptions">
            <div class="checkbox-group">
              <label>Select Commercial Options</label>
              <div class="checkbox-grid">
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Office" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Office</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Shops" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Shops</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Showroom" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Showroom</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Plot" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Plot</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Other" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Other</label>
                </div>
              </div>
            </div>
          </div>

          <div class="property-options" id="residentOptions">
            <div class="checkbox-group">
              <label>Select Resident Options</label>
              <div class="checkbox-grid">
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Villa/Bungalow" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Villa/Bungalow</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Flat/Apartment" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Flat/Apartment</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Plots" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Plots</label>
                </div>
                <div class="checkbox-option" onclick="toggleCheckbox(this)">
                  <input type="checkbox" value="Other" name="property_types[]" onclick="handleCheckboxClick(event)"> 
                  <label>Other</label>
                </div>
              </div>
            </div>
          </div>

          <button type="submit">
            <i class="fas fa-user-plus"></i>
            Create Account
          </button>
        </form>

        <div class="link-toggle">
          Already have an account? <a onclick="showLogin()">Login here</a>
        </div>
      </div>

     
<div class="success-message" id="successMessage" style="<?php echo (isset($_SESSION['new_user_id']) || $success) ? 'display:block;' : 'display:none;' ?>">
    <i class="fas fa-check-circle"></i>
    
    
    <?php if (isset($_SESSION['new_user_id'])): ?>
        <h1 style="font-size: 2.5rem; color: #4f46e5; text-align: center; margin-bottom: 20px;">
        Thank You!
    </h1>
    <div style="text-align: center; margin-top: 20px;">
        <p style="font-size: 1.2rem; margin-bottom: 10px;">
            For becoming <strong style="color: #4f46e5;"><?php echo ($_SESSION['new_user_id'] + 400); ?>th</strong> 
            member of Apna Heaven family.
        </p>
        <p style="font-size: 1.1rem;">
            You will be notified as soon as we start the portal.
        </p>
        <?php unset($_SESSION['new_user_id'], $_SESSION['registration_success']); ?>
    <?php endif; ?>
    <button onclick="showLogin()"><i class="fas fa-sign-in-alt"></i> Continue to Login</button>
</div>


    </div>
  </div>

  <script>
      // Add this to your existing script section
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['new_user_id']) || $success): ?>
        showRegister();
        document.getElementById('registrationForm').style.display = 'none';
        document.getElementById('successMessage').style.display = 'block';
    <?php endif; ?>
});
    function showRegister() {
      document.getElementById('loginForm').style.display = 'none';
      document.getElementById('registerForm').style.display = 'block';
      resetForm();
    }

    function showLogin() {
      document.getElementById('loginForm').style.display = 'block';
      document.getElementById('registerForm').style.display = 'none';
      resetForm();
    }

    function showPropertyOptions() {
      document.getElementById('commercialOptions').classList.remove('active');
      document.getElementById('residentOptions').classList.remove('active');

      const value = document.querySelector('select[name="property_category"]').value;
      if (value === 'commercial') {
        document.getElementById('commercialOptions').classList.add('active');
      } else if (value === 'resident') {
        document.getElementById('residentOptions').classList.add('active');
      }
    }

    function toggleCheckbox(element) {
      const checkbox = element.querySelector('input[type="checkbox"]');
      checkbox.checked = !checkbox.checked;
      
      if (checkbox.checked) {
        element.classList.add('selected');
      } else {
        element.classList.remove('selected');
      }
      
      event.stopPropagation();
    }

    function handleCheckboxClick(event) {
      const checkbox = event.target;
      const option = checkbox.closest('.checkbox-option');
      
      if (checkbox.checked) {
        option.classList.add('selected');
      } else {
        option.classList.remove('selected');
      }
      
      event.stopPropagation();
    }

    function resetForm() {
      document.querySelectorAll('#registerForm input, #registerForm select').forEach(el => {
        if (el.type === 'checkbox') {
          el.checked = false;
          const option = el.closest('.checkbox-option');
          if (option) option.classList.remove('selected');
        } else {
          el.value = '';
        }
      });
      document.getElementById('commercialOptions').classList.remove('active');
      document.getElementById('residentOptions').classList.remove('active');
      document.getElementById('registrationForm').style.display = 'block';
      document.getElementById('successMessage').style.display = 'none';
      document.getElementById('passwordStrength').textContent = '';
    }

    function checkPasswordStrength(password) {
      const strengthIndicator = document.getElementById('passwordStrength');
      const strength = {
        0: "Very Weak",
        1: "Weak",
        2: "Medium",
        3: "Strong",
        4: "Very Strong"
      };
      
      let score = 0;
      
      // Check password length
      if (password.length >= 8) score++;
      if (password.length >= 12) score++;
      
      // Check for mixed case
      if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
      
      // Check for numbers
      if (/\d/.test(password)) score++;
      
      // Check for special chars
      if (/[^a-zA-Z0-9]/.test(password)) score++;
      
      // Display result
      strengthIndicator.textContent = `Strength: ${strength[score]}`;
      strengthIndicator.className = 'password-strength';
      
      if (score < 2) {
        strengthIndicator.classList.add('weak');
      } else if (score < 4) {
        strengthIndicator.classList.add('medium');
      } else {
        strengthIndicator.classList.add('strong');
      }
    }
  </script>
</body>
</html>