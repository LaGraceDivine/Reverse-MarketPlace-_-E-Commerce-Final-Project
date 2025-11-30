<?php
session_start();
require_once "../controllers/offer_controller.php";
require_once "../controllers/request_controller.php";
require_once "../controllers/order_controller.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $seller_id = $_SESSION['user_id'];
    $request_id = $_POST['request_id'] ?? 0;
    $price = $_POST['price'] ?? 0;
    $delivery_time = $_POST['delivery_time'] ?? '';
    $message = $_POST['message'] ?? '';
    $brand = $_POST['brand'] ?? null;

    // Optional product image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            if ($_FILES['image']['size'] <= 5000000) { // 5MB
                $new_name = uniqid('offer_', true) . '.' . $ext;
                $destDir = '../uploads/offers/';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $dest = $destDir . $new_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    // Store relative path so it can be referenced in the UI
                    $imagePath = 'uploads/offers/' . $new_name;
                }
            }
        }
    }

    // Validate price vs budget
    $request = get_request_by_id_ctr($request_id);
    if (!$request) {
        echo json_encode(['status' => 'error', 'message' => 'Request not found']);
        exit;
    }
    if ($price > $request['max_budget']) {
        echo json_encode(['status' => 'error', 'message' => 'Price exceeds budget']);
        exit;
    }

    if (create_offer_ctr($request_id, $seller_id, $price, $delivery_time, $message, $imagePath, $brand)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create offer']);
    }

} elseif ($action === 'fetch_seller') {
    $offers = get_offers_by_seller_ctr($_SESSION['user_id']);
    echo json_encode(['status' => 'success', 'data' => $offers]);

} elseif ($action === 'withdraw') {
    $id = $_POST['id'] ?? 0;
    if (withdraw_offer_ctr($id, $_SESSION['user_id'])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to withdraw offer']);
    }

} elseif ($action === 'decline') {
    // Buyer declines an offer
    error_log("Decline action called with offer_id: " . ($_POST['offer_id'] ?? 'none'));
    $offer_id = $_POST['offer_id'] ?? 0;
    $offer = get_offer_by_id_ctr($offer_id);

    if (!$offer) {
        error_log("Offer not found for decline ID: $offer_id");
        echo json_encode(['status' => 'error', 'message' => 'Offer not found']);
        exit;
    }

    // Ensure the current user is the buyer who owns the request
    $request = get_request_by_id_ctr($offer['request_id']);
    if (!$request || $request['buyer_id'] != $_SESSION['user_id']) {
        error_log("Unauthorized decline. Request buyer: " . ($request['buyer_id'] ?? 'none') . ", Session user: " . $_SESSION['user_id']);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    error_log("Attempting to decline offer ID: $offer_id");
    $decline_result = update_offer_status_ctr($offer_id, 'rejected');
    error_log("Decline result: " . ($decline_result ? 'success' : 'failed'));
    
    if ($decline_result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to decline offer - check server logs for database error details']);
    }

} elseif ($action === 'accept') {
    // Buyer accepts offer
    error_log("Accept action called with offer_id: " . ($_POST['offer_id'] ?? 'none'));
    $offer_id = $_POST['offer_id'] ?? 0;
    $offer = get_offer_by_id_ctr($offer_id);
    
    if (!$offer) {
        error_log("Offer not found for ID: $offer_id");
        echo json_encode(['status' => 'error', 'message' => 'Offer not found']);
        exit;
    }
    
    $request = get_request_by_id_ctr($offer['request_id']);
    if ($request['buyer_id'] != $_SESSION['user_id']) {
        error_log("Unauthorized access. Request buyer: " . $request['buyer_id'] . ", Session user: " . $_SESSION['user_id']);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    
    // Create order
    error_log("Creating order for request: " . $request['id'] . ", offer: " . $offer['id']);
    $order_id = create_order_ctr($request['id'], $offer['id'], $_SESSION['user_id'], $offer['seller_id'], $offer['price']);
    
    if ($order_id) {
        // Update offer status
        $offer_update = update_offer_status_ctr($offer['id'], 'accepted');
        error_log("Offer status update result: " . ($offer_update ? 'success' : 'failed'));
        
        // Update request status
        $request_update = update_request_status_ctr($request['id'], 'completed');
        error_log("Request status update result: " . ($request_update ? 'success' : 'failed'));
        
        error_log("Order created successfully with ID: $order_id");
        echo json_encode(['status' => 'success', 'order_id' => $order_id]);
    } else {
        error_log("Failed to create order");
        echo json_encode(['status' => 'error', 'message' => 'Failed to create order - check server logs for database error details']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
