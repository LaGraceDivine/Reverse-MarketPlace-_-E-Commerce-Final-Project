<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    switch($_SESSION['user_role']){
        case 1:
            header("Location: buyer_dashboard.php");
            break;
        case 2:
            header("Location: seller_dashboard.php");
            break;
        case 3:
            header("Location: dashboard.php");
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Reverse Marketplace</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .register-container {
      background: white;
      padding: 50px 40px;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 550px;
      animation: slideUp 0.5s ease-out;
      max-height: 90vh;
      overflow-y: auto;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }

    .logo-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 36px;
      margin-bottom: 15px;
    }

    .logo-container h1 {
      font-size: 28px;
      color: #1a1a1a;
      margin-bottom: 8px;
    }

    .logo-container p {
      font-size: 15px;
      color: #666;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
      font-size: 14px;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.3s;
      background: white;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(49, 89, 175, 0.1);
    }

    .form-group select {
      cursor: pointer;
    }

    .form-group select:disabled {
      background: #f3f4f6;
      cursor: not-allowed;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .register-btn {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }

    .register-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(46, 132, 211, 0.4);
    }

    .register-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .register-btn:active:not(:disabled) {
      transform: translateY(0);
    }

    .message {
      margin-top: 20px;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      text-align: center;
      display: none;
    }

    .message.show {
      display: block;
    }

    .message.error {
      background: #fee2e2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }

    .message.success {
      background: #d1fae5;
      color: #059669;
      border: 1px solid #a7f3d0;
    }

    .login-link {
      text-align: center;
      margin-top: 25px;
      font-size: 14px;
      color: #666;
    }

    .login-link a {
      color: #3b82f6;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .login-link a:hover {
      color: #3b82f6;
      text-decoration: underline;
    }

    .back-home {
      text-align: center;
      margin-top: 15px;
    }

    .back-home a {
      color: #666;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s;
    }

    .back-home a:hover {
      color: #a855f7;
    }

    @media (max-width: 580px) {
      .register-container {
        padding: 40px 30px;
      }

      .logo-container h1 {
        font-size: 24px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <div class="register-container">
    <div class="logo-container">
      <div class="logo-icon">🛍️</div>
      <h1>Create Account</h1>
      <p>Join Reverse Marketplace today</p>
    </div>

    <form id="registerForm">
      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" placeholder="John Doe" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="your@email.com" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Min. 6 characters" minlength="6" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="country">Country</label>
          <select id="country" name="country" required>
            <option value="">Select Country</option>
          </select>
        </div>

        <div class="form-group">
          <label for="city">City</label>
          <select id="city" name="city" required disabled>
            <option value="">Select City</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="contact_number">Contact Number</label>
        <input type="tel" id="contact_number" name="contact_number" placeholder="+233 XX XXX XXXX" required>
      </div>

      <div class="form-group">
        <label for="user_role">I want to:</label>
        <select id="user_role" name="user_role" required>
          <option value="">Select an option</option>
          <option value="1">Buy products (Buyer)</option>
          <option value="2">Sell products (Seller)</option>
        </select>
      </div>

      <button type="submit" class="register-btn" id="submitBtn">Create Account</button>
    </form>

    <div id="message" class="message"></div>

    <div class="login-link">
      Already have an account? <a href="login.php">Sign In</a>
    </div>

    <div class="back-home">
      <a href="../index.php">← Back to Home</a>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const countrySelect = document.getElementById('country');
      const citySelect = document.getElementById('city');
      const form = document.getElementById('registerForm');
      const msg = document.getElementById('message');
      const submitBtn = document.getElementById('submitBtn');

      /** 
       * Show message function
       */
      function showMessage(text, type) {
        msg.textContent = text;
        msg.className = `message ${type} show`;
        setTimeout(() => {
          msg.classList.remove('show');
        }, 5000);
      }

      /** 
       * Loading all countries from REST Countries API alphabetically
       */
      fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
        .then(res => res.json())
        .then(data => {
          const sortedCountries = data
            .filter(c => c.cca2 && c.name && c.name.common)
            .sort((a, b) => a.name.common.localeCompare(b.name.common));

          // Adding each country to the dropdown
          sortedCountries.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.cca2; 
            opt.textContent = c.name.common;
            countrySelect.appendChild(opt);
          });
        })
        .catch(err => {
          console.error("Error loading countries:", err);
          countrySelect.innerHTML = '<option value="">Error loading countries</option>';
          showMessage('Failed to load countries. Please refresh the page.', 'error');
        });

      /**
       * Loading cities when a country is selected
       */
      countrySelect.addEventListener('change', () => {
        const selectedCountryCode = countrySelect.value;
        citySelect.innerHTML = '<option value="">Loading...</option>';
        citySelect.disabled = true;

        if (!selectedCountryCode) {
          citySelect.innerHTML = '<option value="">Select City</option>';
          citySelect.disabled = true;
          return;
        }

        fetch(`../actions/get_cities_action.php?country=${encodeURIComponent(selectedCountryCode)}`)
          .then(res => {
            if (!res.ok) {
              throw new Error('Network response was not ok');
            }
            return res.json();
          })
          .then(data => {
            citySelect.innerHTML = '';
            if (data.error) {
              citySelect.innerHTML = `<option value="">Error: ${data.error}</option>`;
              citySelect.disabled = true;
              showMessage('Failed to load cities. Please try another country.', 'error');
              return;
            }
            if (Array.isArray(data.cities) && data.cities.length > 0) {
              data.cities.sort((a, b) => a.localeCompare(b));
              const defaultOpt = document.createElement('option');
              defaultOpt.value = '';
              defaultOpt.textContent = 'Select City';
              citySelect.appendChild(defaultOpt);
              data.cities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city;
                opt.textContent = city;
                citySelect.appendChild(opt);
              });
              citySelect.disabled = false;
            } else {
              citySelect.innerHTML = '<option value="">No cities found for this country</option>';
              citySelect.disabled = true;
            }
          })
          .catch(err => {
            console.error("Error loading cities:", err);
            citySelect.innerHTML = '<option value="">Error loading cities</option>';
            citySelect.disabled = true;
            showMessage('Error loading cities. Please try again.', 'error');
          });
      });

      /**
       * Form submit handler with AJAX
       */
      form.addEventListener('submit', e => {
        e.preventDefault();
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating Account...';

        const formData = new FormData(form);

        fetch('../actions/register_customer_action.php', {
          method: 'POST',
          body: formData
        })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              showMessage(data.message || 'Registration successful! Redirecting...', 'success');
              form.reset();
              citySelect.disabled = true;
              setTimeout(() => {
                window.location.href = 'login.php?success=' + encodeURIComponent('Account created successfully! Please login.');
              }, 1500);
            } else {
              showMessage(data.message || 'Registration failed. Please try again.', 'error');
              submitBtn.disabled = false;
              submitBtn.textContent = 'Create Account';
            }
          })
          .catch(err => {
            console.error("Error submitting form:", err);
            showMessage('An error occurred while registering. Please try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Account';
          });
      });
    });
  </script>

</body>
</html>