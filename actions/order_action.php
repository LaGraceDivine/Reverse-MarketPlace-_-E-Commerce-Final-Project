<?php
session_start();
require_once "../controllers/order_controller.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'fetch_buyer') {
    $orders = get_orders_by_buyer_ctr($_SESSION['user_id']);
    echo json_encode(['status' => 'success', 'data' => $orders]);

} elseif ($action === 'fetch_seller') {
    $orders = get_orders_by_seller_ctr($_SESSION['user_id']);
    echo json_encode(['status' => 'success', 'data' => $orders]);

} elseif ($action === 'mark_delivered') {
    $id = $_POST['id'] ?? 0;
    // Verify ownership
    $order = get_order_by_id_ctr($id);
    if ($order && $order['buyer_id'] == $_SESSION['user_id']) {
        if (update_delivery_status_ctr($id, 'delivered')) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    }

} elseif ($action === 'mark_shipped') {
    $id = $_POST['id'] ?? 0;
    // Verify ownership
    $order = get_order_by_id_ctr($id);
    if ($order && $order['seller_id'] == $_SESSION['user_id']) {
        if (update_delivery_status_ctr($id, 'in_transit')) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
