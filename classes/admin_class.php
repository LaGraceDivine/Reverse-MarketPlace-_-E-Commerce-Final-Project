<?php
require_once "db_connection.php";

class Admin extends Database {

    public function getStats() {
        try {
            $conn = $this->connect();
            $stats = [];
            
            // Total Users
            $stmt = $conn->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN user_role = 1 THEN 1 ELSE 0 END) as buyers,
                SUM(CASE WHEN user_role = 2 THEN 1 ELSE 0 END) as sellers
                FROM customers");
            $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Active Requests
            $stmt = $conn->query("SELECT COUNT(*) as count FROM requests WHERE status = 'active'");
            $stats['active_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Total Offers
            $stmt = $conn->query("SELECT COUNT(*) as count FROM offers");
            $stats['total_offers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Completed Orders
            $stmt = $conn->query("SELECT COUNT(*) as count FROM orders WHERE delivery_status = 'delivered'");
            $stats['completed_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Revenue
            $stmt = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'");
            $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            return $stats;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAllUsers() {
        try {
            $conn = $this->connect();
            
            // First, try a simple query to see if we can fetch users at all
            $stmt = $conn->query("SELECT c.*, 
                                  COALESCE((SELECT COUNT(*) FROM ratings r WHERE r.rated_id = c.id AND r.rating < 3), 0) as low_rating_count 
                                  FROM customers c 
                                  WHERE c.user_role != 3
                                  ORDER BY c.id DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("getAllUsers Success: Found " . count($users) . " users");
            return $users;
        } catch (Exception $e) {
            error_log("getAllUsers Error: " . $e->getMessage());
            error_log("getAllUsers Stack Trace: " . $e->getTraceAsString());
            
            // Try a simpler query without ratings
            try {
                $conn = $this->connect();
                $stmt = $conn->query("SELECT * FROM customers WHERE user_role != 3 ORDER BY id DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("getAllUsers Fallback Success: Found " . count($users) . " users");
                return $users;
            } catch (Exception $e2) {
                error_log("getAllUsers Fallback Error: " . $e2->getMessage());
                return [];
            }
        }
    }

    public function updateUserStatus($id, $status) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE customers SET is_active = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Category Management
    public function addCategory($name, $description) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            return $stmt->execute([$name, $description]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getAllCategories() {
        try {
            $conn = $this->connect();
            $stmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function deleteCategory($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
