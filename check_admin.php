<?php
require_once "classes/db_connection.php";

$db = new Database();
$conn = $db->connect();

// Check for admin users
echo "<h2>Admin Users in Database</h2>";
try {
    $stmt = $conn->query("SELECT id, full_name, email, user_role FROM customers WHERE user_role = 3");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($admins) > 0) {
        echo "<p>Found " . count($admins) . " admin user(s):</p>";
        echo "<pre>";
        print_r($admins);
        echo "</pre>";
    } else {
        echo "<p style='color:red;'>NO ADMIN USERS FOUND!</p>";
        echo "<p>You need to create an admin user. Run this SQL:</p>";
        echo "<code>UPDATE customers SET user_role = 3 WHERE email = 'your-email@example.com';</code>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Check all users and their roles
echo "<h2>All Users and Roles</h2>";
try {
    $stmt = $conn->query("SELECT id, full_name, email, user_role FROM customers");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
    foreach ($users as $user) {
        $role = $user['user_role'] == 1 ? 'Buyer' : ($user['user_role'] == 2 ? 'Seller' : 'Admin');
        echo "<tr><td>{$user['id']}</td><td>{$user['full_name']}</td><td>{$user['email']}</td><td>{$role} ({$user['user_role']})</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
