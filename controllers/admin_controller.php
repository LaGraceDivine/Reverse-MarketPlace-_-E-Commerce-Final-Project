<?php
require_once __DIR__ . "/../classes/admin_class.php";

function get_dashboard_stats_ctr() {
    $admin = new Admin();
    return $admin->getStats();
}

function get_all_users_ctr() {
    $admin = new Admin();
    return $admin->getAllUsers();
}

function update_user_status_ctr($id, $status) {
    $admin = new Admin();
    return $admin->updateUserStatus($id, $status);
}

function delete_user_ctr($id) {
    $admin = new Admin();
    return $admin->deleteUser($id);
}

function add_category_ctr($name, $description) {
    $admin = new Admin();
    return $admin->addCategory($name, $description);
}

function get_all_categories_ctr() {
    $admin = new Admin();
    return $admin->getAllCategories();
}

function delete_category_ctr($id) {
    $admin = new Admin();
    return $admin->deleteCategory($id);
}
?>
