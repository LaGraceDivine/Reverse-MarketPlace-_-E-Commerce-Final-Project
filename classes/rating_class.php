<?php
require_once "db_connection.php";

class Rating extends Database {

    // Add a rating
    public function addRating($order_id, $rater_id, $rated_id, $rating, $review) {
        try {
            $conn = $this->connect();
            
            // Check if already rated
            $check = $conn->prepare("SELECT id FROM ratings WHERE order_id = ? AND rater_id = ?");
            $check->execute([$order_id, $rater_id]);
            if ($check->rowCount() > 0) {
                return "Already rated";
            }

            // Insert rating
            $stmt = $conn->prepare("INSERT INTO ratings (order_id, rater_id, rated_id, rating, review) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$order_id, $rater_id, $rated_id, $rating, $review])) {
                // Update user average rating
                $this->updateUserRating($rated_id);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Update user's average rating
    private function updateUserRating($user_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings WHERE rated_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $avg = $result['avg_rating'] ? $result['avg_rating'] : 0;
            $count = $result['total_ratings'] ? $result['total_ratings'] : 0;
            
            $update = $conn->prepare("UPDATE customers SET average_rating = ?, total_ratings = ? WHERE id = ?");
            $update->execute([$avg, $count, $user_id]);
        } catch (Exception $e) {
            // Log error
        }
    }

    // Get ratings for a user
    public function getUserRatings($user_id) {
        try {
            $conn = $this->connect();
            $stmt = $conn->prepare("SELECT ratings.*, customers.full_name as rater_name, customers.profile_image 
                                    FROM ratings 
                                    JOIN customers ON ratings.rater_id = customers.id 
                                    WHERE rated_id = ? 
                                    ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
