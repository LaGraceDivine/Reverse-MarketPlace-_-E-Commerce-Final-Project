<?php
session_start();
require_once "../controllers/admin_controller.php";

header('Content-Type: application/json');

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 3) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'stats') {
    $stats = get_dashboard_stats_ctr();
    echo json_encode(['status' => 'success', 'data' => $stats]);

} elseif ($action === 'users') {
    $users = get_all_users_ctr();
    echo json_encode(['status' => 'success', 'data' => $users]);

} elseif ($action === 'toggle_user') {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? 1;
    if (update_user_status_ctr($id, $status)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed']);
    }

} elseif ($action === 'delete_user') {
    $id = $_POST['id'] ?? 0;
    if (delete_user_ctr($id)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed']);
    }

} elseif ($action === 'categories') {
    $categories = get_all_categories_ctr();
    echo json_encode(['status' => 'success', 'data' => $categories]);

} elseif ($action === 'add_category') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    if (add_category_ctr($name, $description)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed']);
    }

} elseif ($action === 'delete_category') {
    $id = $_POST['id'] ?? 0;
    if (delete_category_ctr($id)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
