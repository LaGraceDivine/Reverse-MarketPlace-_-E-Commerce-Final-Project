<?php
session_start();

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: login/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reverse Marketplace - You Name It, Sellers Compete For It</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
      background: #f8f9fb;
      color: #333;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      min-height: 100 vh;
      margin: 0;
      padding: 0;
    }

    main {
      width: 100%;
      display: block;
      margin-top: 80px;
      flex: 1;
    }

    /* Header */
    header {
      background: white;
      padding: 20px 50px;
      display: flex;
      flex-direction: row !important;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      width: 100%;
      box-sizing: border-box;
      height: auto;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 22px;
      font-weight: 600;
      color: #333;
      flex-shrink: 0;
    }

    .logo-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb#7c3aed 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
    }

    .get-started-btn {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb#7c3aed 100%);
      color: white;
      padding: 12px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
      width: auto;
      max-width: 300px;
    }

    .get-started-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(168, 85, 247, 0.4);
    }

    /* Hero Section */
    .hero {
      text-align: center;
      padding: 100px 50px 80px;
      background: linear-gradient(180deg, #ffffff 0%, #f8f9fb 100%);
      width: 100%;
      display: block;
    }

    .hero h1 {
      font-size: 56px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 30px;
      line-height: 1.2;
      max-width: 100%;
    }

    .hero p {
      font-size: 20px;
      color: #666;
      max-width: 800px;
      margin: 0 auto 40px;
      line-height: 1.6;
    }

    .start-shopping-btn {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      padding: 18px 40px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 18px;
      display: fixed;
      align-items: center;
      gap: 10px;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
      width: auto;
      max-width: 300px;
    }

    .start-shopping-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 35px rgba(168, 85, 247, 0.4);
    }

    /* Features Section */
    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      padding: 0 50px 50px;
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
    }

    .feature-card {
      background: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.06);
      transition: all 0.3s;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .feature-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin-bottom: 20px;
    }

    .feature-card:nth-child(1) .feature-icon {
      background: #f3e8ff;
      color: #3e4db2ff;
    }

    .feature-card:nth-child(2) .feature-icon {
      background: #dbeafe;
      color: #3b82f6;
    }

    .feature-card:nth-child(3) .feature-icon {
      background: #d1fae5;
      color: #10b981;
    }

    .feature-card h3 {
      font-size: 24px;
      margin-bottom: 15px;
      color: #1a1a1a;
    }

    .feature-card p {
      color: #666;
      line-height: 1.6;
      font-size: 16px;
    }

    /* How It Works Section */
    .how-it-works {
      padding: 80px 50px;
      max-width: 1400px;
      margin: 0 auto;
      background: #f8f9fb;
    }

    .how-it-works h2 {
      text-align: center;
      font-size: 48px;
      margin-bottom: 60px;
      color: #1a1a1a;
    }

    .workflow-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .workflow-column h3 {
      display: flex;
      align-items: center;
      gap: 15px;
      font-size: 28px;
      margin-bottom: 40px;
      color: #1a1a1a;
    }

    .workflow-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
    }

    .workflow-column:nth-child(1) .workflow-icon {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
    }

    .workflow-column:nth-child(2) .workflow-icon {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
    }

    .workflow-step {
      display: fixed;
      gap: 20px;
      margin-bottom: 35px;
      align-items: flex-start;
    }

    .workflow-step:last-child {
      margin-bottom: 0;
    }

    .step-number {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 20px;
      color: white;
      flex-shrink: 0;
    }

    .workflow-column:nth-child(1) .step-number {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .workflow-column:nth-child(2) .step-number {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .step-content h4 {
      font-size: 20px;
      margin-bottom: 8px;
      color: #1a1a1a;
      font-weight: 600;
    }

    .step-content p {
      color: #666;
      line-height: 1.5;
      font-size: 16px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.6);
      backdrop-filter: blur(5px);
      animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .modal-content {
      background-color: white;
      margin: 8% auto;
      padding: 50px;
      border-radius: 20px;
      width: 90%;
      max-width: 450px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      animation: slideUp 0.4s;
      position: relative;
    }

    @keyframes slideUp {
      from {
        transform: translateY(50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .close {
      color: #aaa;
      position: absolute;
      right: 25px;
      top: 20px;
      font-size: 32px;
      font-weight: bold;
      cursor: pointer;
      transition: color 0.3s;
    }

    .close:hover {
      color: #333;
    }

    .modal-content h2 {
      text-align: center;
      margin-bottom: 15px;
      color: #1a1a1a;
      font-size: 32px;
    }

    .modal-content p {
      text-align: center;
      color: #666;
      margin-bottom: 35px;
      font-size: 16px;
    }

    .modal-buttons {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .modal-btn {
      padding: 16px 30px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      text-align: center;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
    }

    .modal-btn-primary {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
    }

    .modal-btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(168, 85, 247, 0.4);
    }

    .modal-btn-secondary {
      background: #f3f4f6;
      color: #333;
    }

    .modal-btn-secondary:hover {
      background: #e5e7eb;
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 36px;
      }

      .workflow-container {
        grid-template-columns: 1fr;
        gap: 50px;
      }

      .features {
        grid-template-columns: 1fr;
      }

      header {
        padding: 20px;
      }

      .how-it-works {
        padding: 50px 20px;
      }

      .workflow-column {
        padding: 30px 25px;
      }

      .hero {
        padding: 60px 20px 50px;
      }

      .features {
        padding: 0 20px 30px;
      }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="logo">
      <div class="logo-icon">🛍️</div>
      <span>Reverse Marketplace</span>
    </div>
    <button class="get-started-btn" onclick="openModal()">Get Started</button>
  </header>

  <!-- Hero Section -->
  <main>
  <section class="hero">
    <h1>You Name It. Sellers Compete For It.</h1>
    <p>Africa's first demand-driven marketplace. Post what you want, set your price, and let sellers compete to give you the best deal.</p>
    <button class="start-shopping-btn" onclick="openModal()">
      Start Now
      <span>→</span>
    </button>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="feature-card">
      <div class="feature-icon">📉</div>
      <h3>Better Prices</h3>
      <p>Sellers compete for your business. You get the best price every time.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">👥</div>
      <h3>Support Local</h3>
      <p>Connect with local sellers who can offer better deals than big retailers.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🛡️</div>
      <h3>Secure & Safe</h3>
      <p>Protected payments with mobile money and cards. Dispute resolution included.</p>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="how-it-works">
    <h2>How It Works</h2>
    <div class="workflow-container">
      <!-- For Buyers -->
      <div class="workflow-column">
        <h3>
          <div class="workflow-icon">🛍️</div>
          For Buyers
        </h3>
        <div class="workflow-step">
          <div class="step-number">1</div>
          <div class="step-content">
            <h4>Post Your Request</h4>
            <p>Describe what you want and set your maximum price</p>
          </div>
        </div>
        <div class="workflow-step">
          <div class="step-number">2</div>
          <div class="step-content">
            <h4>Review Offers</h4>
            <p>Sellers compete with their best prices and terms</p>
          </div>
        </div>
        <div class="workflow-step">
          <div class="step-number">3</div>
          <div class="step-content">
            <h4>Accept & Pay</h4>
            <p>Choose the best offer and complete your purchase</p>
          </div>
        </div>
      </div>

      <!-- For Sellers -->
      <div class="workflow-column">
        <h3>
          <div class="workflow-icon">🏪</div>
          For Sellers
        </h3>
        <div class="workflow-step">
          <div class="step-number">1</div>
          <div class="step-content">
            <h4>Browse Requests</h4>
            <p>See what buyers are looking for in real-time</p>
          </div>
        </div>
        <div class="workflow-step">
          <div class="step-number">2</div>
          <div class="step-content">
            <h4>Make Your Offer</h4>
            <p>Submit your best price and delivery terms</p>
          </div>
        </div>
        <div class="workflow-step">
          <div class="step-number">3</div>
          <div class="step-content">
            <h4>Get Paid & Deliver</h4>
            <p>When accepted, fulfill the order and get paid</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  </main>

  <!-- Modal -->
  <div id="authModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Get Started</h2>
      <p>Join our marketplace to start buying or selling</p>
      <div class="modal-buttons">
        <a href="login/register.php" class="modal-btn modal-btn-primary">Create Account</a>
        <a href="login/login.php" class="modal-btn modal-btn-secondary">Sign In</a>
      </div>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('authModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      document.getElementById('authModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      const modal = document.getElementById('authModal');
      if (event.target == modal) {
        closeModal();
      }
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeModal();
      }
    });
  </script>

</body>
</html>