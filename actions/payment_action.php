<?php
session_start();
require_once "../controllers/payment_controller.php";
require_once "../controllers/order_controller.php";

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'initialize') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }
    
    $order_id = $_GET['order_id'] ?? 0;
    $order = get_order_by_id_ctr($order_id);
    
    if (!$order) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }
    
    if ($order['buyer_id'] != $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    // Get email from session (set during login)
    $email = $_SESSION['user_email'] ?? '';
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email not found in session']);
        exit;
    }
    
    $amount = $order['total_amount'];
    
    // Use Paystack config functions
    require_once '../settings/paystack_config.php';
    
    // Generate unique reference
    $reference = 'ORD-' . $order_id . '-' . time();
    
    // Initialize payment with Paystack
    $result = paystack_initialize_transaction($amount, $email, $reference);
    
    if ($result && isset($result['status']) && $result['status'] === true) {
        // Store order_id in metadata
        echo json_encode([
            'status' => 'success', 
            'authorization_url' => $result['data']['authorization_url'],
            'reference' => $reference
        ]);
    } else {
        $error_msg = $result['message'] ?? 'Payment initialization failed';
        echo json_encode(['status' => 'error', 'message' => $error_msg]);
    }


} elseif ($action === 'verify') {
    $reference = $_GET['reference'] ?? '';
    if (!$reference) {
        die("No reference supplied");
    }
    
    // Use Paystack config functions
    require_once '../settings/paystack_config.php';
    
    // Verify payment with Paystack
    $result = paystack_verify_transaction($reference);
    
    if ($result && isset($result['status']) && $result['status'] === true) {
        // Extract order_id from reference (format: ORD-{order_id}-{timestamp})
        $parts = explode('-', $reference);
        if (count($parts) >= 2 && $parts[0] === 'ORD') {
            $order_id = (int)$parts[1];
            
            // Update order payment status
            update_payment_status_ctr($order_id, 'paid', $reference);
            
            // Redirect to buyer dashboard with success message
            header("Location: ../login/buyer_dashboard.php?payment=success");
        } else {
            header("Location: ../login/buyer_dashboard.php?payment=failed");
        }
    } else {
        header("Location: ../login/buyer_dashboard.php?payment=failed");
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
