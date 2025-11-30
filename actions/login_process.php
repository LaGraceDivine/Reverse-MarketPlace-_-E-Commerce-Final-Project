<?php
session_start();
require_once "../controllers/customer_controller.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate inputs
    if (empty($email) || empty($password)) {
        header("Location: ../login/login.php?error=Please fill in all fields");
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login/login.php?error=Invalid email format");
        exit;
    }

    // Fetch user data by email
    $user = get_customer_by_email_ctr($email);

    // Check if user exists and verify password
    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['user_role'];
        
        // Role-based redirect
        switch((int)$user['user_role']) {
            case 1: // Buyer
                header("Location: ../login/buyer_dashboard.php");
                break;
            case 2: // Seller
                header("Location: ../login/seller_dashboard.php");
                break;
            case 3: // Admin
                header("Location: ../login/dashboard.php");
                break;
            default:
                // Clear session if role is not recognized
                session_destroy();
                header("Location: ../login/login.php?error=Invalid user role. Please contact support.");
        }
        exit;
    } else {
        // Invalid credentials
        header("Location: ../login/login.php?error=Invalid email or password");
        exit;
    }
} else {
    // Not a POST request
    header("Location: ../login/login.php?error=Invalid request method");
    exit;
}
?>