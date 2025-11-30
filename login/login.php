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
  <title>Login - Reverse Marketplace</title>
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

    .login-container {
      background: white;
      padding: 50px 40px;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 450px;
      animation: slideUp 0.5s ease-out;
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

    .form-group input {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.3s;
    }

    .form-group input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    }

    .login-btn {
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

    .login-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(168, 85, 247, 0.4);
    }

    .login-btn:active {
      transform: translateY(0);
    }

    .message {
      margin-top: 20px;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      text-align: center;
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

    .register-link {
      text-align: center;
      margin-top: 25px;
      font-size: 14px;
      color: #666;
    }

    .register-link a {
      color: #3b82f6;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .register-link a:hover {
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
      color: #3b82f6;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 40px 30px;
      }

      .logo-container h1 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="logo-container">
      <div class="logo-icon">🛍️</div>
      <h1>Welcome Back</h1>
      <p>Sign in to continue to Reverse Marketplace</p>
    </div>

    <form id="loginForm" action="../actions/login_process.php" method="POST">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="your@email.com" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" class="login-btn">Sign In</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
      <div class="message error">
        <?php echo htmlspecialchars($_GET['error']); ?>
      </div>
    <?php elseif (isset($_GET['success'])): ?>
      <div class="message success">
        <?php echo htmlspecialchars($_GET['success']); ?>
      </div>
    <?php endif; ?>

    <div class="register-link">
      Don't have an account? <a href="register.php">Create Account</a>
    </div>

    <div class="back-home">
      <a href="../index.php">← Back to Home</a>
    </div>
  </div>

</body>
</html>