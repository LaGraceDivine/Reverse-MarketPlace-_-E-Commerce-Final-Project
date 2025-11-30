<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/dispute_class.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$dispute = new Dispute();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        // Create new dispute
        $user_id = $_SESSION['user_id'];
        $user_type = $_SESSION['user_type'] ?? 'buyer';
        $dispute_type = $_POST['dispute_type'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $description = $_POST['description'] ?? '';
        $order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : null;
        
        if (empty($dispute_type) || empty($subject) || empty($description)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }
        
        $dispute_id = $dispute->createDispute($user_id, $user_type, $dispute_type, $subject, $description, $order_id);
        
        if ($dispute_id) {
            // Add initial message if provided
            if (!empty($description)) {
                $dispute->sendDisputeMessage($dispute_id, $user_id, 'user', $description);
            }
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Dispute created successfully',
                'dispute_id' => $dispute_id
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create dispute']);
        }
        break;
        
    case 'get_user_disputes':
        // Get all disputes for logged-in user
        $user_id = $_SESSION['user_id'];
        $disputes = $dispute->getDisputesByUser($user_id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $disputes
        ]);
        break;
        
    case 'get_dispute_details':
        // Get specific dispute with messages
        $dispute_id = $_GET['id'] ?? 0;
        
        if (!$dispute_id) {
            echo json_encode(['status' => 'error', 'message' => 'Dispute ID required']);
            exit;
        }
        
        $dispute_data = $dispute->getDisputeById($dispute_id);
        
        if (!$dispute_data) {
            echo json_encode(['status' => 'error', 'message' => 'Dispute not found']);
            exit;
        }
        
        // Check if user has access to this dispute
        if ($dispute_data['user_id'] != $_SESSION['user_id'] && !isset($_SESSION['is_admin'])) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit;
        }
        
        $messages = $dispute->getDisputeMessages($dispute_id);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'dispute' => $dispute_data,
                'messages' => $messages
            ]
        ]);
        break;
        
    case 'send_message':
        // Send message in dispute thread
        $dispute_id = $_POST['dispute_id'] ?? 0;
        $message = $_POST['message'] ?? '';
        
        if (!$dispute_id || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }
        
        // Verify user has access to this dispute
        $dispute_data = $dispute->getDisputeById($dispute_id);
        if (!$dispute_data || ($dispute_data['user_id'] != $_SESSION['user_id'] && !isset($_SESSION['is_admin']))) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit;
        }
        
        $sender_type = isset($_SESSION['is_admin']) ? 'admin' : 'user';
        $sender_id = $_SESSION['user_id'];
        
        // Handle file upload if present
        $attachment = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/disputes/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $file_name = 'dispute_' . $dispute_id . '_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
                $attachment = $file_name;
            }
        }
        
        $result = $dispute->sendDisputeMessage($dispute_id, $sender_id, $sender_type, $message, $attachment);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Message sent']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
        }
        break;
        
    case 'get_unread_count':
        // Get unread dispute count
        $user_id = $_SESSION['user_id'];
        $count = $dispute->getUnreadDisputeCount($user_id);
        
        echo json_encode([
            'status' => 'success',
            'count' => $count
        ]);
        break;
        
    // Admin-only actions
    case 'get_all':
        error_log("get_all called - is_admin: " . (isset($_SESSION['is_admin']) ? 'true' : 'false') . ", user_role: " . ($_SESSION['user_role'] ?? 'not set'));
        
        // Check if user is admin (either by is_admin flag or user_role = 3)
        if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 3)) {
            error_log("Admin access denied - session: " . print_r($_SESSION, true));
            echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
            exit;
        }
        
        // Set is_admin flag if user_role is 3
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 3) {
            $_SESSION['is_admin'] = true;
        }
        
        $status = $_GET['status'] ?? null;
        $disputes = $dispute->getAllDisputes($status);
        
        error_log("Disputes fetched: " . count($disputes));
        
        echo json_encode([
            'status' => 'success',
            'data' => $disputes
        ]);
        break;
        
    case 'get_stats':
        // Check if user is admin (either by is_admin flag or user_role = 3)
        if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 3)) {
            echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
            exit;
        }
        
        $stats = $dispute->getDisputeStats();
        
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
        break;
        
    case 'update_status':
        if (!isset($_SESSION['is_admin'])) {
            echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
            exit;
        }
        
        $dispute_id = $_POST['dispute_id'] ?? 0;
        $status = $_POST['status'] ?? '';
        
        if (!$dispute_id || empty($status)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }
        
        $result = $dispute->updateDisputeStatus($dispute_id, $status);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Status updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
        }
        break;
        
    case 'update_priority':
        if (!isset($_SESSION['is_admin'])) {
            echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
            exit;
        }
        
        $dispute_id = $_POST['dispute_id'] ?? 0;
        $priority = $_POST['priority'] ?? '';
        
        if (!$dispute_id || empty($priority)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }
        
        $result = $dispute->updateDisputePriority($dispute_id, $priority);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Priority updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update priority']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
