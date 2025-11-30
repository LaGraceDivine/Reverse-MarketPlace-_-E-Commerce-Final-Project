<?php
require_once __DIR__ . "/../classes/payment_class.php";

function initialize_payment_ctr($email, $amount, $order_id, $callback_url) {
    $payment = new Payment();
    return $payment->initializePayment($email, $amount, $order_id, $callback_url);
}

function verify_payment_ctr($reference) {
    $payment = new Payment();
    return $payment->verifyPayment($reference);
}
