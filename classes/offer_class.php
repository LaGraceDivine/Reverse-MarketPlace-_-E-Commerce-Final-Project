<?php
require_once "db_connection.php";

class Offer extends Database {

    // Create a new offer
    public function createOffer($request_id, $seller_id, $price, $delivery_time, $message, $image = null, $brand = null) {
        try {
            $conn = $this->connect();
            // Match the actual offers table: request_id, seller_id, price, delivery_time, message, status, created_at
            $stmt = $conn->prepare(
                "INSERT INTO offers (request_id, seller_id, price, delivery_time, message, status, created_at)
                 VALUES (?, ?, ?, ?, ?, 'pending', NOW())"
            );
            return $stmt->execute([$request_id, $seller_id, $price, $delivery_time, $message]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Get offers for a specific request (only pending offers)
    public function getOffersByRequest($request_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT offers.*, customers.full_name as seller_name, customers.company_name, customers.average_rating, customers.total_ratings, customers.profile_image 
                                    FROM offers 
                                    JOIN customers ON offers.seller_id = customers.id 
                                    WHERE request_id = ? AND offers.status = 'pending' 
                                    ORDER BY price ASC");
            $stmt->execute([$request_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get offers by seller
    public function getOffersBySeller($seller_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT offers.*, requests.title as request_title 
                                    FROM offers 
                                    JOIN requests ON offers.request_id = requests.id 
                                    WHERE offers.seller_id = ? 
                                    ORDER BY created_at DESC");
            $stmt->execute([$seller_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get single offer
    public function getOfferById($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT * FROM offers WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    // Update offer status
    public function updateOfferStatus($id, $status) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE offers SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $id]);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Offer status update failed. SQL Error: " . $errorInfo[2]);
                error_log("Offer status update failed. Full error info: " . print_r($errorInfo, true));
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Offer status update exception: " . $e->getMessage());
            return false;
        }
    }
    
    // Withdraw offer (only if pending)
    public function withdrawOffer($id, $seller_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("UPDATE offers SET status = 'withdrawn' WHERE id = ? AND seller_id = ? AND status = 'pending'");
            return $stmt->execute([$id, $seller_id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
