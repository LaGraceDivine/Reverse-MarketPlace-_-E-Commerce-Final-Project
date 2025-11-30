<?php
require_once __DIR__ . "/../classes/offer_class.php";

function create_offer_ctr($request_id, $seller_id, $price, $delivery_time, $message, $image = null, $brand = null) {
    $offer = new Offer();
    return $offer->createOffer($request_id, $seller_id, $price, $delivery_time, $message, $image, $brand);
}

function get_offers_by_request_ctr($request_id) {
    $offer = new Offer();
    return $offer->getOffersByRequest($request_id);
}

function get_offers_by_seller_ctr($seller_id) {
    $offer = new Offer();
    return $offer->getOffersBySeller($seller_id);
}

function get_offer_by_id_ctr($id) {
    $offer = new Offer();
    return $offer->getOfferById($id);
}

function update_offer_status_ctr($id, $status) {
    $offer = new Offer();
    return $offer->updateOfferStatus($id, $status);
}

function withdraw_offer_ctr($id, $seller_id) {
    $offer = new Offer();
    return $offer->withdrawOffer($id, $seller_id);
}
?>
