<?php
require_once "db_connection.php";

class Chat extends Database {

    // Send a message
    public function sendMessage($order_id, $sender_id, $receiver_id, $message) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("INSERT INTO chat_messages (order_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$order_id, $sender_id, $receiver_id, $message]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Get messages for an order
    public function getMessages($order_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT * FROM chat_messages WHERE order_id = ? ORDER BY created_at ASC");
            $stmt->execute([$order_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Mark messages as read
    public function markRead($order_id, $receiver_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE chat_messages SET is_read = TRUE WHERE order_id = ? AND receiver_id = ?");
            return $stmt->execute([$order_id, $receiver_id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get unread count
    public function getUnreadCount($receiver_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM chat_messages WHERE receiver_id = ? AND is_read = FALSE");
            $stmt->execute([$receiver_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }
}
?>
