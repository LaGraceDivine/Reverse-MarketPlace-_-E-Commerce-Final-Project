<?php
session_start();
require_once "../controllers/request_controller.php";
require_once "../controllers/offer_controller.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_id = $_SESSION['user_id'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $max_budget = $_POST['max_budget'] ?? '';
    
    // Image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            if ($_FILES['image']['size'] <= 5000000) { // 5MB
                $new_name = uniqid() . '.' . $ext;
                $dest = "../uploads/requests/" . $new_name;
                if (!is_dir("../uploads/requests/")) {
                    mkdir("../uploads/requests/", 0755, true);
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image = $new_name;
                }
            }
        }
    }

    if (create_request_ctr($buyer_id, $title, $description, $category, $max_budget, $image)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create request']);
    }

} elseif ($action === 'fetch_buyer') {
    $requests = get_requests_by_buyer_ctr($_SESSION['user_id']);
    echo json_encode(['status' => 'success', 'data' => $requests]);

} elseif ($action === 'fetch_active') {
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    $requests = get_active_requests_ctr($search, $category);
    echo json_encode(['status' => 'success', 'data' => $requests]);

} elseif ($action === 'get_details') {
    $id = $_GET['id'] ?? 0;
    $request = get_request_by_id_ctr($id);
    if ($request) {
        // If buyer is viewing their own request, fetch offers too
        if ($request['buyer_id'] == $_SESSION['user_id']) {
            $offers = get_offers_by_request_ctr($id);
            $request['offers'] = $offers;
        }
        echo json_encode(['status' => 'success', 'data' => $request]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Request not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
