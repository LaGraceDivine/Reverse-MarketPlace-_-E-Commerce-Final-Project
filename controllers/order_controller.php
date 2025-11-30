<?php
require_once __DIR__ . "/../classes/order_class.php";

function create_order_ctr($request_id, $offer_id, $buyer_id, $seller_id, $total_amount) {
    $order = new Order();
    return $order->createOrder($request_id, $offer_id, $buyer_id, $seller_id, $total_amount);
}

function get_order_by_id_ctr($id) {
    $order = new Order();
    return $order->getOrderById($id);
}

function get_orders_by_buyer_ctr($buyer_id) {
    $order = new Order();
    return $order->getOrdersByBuyer($buyer_id);
}

function get_orders_by_seller_ctr($seller_id) {
    $order = new Order();
    return $order->getOrdersBySeller($seller_id);
}

function update_payment_status_ctr($id, $status, $reference = null) {
    $order = new Order();
    return $order->updatePaymentStatus($id, $status, $reference);
}

function update_delivery_status_ctr($id, $status) {
    $order = new Order();
    return $order->updateDeliveryStatus($id, $status);
}
