<?php
session_start();
require_once "../controllers/chat_controller.php";
require_once "../controllers/order_controller.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $message = $_POST['message'] ?? '';
    
    $order = get_order_by_id_ctr($order_id);
    if (!$order) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }
    
    // Determine receiver
    $receiver_id = 0;
    if ($_SESSION['user_id'] == $order['buyer_id']) {
        $receiver_id = $order['seller_id'];
    } elseif ($_SESSION['user_id'] == $order['seller_id']) {
        $receiver_id = $order['buyer_id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    if (send_message_ctr($order_id, $_SESSION['user_id'], $receiver_id, $message)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }

} elseif ($action === 'fetch') {
    $order_id = $_GET['order_id'] ?? 0;
    
    // Verify access
    $order = get_order_by_id_ctr($order_id);
    if ($order && ($order['buyer_id'] == $_SESSION['user_id'] || $order['seller_id'] == $_SESSION['user_id'])) {
        $messages = get_messages_ctr($order_id);
        echo json_encode(['status' => 'success', 'data' => $messages]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    }

} elseif ($action === 'mark_read') {
    $order_id = $_POST['order_id'] ?? 0;
    mark_read_ctr($order_id, $_SESSION['user_id']);
    echo json_encode(['status' => 'success']);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
