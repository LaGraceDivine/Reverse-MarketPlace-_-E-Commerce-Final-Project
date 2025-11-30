<?php
require_once __DIR__ . "/../classes/rating_class.php";

function add_rating_ctr($order_id, $rater_id, $rated_id, $rating, $review) {
    $rate = new Rating();
    return $rate->addRating($order_id, $rater_id, $rated_id, $rating, $review);
}

function get_user_ratings_ctr($user_id) {
    $rate = new Rating();
    return $rate->getUserRatings($user_id);
}
?>
