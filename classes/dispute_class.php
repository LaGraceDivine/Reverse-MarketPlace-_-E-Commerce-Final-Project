<?php
require_once "db_connection.php";

class Dispute extends Database {

    // Create a new dispute
    public function createDispute($user_id, $user_type, $dispute_type, $subject, $description, $order_id = null) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("INSERT INTO disputes (user_id, user_type, dispute_type, subject, description, order_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $user_type, $dispute_type, $subject, $description, $order_id]);
            return $conn->lastInsertId();
        } catch (Exception $e) {
            error_log("Create dispute error: " . $e->getMessage());
            return false;
        }
    }

    // Get all disputes for a specific user
    public function getDisputesByUser($user_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("
                SELECT d.*, 
                       (SELECT COUNT(*) FROM dispute_messages WHERE dispute_id = d.id) as message_count,
                       (SELECT created_at FROM dispute_messages WHERE dispute_id = d.id ORDER BY created_at DESC LIMIT 1) as last_message_at
                FROM disputes d 
                WHERE d.user_id = ? 
                ORDER BY d.updated_at DESC
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get disputes by user error: " . $e->getMessage());
            return [];
        }
    }

    // Get dispute by ID with all details
    public function getDisputeById($dispute_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("
                SELECT d.*, 
                       COALESCE(c.full_name, 'Unknown User') as username, 
                       c.email 
                FROM disputes d
                LEFT JOIN customers c ON d.user_id = c.id
                WHERE d.id = ?
            ");
            $stmt->execute([$dispute_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get dispute by ID error: " . $e->getMessage());
            return null;
        }
    }

    // Update dispute status
    public function updateDisputeStatus($dispute_id, $status) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE disputes SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$status, $dispute_id]);
        } catch (Exception $e) {
            error_log("Update dispute status error: " . $e->getMessage());
            return false;
        }
    }

    // Update dispute priority
    public function updateDisputePriority($dispute_id, $priority) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE disputes SET priority = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$priority, $dispute_id]);
        } catch (Exception $e) {
            error_log("Update dispute priority error: " . $e->getMessage());
            return false;
        }
    }

    // Get all disputes (for admin)
    public function getAllDisputes($status = null, $limit = 100, $offset = 0) {
        try {
            $conn = $this->connect();
            $limit = (int)$limit;
            $offset = (int)$offset;
            
            if ($status) {
                $stmt = $conn->prepare("
                    SELECT d.*, 
                           COALESCE(c.full_name, 'Unknown User') as username, 
                           c.email,
                           (SELECT COUNT(*) FROM dispute_messages WHERE dispute_id = d.id) as message_count
                    FROM disputes d
                    LEFT JOIN customers c ON d.user_id = c.id
                    WHERE d.status = ?
                    ORDER BY 
                        CASE d.priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
                        d.created_at DESC
                    LIMIT $limit OFFSET $offset
                ");
                $stmt->execute([$status]);
            } else {
                $stmt = $conn->query("
                    SELECT d.*, 
                           COALESCE(c.full_name, 'Unknown User') as username, 
                           c.email,
                           (SELECT COUNT(*) FROM dispute_messages WHERE dispute_id = d.id) as message_count
                    FROM disputes d
                    LEFT JOIN customers c ON d.user_id = c.id
                    ORDER BY 
                        CASE d.priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
                        d.created_at DESC
                    LIMIT $limit OFFSET $offset
                ");
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get all disputes error: " . $e->getMessage());
            return [];
        }
    }

    // Get dispute statistics
    public function getDisputeStats() {
        try {
            $conn = $this->connect();
            
            $stmt = $conn->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as `open`,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed,
                    SUM(CASE WHEN priority = 'high' AND status IN ('open', 'in_progress') THEN 1 ELSE 0 END) as high_priority_count
                FROM disputes
            ");
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'total' => (int)$result['total'],
                    'open' => (int)$result['open'],
                    'in_progress' => (int)$result['in_progress'],
                    'resolved' => (int)$result['resolved'],
                    'closed' => (int)$result['closed'],
                    'high_priority' => (int)$result['high_priority_count']
                ];
            }
            
            return [
                'total' => 0,
                'open' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'closed' => 0,
                'high_priority' => 0
            ];
        } catch (Exception $e) {
            error_log("Get dispute stats error: " . $e->getMessage());
            return [
                'total' => 0,
                'open' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'closed' => 0,
                'high_priority' => 0
            ];
        }
    }

    // Send message in dispute
    public function sendDisputeMessage($dispute_id, $sender_id, $sender_type, $message, $attachment = null) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("INSERT INTO dispute_messages (dispute_id, sender_id, sender_type, message, attachment) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$dispute_id, $sender_id, $sender_type, $message, $attachment]);
            
            // Update dispute's updated_at timestamp
            if ($result) {
                $updateStmt = $conn->prepare("UPDATE disputes SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$dispute_id]);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Send dispute message error: " . $e->getMessage());
            return false;
        }
    }

    // Get all messages for a dispute
    public function getDisputeMessages($dispute_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("
                SELECT dm.*, 
                       CASE 
                           WHEN dm.sender_type = 'user' THEN COALESCE(c.full_name, 'User')
                           WHEN dm.sender_type = 'admin' THEN 'Support Team'
                           ELSE 'Unknown'
                       END as sender_name
                FROM dispute_messages dm
                LEFT JOIN customers c ON dm.sender_id = c.id AND dm.sender_type = 'user'
                WHERE dm.dispute_id = ?
                ORDER BY dm.created_at ASC
            ");
            $stmt->execute([$dispute_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get dispute messages error: " . $e->getMessage());
            return [];
        }
    }

    // Get unread dispute count for user
    public function getUnreadDisputeCount($user_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("
                SELECT COUNT(DISTINCT d.id) as count
                FROM disputes d
                INNER JOIN dispute_messages dm ON d.id = dm.dispute_id
                WHERE d.user_id = ? 
                AND dm.sender_type = 'admin'
                AND dm.created_at > (
                    SELECT COALESCE(MAX(created_at), '1970-01-01')
                    FROM dispute_messages 
                    WHERE dispute_id = d.id 
                    AND sender_type = 'user'
                )
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Get unread dispute count error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
