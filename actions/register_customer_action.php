<?php
header('Content-Type: application/json');
require_once "../controllers/customer_controller.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $user_role = (int)($_POST['user_role'] ?? 2); // Default to seller

    // Validate required fields
    if (empty($full_name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($contact_number) || empty($user_role)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill in all required fields'
        ]);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email format'
        ]);
        exit;
    }

    // Validate password length
    if (strlen($password) < 6) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password must be at least 6 characters long'
        ]);
        exit;
    }

    // Validate user role (must be 1 for buyer or 2 for seller)
    if (!in_array($user_role, [1, 2])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid user role selected'
        ]);
        exit;
    }

    // Prepare data array
    $data = [
        'full_name' => $full_name,
        'email' => $email,
        'password' => $password,
        'country' => $country,
        'city' => $city,
        'contact_number' => $contact_number,
        'user_role' => $user_role
    ];

    // Register the customer
    $result = register_customer_ctr($data);

    if ($result === "success") {
        echo json_encode([
            'status' => 'success',
            'message' => 'Account created successfully! Redirecting to login...'
        ]);
    } elseif ($result === "Email already exists") {
        echo json_encode([
            'status' => 'error',
            'message' => 'This email is already registered. Please login instead.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => $result
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>