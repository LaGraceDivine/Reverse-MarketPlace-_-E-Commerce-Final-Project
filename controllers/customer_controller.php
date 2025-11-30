<?php
require_once __DIR__ . "/../classes/customer_class.php";

/**
 * Register a new customer
 */
function register_customer_ctr($data) {
    $customer = new Customer();
    return $customer->addCustomer(
        $data['full_name'],
        $data['email'],
        $data['password'],
        $data['country'],
        $data['city'],
        $data['contact_number'],
        $data['user_role'] ?? 2  // Default to seller if not specified
    );
}

/**
 * Get customer by email
 */
function get_customer_by_email_ctr($email) {
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

/**
 * Login customer (alternative method)
 */
function login_customer_ctr($email, $password) {
    $customer = new Customer();
    return $customer->loginCustomer($email, $password);
}

/**
 * Get customer by ID
 */
function get_customer_by_id_ctr($id) {
    $customer = new Customer();
    return $customer->getCustomerById($id);
}
?>