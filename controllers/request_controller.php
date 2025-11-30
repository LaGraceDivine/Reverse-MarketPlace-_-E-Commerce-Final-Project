<?php
require_once __DIR__ . "/../classes/request_class.php";

function create_request_ctr($buyer_id, $title, $description, $category, $max_budget, $image) {
    $request = new Request();
    return $request->createRequest($buyer_id, $title, $description, $category, $max_budget, $image);
}

function get_all_requests_ctr() {
    $request = new Request();
    return $request->getAllRequests();
}

function get_active_requests_ctr($search = "", $category = "") {
    $request = new Request();
    return $request->getActiveRequests($search, $category);
}

function get_requests_by_buyer_ctr($buyer_id) {
    $request = new Request();
    return $request->getRequestsByBuyer($buyer_id);
}

function get_request_by_id_ctr($id) {
    $request = new Request();
    return $request->getRequestById($id);
}

function update_request_status_ctr($id, $status) {
    $request = new Request();
    return $request->updateRequestStatus($id, $status);
}
?>
