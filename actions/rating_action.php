<?php
session_start();
require_once "../controllers/rating_controller.php";
require_once "../controllers/order_controller.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $rating = $_POST['rating'] ?? 0;
    $review = $_POST['review'] ?? '';
    
    // Verify order completion and participation
    $order = get_order_by_id_ctr($order_id);
    if (!$order) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }
    
    if ($order['delivery_status'] !== 'delivered') {
        echo json_encode(['status' => 'error', 'message' => 'Order not completed']);
        exit;
    }
    
    $rater_id = $_SESSION['user_id'];
    $rated_id = 0;
    
    if ($rater_id == $order['buyer_id']) {
        $rated_id = $order['seller_id'];
    } elseif ($rater_id == $order['seller_id']) {
        $rated_id = $order['buyer_id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    if (add_rating_ctr($order_id, $rater_id, $rated_id, $rating, $review)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit rating']);
    }

} elseif ($action === 'get') {
    $user_id = $_GET['user_id'] ?? 0;
    $ratings = get_user_ratings_ctr($user_id);
    echo json_encode(['status' => 'success', 'data' => $ratings]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
