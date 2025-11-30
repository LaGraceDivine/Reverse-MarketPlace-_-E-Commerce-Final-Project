<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 3) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
        body { display: flex; background: #f4f6f9; }
        .sidebar { width: 250px; background: #1a1c23; color: white; min-height: 100vh; padding: 20px; }
        .sidebar h2 { margin-bottom: 30px; font-size: 20px; }
        .sidebar a { display: block; padding: 12px; color: #a0aec0; text-decoration: none; margin-bottom: 5px; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #2d3748; color: white; }
        .main { flex: 1; padding: 30px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stat-card h3 { font-size: 14px; color: #666; margin-bottom: 10px; }
        .stat-card p { font-size: 24px; font-weight: bold; color: #333; }
        .section { display: none; }
        .section.active { display: block; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; color: #666; }
        .btn { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-success { background: #10b981; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; width: 400px; margin: 100px auto; padding: 20px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#" class="active" onclick="showSection('dashboard')">Dashboard</a>
    <a href="#" onclick="showSection('users')">Users</a>
    <a href="#" onclick="showSection('categories')">Categories</a>
    <a href="../actions/logout.php" style="margin-top: 50px; color: #ef4444;">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Dashboard</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Dashboard Section -->
    <div id="dashboard" class="section active">
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Users</h3><p id="totalUsers">0</p></div>
            <div class="stat-card"><h3>Active Requests</h3><p id="activeRequests">0</p></div>
            <div class="stat-card"><h3>Total Offers</h3><p id="totalOffers">0</p></div>
            <div class="stat-card"><h3>Completed Orders</h3><p id="completedOrders">0</p></div>
            <div class="stat-card"><h3>Revenue</h3><p id="totalRevenue">GH₵ 0.00</p></div>
        </div>
    </div>

    <!-- Users Section -->
    <div id="users" class="section">
        <div class="card">
            <h2>User Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable"></tbody>
            </table>
        </div>
    </div>

    <!-- Categories Section -->
    <div id="categories" class="section">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Categories</h2>
                <button class="btn btn-primary" onclick="openCategoryModal()">Add Category</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTable"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <h2>Add Category</h2>
        <input type="text" id="catName" placeholder="Name" style="width: 100%; padding: 10px; margin: 10px 0;">
        <input type="text" id="catDesc" placeholder="Description" style="width: 100%; padding: 10px; margin: 10px 0;">
        <button class="btn btn-primary" onclick="addCategory()">Save</button>
        <button class="btn" onclick="closeCategoryModal()">Cancel</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function showSection(id) {
        $('.section').removeClass('active');
        $('#' + id).addClass('active');
        $('.sidebar a').removeClass('active');
        event.target.classList.add('active');
        
        if (id === 'dashboard') fetchStats();
        if (id === 'users') fetchUsers();
        if (id === 'categories') fetchCategories();
    }

    function fetchStats() {
        $.get('../actions/admin_action.php?action=stats', function(res) {
            if (res.status === 'success') {
                $('#totalUsers').text(res.data.users.total);
                $('#activeRequests').text(res.data.active_requests);
                $('#totalOffers').text(res.data.total_offers);
                $('#completedOrders').text(res.data.completed_orders);
                $('#totalRevenue').text('GH₵ ' + res.data.total_revenue);
            }
        });
    }

    function fetchUsers() {
        $.get('../actions/admin_action.php?action=users', function(res) {
            console.log("User Fetch Response:", res);
            if (res.status === 'success') {
                let html = '';
                if (res.data.length === 0) {
                    html = '<tr><td colspan="6" style="text-align:center;">No users found</td></tr>';
                } else {
                    res.data.forEach(user => {
                        const isHighRisk = user.low_rating_count > 3;
                        const riskBadge = isHighRisk ? '<span style="background:#fee2e2; color:#b91c1c; padding:2px 6px; border-radius:4px; font-size:12px; font-weight:bold;">High Risk</span>' : '';
                        
                        html += `<tr>
                            <td>
                                ${user.full_name}
                                ${riskBadge}
                            </td>
                            <td>${user.email}</td>
                            <td>${user.user_role == 1 ? 'Buyer' : (user.user_role == 2 ? 'Seller' : 'Admin')}</td>
                            <td>
                                <div>⭐ ${parseFloat(user.average_rating || 0).toFixed(1)} (${user.total_ratings || 0})</div>
                                <div style="font-size:12px; color:#666;">${user.low_rating_count} low ratings</div>
                            </td>
                            <td>${user.is_active ? '<span style="color:green">Active</span>' : '<span style="color:red">Suspended</span>'}</td>
                            <td>
                                <button class="btn btn-sm ${user.is_active ? 'btn-danger' : 'btn-success'}" onclick="toggleUser(${user.id}, ${user.is_active ? 0 : 1})">
                                    ${user.is_active ? 'Suspend' : 'Activate'}
                                </button>
                            </td>
                        </tr>`;
                    });
                }
                $('#usersTable').html(html);
            } else {
                console.error("Fetch failed:", res.message);
                $('#usersTable').html('<tr><td colspan="6" style="text-align:center; color:red;">Failed to load users</td></tr>');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            console.log("Response Text:", jqXHR.responseText);
            $('#usersTable').html('<tr><td colspan="6" style="text-align:center; color:red;">Server Error. Check console.</td></tr>');
        });
    }

    function toggleUser(id, status) {
        $.post('../actions/admin_action.php?action=toggle_user', {id: id, status: status}, function(res) {
            if (res.status === 'success') fetchUsers();
        });
    }

    function fetchCategories() {
        $.get('../actions/admin_action.php?action=categories', function(res) {
            if (res.status === 'success') {
                let html = '';
                res.data.forEach(cat => {
                    html += `<tr>
                        <td>${cat.name}</td>
                        <td>${cat.description}</td>
                        <td><button class="btn btn-danger" onclick="deleteCategory(${cat.id})">Delete</button></td>
                    </tr>`;
                });
                $('#categoriesTable').html(html);
            }
        });
    }

    function openCategoryModal() { $('#categoryModal').show(); }
    function closeCategoryModal() { $('#categoryModal').hide(); }

    function addCategory() {
        $.post('../actions/admin_action.php?action=add_category', {
            name: $('#catName').val(),
            description: $('#catDesc').val()
        }, function(res) {
            if (res.status === 'success') {
                closeCategoryModal();
                fetchCategories();
            } else {
                alert('Failed');
            }
        });
    }

    function deleteCategory(id) {
        if(confirm('Delete?')) {
            $.post('../actions/admin_action.php?action=delete_category', {id: id}, function(res) {
                if (res.status === 'success') fetchCategories();
            });
        }
    }

    // Init
    fetchStats();
</script>
</body>
</html>