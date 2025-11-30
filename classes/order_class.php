<?php
require_once "db_connection.php";

class Order extends Database {

    // Create a new order
    public function createOrder($request_id, $offer_id, $buyer_id, $seller_id, $total_amount) {
        try {
            $conn = $this->connect();
            
            // Generate unique invoice number (using a simpler format that won't overflow)
            // Format: YYYYMMDDXXX where XXX is a random 3-digit number
            // This gives us a number like 2025112901 (10 digits max)
            $invoice_no = (int)(date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT));
            
            // Insert order - customer_id is set to buyer_id (they are the same)
            $stmt = $conn->prepare("INSERT INTO orders 
                (customer_id, invoice_no, order_date, order_status, request_id, offer_id, buyer_id, seller_id, total_amount, payment_status, delivery_status, created_at) 
                VALUES (?, ?, NOW(), 'pending', ?, ?, ?, ?, ?, 'pending', 'pending', NOW())");
            
            $result = $stmt->execute([$buyer_id, $invoice_no, $request_id, $offer_id, $buyer_id, $seller_id, $total_amount]);
            
            if ($result) {
                return $conn->lastInsertId();
            }
            
            // Log the detailed error if it fails
            $errorInfo = $stmt->errorInfo();
            error_log("Order creation failed. SQL Error: " . $errorInfo[2]);
            error_log("Order creation failed. Full error info: " . print_r($errorInfo, true));
            return false;
        } catch (Exception $e) {
            error_log("Order creation exception: " . $e->getMessage());
            error_log("Order creation exception trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // Get order by ID
    public function getOrderById($id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT orders.*, requests.title as request_title, 
                                    buyer.full_name as buyer_name, seller.full_name as seller_name, seller.company_name
                                    FROM orders 
                                    JOIN requests ON orders.request_id = requests.id
                                    JOIN customers as buyer ON orders.buyer_id = buyer.id
                                    JOIN customers as seller ON orders.seller_id = seller.id
                                    WHERE orders.order_id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    // Get orders by buyer
    public function getOrdersByBuyer($buyer_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT orders.*, requests.title as request_title, seller.full_name as seller_name, seller.company_name,
                                    (SELECT COUNT(*) FROM ratings WHERE ratings.order_id = orders.order_id AND ratings.rater_id = orders.buyer_id) as has_rated
                                    FROM orders 
                                    JOIN requests ON orders.request_id = requests.id
                                    JOIN customers as seller ON orders.seller_id = seller.id
                                    WHERE orders.buyer_id = ? 
                                    ORDER BY created_at DESC");
            $stmt->execute([$buyer_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get orders by seller
    public function getOrdersBySeller($seller_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT orders.*, requests.title as request_title, buyer.full_name as buyer_name,
                                    (SELECT COUNT(*) FROM ratings WHERE ratings.order_id = orders.order_id AND ratings.rater_id = orders.seller_id) as has_rated
                                    FROM orders 
                                    JOIN requests ON orders.request_id = requests.id
                                    JOIN customers as buyer ON orders.buyer_id = buyer.id
                                    WHERE orders.seller_id = ? 
                                    ORDER BY created_at DESC");
            $stmt->execute([$seller_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Update payment status
    public function updatePaymentStatus($id, $status, $reference = null) {
        try {
            $conn = $this->connect();
            $sql = "UPDATE orders SET payment_status = ?";
            $params = [$status];
            if ($reference) {
                $sql .= ", payment_reference = ?";
                $params[] = $reference;
            }
            $sql .= " WHERE order_id = ?";
            $params[] = $id;
            
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }

    // Update delivery status
    public function updateDeliveryStatus($id, $status) {
        try {
            $conn = $this->connect();
            $sql = "UPDATE orders SET delivery_status = ?";
            if ($status == 'delivered') {
                $sql .= ", completed_at = CURRENT_TIMESTAMP";
            }
            $sql .= " WHERE order_id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$status, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
}