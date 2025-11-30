<?php
require_once "db_connection.php";

class Request extends Database {

    // Create a new request
    public function createRequest($buyer_id, $title, $description, $category, $max_budget, $image) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("INSERT INTO requests (buyer_id, title, description, category, max_budget, image) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$buyer_id, $title, $description, $category, $max_budget, $image])) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Get all requests (for admin)
    public function getAllRequests() {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT requests.*, customers.full_name as buyer_name FROM requests JOIN customers ON requests.buyer_id = customers.id ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get active requests (for sellers)
    public function getActiveRequests($search = "", $category = "") {
        try {
            $conn = $this->connect();
            // Select all requests; LEFT JOIN customers so requests show even if buyer record is missing
            $sql = "SELECT requests.*, customers.full_name as buyer_name, 
                    (SELECT COUNT(*) FROM offers WHERE offers.request_id = requests.id AND offers.status = 'pending') as offer_count,
                    (SELECT MIN(price) FROM offers WHERE offers.request_id = requests.id AND offers.status = 'pending') as lowest_offer
                    FROM requests 
                    LEFT JOIN customers ON requests.buyer_id = customers.id 
                    WHERE 1 = 1";

            $params = [];
            if (!empty($search)) {
                $sql .= " AND (title LIKE ? OR description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if (!empty($category)) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get requests by buyer ID
    public function getRequestsByBuyer($buyer_id) {
        try {
            $conn = $this->connect();
            $sql = "SELECT requests.*, 
                    (SELECT COUNT(*) FROM offers WHERE offers.request_id = requests.id AND offers.status = 'pending') as offer_count,
                    (SELECT MIN(price) FROM offers WHERE offers.request_id = requests.id AND offers.status = 'pending') as lowest_offer
                    FROM requests WHERE buyer_id = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$buyer_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get single request details
    public function getRequestById($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT requests.*, customers.full_name as buyer_name FROM requests JOIN customers ON requests.buyer_id = customers.id WHERE requests.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    // Update request status
    public function updateRequestStatus($id, $status) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
