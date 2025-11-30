<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get user info
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Dashboard - Reverse Marketplace</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
      background: #f8f9fb;
      color: #333;
    }

    /* Header */
    .header {
      background: white;
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e5e7eb;
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo-icon {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 28px;
    }

    .header-text h1 {
      font-size: 20px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 2px;
    }

    .header-text p {
      font-size: 14px;
      color: #666;
    }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: #f3f4f6;
      color: #333;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s;
    }

    .logout-btn:hover {
      background: #e5e7eb;
    }

    /* Tabs */
    .tabs {
      background: white;
      padding: 0 50px;
      display: flex;
      gap: 40px;
      border-bottom: 1px solid #e5e7eb;
    }

    .tab {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 20px 0;
      font-size: 16px;
      font-weight: 500;
      color: #666;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      transition: all 0.3s;
    }

    .tab:hover {
      color: #2563eb;
    }

    .tab.active {
      color: #2563eb;
      border-bottom-color: #2563eb;
    }

    .tab-icon {
      font-size: 20px;
    }

    /* Main Content */
    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 50px;
    }

    .page-header {
      margin-bottom: 30px;
    }

    .page-title h2 {
      font-size: 32px;
      margin-bottom: 8px;
      color: #1a1a1a;
    }

    .page-title p {
      font-size: 16px;
      color: #666;
    }

    /* Search Bar */
    .search-filter {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }

    .search-box {
      flex: 1;
      position: relative;
    }

    .search-box input {
      width: 100%;
      padding: 14px 16px 14px 45px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 15px;
      transition: all 0.3s;
    }

    .search-box input:focus {
      outline: none;
      border-color: #2563eb;
    }

    .search-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 18px;
    }

    .category-filter {
      min-width: 200px;
    }

    .category-filter select {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 15px;
      cursor: pointer;
      background: white;
      transition: all 0.3s;
    }

    .category-filter select:focus {
      outline: none;
      border-color: #2563eb;
    }

    /* Empty State */
    .empty-state {
      background: white;
      border-radius: 16px;
      padding: 80px 40px;
      text-align: center;
      border: 2px solid #f3f4f6;
    }

    .empty-icon {
      font-size: 80px;
      color: #d1d5db;
      margin-bottom: 20px;
    }

    .empty-state h3 {
      font-size: 24px;
      margin-bottom: 10px;
      color: #1a1a1a;
    }

    .empty-state p {
      font-size: 16px;
      color: #666;
    }

    /* Request Card */
    .request-card {
      background: white;
      border-radius: 16px;
      padding: 30px;
      border: 2px solid #f3f4f6;
      margin-bottom: 20px;
    }

    .request-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
    }

    .request-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .request-title h3 {
      font-size: 22px;
      color: #1a1a1a;
    }

    .status-badge {
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
    }

    .status-badge.open {
      background: #d1fae5;
      color: #059669;
    }

    .status-badge.pending {
      background: #fef3c7;
      color: #d97706;
    }

    .status-badge.accepted {
      background: #d1fae5;
      color: #059669;
    }

    .status-badge.rejected {
      background: #fee2e2;
      color: #b91c1c;
    }

    .status-badge.withdrawn {
      background: #f3f4f6;
      color: #6b7280;
    }

    .request-description {
      font-size: 15px;
      color: #666;
      margin-bottom: 20px;
    }

    .request-meta {
      display: flex;
      gap: 25px;
      margin-bottom: 25px;
      flex-wrap: wrap;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: #666;
    }

    .budget-section {
      background: #f8f9fb;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .budget-item {
      flex: 1;
    }

    .budget-label {
      font-size: 13px;
      color: #666;
      margin-bottom: 5px;
    }

    .budget-amount {
      font-size: 28px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .budget-amount.green {
      color: #059669;
    }

    .make-offer-btn {
      width: 100%;
      background: #2563eb;
      color: white;
      padding: 16px;
      border-radius: 10px;
      border: none;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }

    .make-offer-btn:hover {
      background: #476cd1ff;
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(70, 115, 213, 0.4);
    }

    /* Offer Card */
    .offer-card {
      background: white;
      border-radius: 16px;
      padding: 30px;
      border: 2px solid #f3f4f6;
      margin-bottom: 20px;
    }

    .offer-details {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-top: 20px;
    }

    .offer-detail-item {
      background: #f8f9fb;
      padding: 15px;
      border-radius: 10px;
    }

    .offer-detail-label {
      font-size: 13px;
      color: #666;
      margin-bottom: 5px;
    }

    .offer-detail-value {
      font-size: 20px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .offer-message {
      margin-top: 20px;
    }

    .offer-message-label {
      font-size: 13px;
      color: #666;
      margin-bottom: 8px;
      font-weight: 600;
    }

    .offer-message-text {
      font-size: 15px;
      color: #333;
      line-height: 1.6;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      backdrop-filter: blur(5px);
      animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .modal-content {
      background-color: white;
      margin: 5% auto;
      padding: 40px;
      border-radius: 20px;
      width: 90%;
      max-width: 600px;
      max-height: 85vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      animation: slideUp 0.4s;
      position: relative;
    }

    @keyframes slideUp {
      from {
        transform: translateY(50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .close {
      color: #aaa;
      position: absolute;
      right: 25px;
      top: 25px;
      font-size: 32px;
      font-weight: bold;
      cursor: pointer;
      transition: color 0.3s;
    }

    .close:hover {
      color: #333;
    }

    .modal-content h2 {
      margin-bottom: 10px;
      color: #1a1a1a;
      font-size: 28px;
    }

    .modal-subtitle {
      font-size: 14px;
      color: #666;
      margin-bottom: 30px;
    }

    .modal-subtitle span {
      font-weight: 600;
      color: #333;
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
      font-size: 15px;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #406ed1ff;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }

    .modal-buttons {
      display: flex;
      gap: 15px;
      margin-top: 30px;
    }

    .modal-btn {
      flex: 1;
      padding: 14px 20px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      border: none;
      transition: all 0.3s;
    }

    .modal-btn-cancel {
      background: #f3f4f6;
      color: #333;
    }

    .modal-btn-cancel:hover {
      background: #e5e7eb;
    }

    .modal-btn-submit {
      background: #3663c4ff;
      color: white;
    }

    .modal-btn-submit:hover {
      background: #4468cdff;
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(55, 103, 206, 0.4);
    }

    @media (max-width: 768px) {
      .header, .tabs, .container {
        padding-left: 20px;
        padding-right: 20px;
      }

      .search-filter {
        flex-direction: column;
      }

      .category-filter {
        min-width: 100%;
      }

      .budget-section {
        flex-direction: column;
        gap: 15px;
      }

      .offer-details {
        grid-template-columns: 1fr;
      }

      .modal-content {
        padding: 30px 25px;
        margin: 10% auto;
      }

      .modal-buttons {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="header-left">
      <div class="logo-icon">🛍️</div>
      <div class="header-text">
        <h1>Reverse Marketplace</h1>
        <p>Welcome, <?php echo htmlspecialchars($username); ?></p>
      </div>
    </div>
    <a href="../actions/logout.php" class="logout-btn">
      <span>↪</span>
      Logout
    </a>
  </div>

  <!-- Tabs -->
  <div class="tabs">
    <div class="tab active" id="browseTab">
      <span class="tab-icon">🔍</span>
      <span>Browse Requests (<span id="browseCount">2</span>)</span>
    </div>
    <div class="tab" id="offersTab">
      <span class="tab-icon">🏪</span>
      <span>My Offers (<span id="offersCount">0</span>)</span>
    </div>
    <div class="tab" id="ordersTab">
      <span class="tab-icon">📦</span>
      <span>Active Orders (0)</span>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <!-- Browse Requests View -->
    <div id="browseView">
      <div class="page-header">
        <div class="page-title">
          <h2>Browse Buyer Requests</h2>
        </div>
      </div>

      <div class="search-filter">
        <div class="search-box">
          <span class="search-icon">🔍</span>
          <input type="text" id="searchInput" placeholder="Search requests...">
        </div>
        <div class="category-filter">
          <select id="categoryFilter">
            <option value="">All Categories</option>
            <option value="Fashion">Fashion</option>
            <option value="Electronics">Electronics</option>
            <option value="Home & Garden">Home & Garden</option>
            <option value="Sports">Sports</option>
            <option value="Books">Books</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <div id="requestsContainer">
        <!-- Requests will be loaded here -->
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3>Loading requests...</h3>
        </div>
      </div>
    </div>

    <!-- My Offers View -->
    <div id="offersView" style="display: none;">
      <div class="page-header">
        <div class="page-title">
          <h2>My Offers</h2>
          <p>Track offers you've submitted</p>
        </div>
      </div>

      <div id="offersContainer">
        <div class="empty-state">
          <div class="empty-icon">🏪</div>
          <h3>No offers yet</h3>
          <p>Browse requests and make your first offer</p>
        </div>
      </div>
    </div>

    <!-- Active Orders View -->
    <div id="ordersView" style="display: none;">
      <div class="page-header">
        <div class="page-title">
          <h2>Active Orders</h2>
          <p>Manage and fulfill your accepted offers</p>
        </div>
      </div>

      <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>No orders yet</h3>
        <p>When a buyer accepts your offer, it will appear here</p>
      </div>
    </div>
  </div>

  <!-- Make Offer Modal -->
  <div id="offerModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Make an Offer</h2>
      <p class="modal-subtitle">
        Category: <span id="modalCategory">Fashion</span> &nbsp;&nbsp;
        Max Budget: <span id="modalBudget">GH₵ 200.00</span>
      </p>
      
      <form id="offerForm">
        <input type="hidden" id="requestTitle">
        
        <div class="form-group">
          <label>Your Price (GH₵)</label>
          <input type="number" id="offerPrice" placeholder="150" min="1" step="0.01" required>
        </div>

        <div class="form-group">
          <label>Delivery Time</label>
          <input type="text" id="deliveryTime" placeholder="same day delivery" required>
        </div>

        <div class="form-group">
          <label>Brand (Optional)</label>
          <input type="text" id="offerBrand" placeholder="e.g. Nike, Samsung">
        </div>

        <div class="form-group">
          <label>Product Image (Optional)</label>
          <input type="file" id="offerImage" accept="image/*">
        </div>

        <div class="form-group">
          <label>Additional Details (Optional)</label>
          <textarea id="offerMessage" placeholder="Describe your offer, quality, brand, etc..."></textarea>
        </div>

        <div id="offerStatusMessage" style="display:none; margin-top:10px; font-size:14px;"></div>

        <div class="modal-buttons">
          <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="submit" class="modal-btn modal-btn-submit">Submit Offer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Chat Modal -->
  <div id="chatModal" class="modal">
    <div class="modal-content" style="max-width: 500px; height: 600px; display: flex; flex-direction: column;">
      <span class="close" onclick="closeChatModal()">&times;</span>
      <h2 id="chatTitle">Chat</h2>
      <div id="chatMessages" style="flex: 1; overflow-y: auto; border: 1px solid #eee; padding: 15px; margin-bottom: 15px; border-radius: 10px; background: #f9f9f9;">
        <!-- Messages will appear here -->
      </div>
      <form id="chatForm" style="display: flex; gap: 10px;">
        <input type="hidden" id="chatOrderId">
        <input type="text" id="chatInput" placeholder="Type a message..." style="flex: 1;" required>
        <button type="submit" class="modal-btn modal-btn-submit" style="padding: 10px 20px;">Send</button>
      </form>
    </div>
  </div>

  <!-- Rating Modal -->
  <div id="ratingModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeRatingModal()">&times;</span>
      <h2>Rate Buyer</h2>
      <form id="ratingForm">
        <input type="hidden" id="ratingOrderId">
        <div class="form-group">
          <label>Rating</label>
          <select id="ratingValue" required>
            <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
            <option value="4">⭐⭐⭐⭐ (Good)</option>
            <option value="3">⭐⭐⭐ (Average)</option>
            <option value="2">⭐⭐ (Poor)</option>
            <option value="1">⭐ (Terrible)</option>
          </select>
        </div>
        <div class="form-group">
          <label>Review</label>
          <textarea id="ratingReview" placeholder="Write a review..." required></textarea>
        </div>
        <button type="submit" class="modal-btn modal-btn-submit">Submit Rating</button>
      </form>
    </div>
  </div>

  <script src="../config/config.js.php"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let currentOrderId = 0;
    let currentRequestId = 0;

    // Tab switching
    document.getElementById('browseTab').addEventListener('click', function() {
      document.getElementById('browseTab').classList.add('active');
      document.getElementById('offersTab').classList.remove('active');
      document.getElementById('ordersTab').classList.remove('active');
      document.getElementById('browseView').style.display = 'block';
      document.getElementById('offersView').style.display = 'none';
      document.getElementById('ordersView').style.display = 'none';
      fetchActiveRequests();
    });

    document.getElementById('offersTab').addEventListener('click', function() {
      document.getElementById('offersTab').classList.add('active');
      document.getElementById('browseTab').classList.remove('active');
      document.getElementById('ordersTab').classList.remove('active');
      document.getElementById('offersView').style.display = 'block';
      document.getElementById('browseView').style.display = 'none';
      document.getElementById('ordersView').style.display = 'none';
      fetchMyOffers();
    });

    document.getElementById('ordersTab').addEventListener('click', function() {
      document.getElementById('ordersTab').classList.add('active');
      document.getElementById('browseTab').classList.remove('active');
      document.getElementById('offersTab').classList.remove('active');
      document.getElementById('ordersView').style.display = 'block';
      document.getElementById('browseView').style.display = 'none';
      document.getElementById('offersView').style.display = 'none';
      fetchActiveOrders();
    });

    // Fetch Active Requests
    function fetchActiveRequests() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        
        $.ajax({
            url: '../actions/request_action.php',
            method: 'GET',
            dataType: 'json',
            data: {
                action: 'fetch_active',
                search: search,
                category: category
            },
            success: function(response) {
                if (response.status === 'success') {
                    displayRequests(response.data);
                }
            }
        });
    }

    // Initial load: fetch active requests when page is ready
    $(document).ready(function() {
        fetchActiveRequests();
    });

    // Search and Filter listeners
    document.getElementById('searchInput').addEventListener('input', fetchActiveRequests);
    document.getElementById('categoryFilter').addEventListener('change', fetchActiveRequests);

    function displayRequests(requests) {
        const container = document.getElementById('requestsContainer');
        document.getElementById('browseCount').textContent = requests.length;

        if (requests.length === 0) {
            container.innerHTML = '<div class="empty-state"><p>No active requests found matching your criteria.</p></div>';
            return;
        }

        container.innerHTML = requests.map(request => `
            <div class="request-card">
              <div class="request-header">
                <div class="request-title">
                  <h3>${request.title}</h3>
                  <span class="status-badge open">Open</span>
                </div>
              </div>
              
              <p class="request-description">${request.description}</p>
              
              ${request.image ? `<div style="margin-bottom: 20px;"><img src="../uploads/requests/${request.image}" style="max-width: 200px; border-radius: 10px; border: 1px solid #eee;"></div>` : ''}

              <div class="request-meta">
                <div class="meta-item">
                  <span>🏷️</span>
                  <span>${request.category}</span>
                </div>
                <div class="meta-item">
                  <span>⏰</span>
                  <span>${request.created_at}</span>
                </div>
                <div class="meta-item">
                  <span>👤</span>
                  <span>${request.buyer_name}</span>
                </div>
                <div class="meta-item">
                  <span>📊</span>
                  <span>${request.offer_count} offers</span>
                </div>
              </div>

              <div class="budget-section">
                <div class="budget-item">
                  <div class="budget-label">Maximum Budget</div>
                  <div class="budget-amount">GH₵ ${request.max_budget}</div>
                </div>
                <div class="budget-item" style="text-align: right;">
                  <div class="budget-label">Lowest Offer</div>
                  <div class="budget-amount green">${request.lowest_offer ? 'GH₵ ' + request.lowest_offer : 'None'}</div>
                </div>
              </div>

              <button class="make-offer-btn" onclick="openOfferModal(${request.id}, '${request.title}', '${request.category}', '${request.max_budget}')">
                Make an Offer
              </button>
            </div>
        `).join('');
    }

    // Make Offer
    function openOfferModal(id, title, category, budget) {
      currentRequestId = id;
      document.getElementById('requestTitle').value = title;
      document.getElementById('modalCategory').textContent = category;
      document.getElementById('modalBudget').textContent = 'GH₵ ' + budget;
      document.getElementById('offerModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    document.getElementById('offerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const statusBox = document.getElementById('offerStatusMessage');
        statusBox.style.display = 'none';
        statusBox.textContent = '';
        statusBox.style.color = '';

        const formData = new FormData();
        formData.append('request_id', currentRequestId);
        formData.append('price', document.getElementById('offerPrice').value);
        formData.append('delivery_time', document.getElementById('deliveryTime').value);
        formData.append('message', document.getElementById('offerMessage').value);
        formData.append('brand', document.getElementById('offerBrand').value);
        
        const imageFile = document.getElementById('offerImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: '../actions/offer_action.php?action=create',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    statusBox.style.display = 'block';
                    statusBox.style.color = '#059669';
                    statusBox.textContent = 'Offer submitted successfully!';
                    fetchActiveRequests(); // Refresh list
                } else {
                    statusBox.style.display = 'block';
                    statusBox.style.color = '#b91c1c';
                    statusBox.textContent = response.message || 'Failed to submit offer.';
                }
            }
        });
    });

    // Fetch My Offers
    function fetchMyOffers() {
        $.ajax({
            url: '../actions/offer_action.php?action=fetch_seller',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    displayOffers(response.data);
                }
            }
        });
    }

    function displayOffers(offers) {
        const container = document.getElementById('offersContainer');
        document.getElementById('offersCount').textContent = offers.length;

        if (offers.length === 0) {
            container.innerHTML = `
              <div class="empty-state">
                <div class="empty-icon">🏪</div>
                <h3>No offers yet</h3>
                <p>Browse requests and make your first offer</p>
              </div>
            `;
            return;
        }

        container.innerHTML = offers.map(offer => `
            <div class="offer-card">
              <div class="request-header">
                <div class="request-title">
                  <h3>Offer for: ${offer.request_title}</h3>
                  <span class="status-badge ${offer.status}">${offer.status}</span>
                </div>
              </div>
              
              <div class="offer-details">
                <div class="offer-detail-item">
                  <div class="offer-detail-label">Your Price</div>
                  <div class="offer-detail-value">GH₵ ${offer.price}</div>
                </div>
                <div class="offer-detail-item">
                  <div class="offer-detail-label">Delivery Time</div>
                  <div class="offer-detail-value" style="font-size: 16px;">${offer.delivery_time}</div>
                </div>
              </div>
              
              <div class="offer-message">
                <div class="offer-message-label">Your Message</div>
                <div class="offer-message-text">${offer.message}</div>
                ${offer.brand ? `<div class="offer-message-text"><strong>Brand:</strong> ${offer.brand}</div>` : ''}
                ${offer.image ? `<div style="margin-top:10px;"><img src="../${offer.image}" style="max-width:100px; border-radius:8px;"></div>` : ''}
              </div>

              ${offer.status === 'pending' ? `<button class="modal-btn modal-btn-cancel" style="margin-top: 20px; color: red;" onclick="withdrawOffer(${offer.id})">Withdraw Offer</button>` : ''}
            </div>
        `).join('');
    }

    function withdrawOffer(id) {
        if (!confirm('Are you sure you want to withdraw this offer?')) return;
        $.ajax({
            url: '../actions/offer_action.php?action=withdraw',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.status === 'success') {
                    fetchMyOffers();
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Fetch Active Orders
    function fetchActiveOrders() {
        $.ajax({
            url: '../actions/order_action.php?action=fetch_seller',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    displayOrders(response.data);
                }
            }
        });
    }

    function displayOrders(orders) {
        const container = document.getElementById('ordersView');
        // Reset to header
        container.innerHTML = `
          <div class="page-header">
            <div class="page-title">
              <h2>Active Orders</h2>
              <p>Manage and fulfill your accepted offers</p>
            </div>
          </div>
        `;

        if (orders.length === 0) {
            container.innerHTML += `
              <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>No orders yet</h3>
                <p>When a buyer accepts your offer, it will appear here</p>
              </div>
            `;
            return;
        }

        container.innerHTML += orders.map(order => `
            <div class="request-card">
              <div class="request-header">
                <div class="request-title">
                  <h3>${order.request_title}</h3>
                  <span class="status-badge" style="background: ${getStatusColor(order.delivery_status)}">${order.delivery_status}</span>
                </div>
                <div style="text-align: right;">
                    <p><strong>GH₵ ${order.total_amount}</strong></p>
                    <span class="status-badge" style="background: ${order.payment_status === 'paid' ? '#d1fae5' : '#fee2e2'}; color: ${order.payment_status === 'paid' ? '#059669' : '#b91c1c'}">${order.payment_status}</span>
                </div>
              </div>
              
              <p>Buyer: ${order.buyer_name}</p>
              <p style="font-size: 13px; color: #666; margin-top: 5px;">Order #${order.order_id} | Invoice: ${order.invoice_no}</p>
              
              <div class="modal-buttons" style="margin-top: 15px;">
                ${order.payment_status === 'pending' ? `
                  <p style="color: #d97706; font-size: 14px;">⏳ Waiting for buyer to complete payment</p>
                ` : ''}
                
                ${order.payment_status === 'paid' && order.delivery_status === 'pending' ? `
                  <button class="modal-btn modal-btn-submit" onclick="openChat(${order.order_id}, '${order.buyer_name}')">
                    💬 Chat with Buyer
                  </button>
                  <button class="modal-btn modal-btn-submit" onclick="markShipped(${order.order_id})" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    📦 Mark as Shipped
                  </button>
                ` : ''}
                
                ${order.payment_status === 'paid' && order.delivery_status === 'in_transit' ? `
                  <button class="modal-btn modal-btn-submit" onclick="openChat(${order.order_id}, '${order.buyer_name}')">
                    💬 Chat with Buyer
                  </button>
                  <p style="color: #2563eb; font-size: 14px; margin-top: 10px;">🚚 Order shipped - waiting for buyer confirmation</p>
                ` : ''}
                
                ${order.delivery_status === 'delivered' ? (
                    order.has_rated == 0 ? `
                  <button class="modal-btn modal-btn-submit" onclick="openRating(${order.order_id}, ${order.buyer_id}, '${order.buyer_name}')" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    ⭐ Rate Buyer
                  </button>
                    ` : `
                  <div style="text-align: center; color: #f59e0b; font-weight: 600; padding: 10px; background: #fffbeb; border-radius: 8px; border: 1px solid #fcd34d;">
                    ⭐ You rated this buyer
                  </div>
                    `
                ) : ''}
              </div>
            </div>
        `).join('');
    }

    function getStatusColor(status) {
        switch(status) {
            case 'pending': return '#fef3c7; color: #d97706';
            case 'in_transit': return '#dbeafe; color: #2563eb';
            case 'delivered': return '#d1fae5; color: #059669';
            default: return '#f3f4f6; color: #666';
        }
    }

    function markShipped(orderId) {
        if (!confirm('Confirm shipment?')) return;
        $.ajax({
            url: '../actions/order_action.php?action=mark_shipped',
            method: 'POST',
            data: { id: orderId },
            success: function(response) {
                if (response.status === 'success') {
                    fetchActiveOrders();
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Chat Functions (Same as buyer)
    function openChat(orderId, buyerName) {
        currentOrderId = orderId;
        document.getElementById('chatOrderId').value = orderId;
        document.getElementById('chatTitle').textContent = `Chat with ${buyerName}`;
        document.getElementById('chatModal').style.display = 'block';
        fetchMessages();
        window.chatInterval = setInterval(fetchMessages, 3000);
    }

    function closeChatModal() {
        document.getElementById('chatModal').style.display = 'none';
        clearInterval(window.chatInterval);
    }

    function fetchMessages() {
        if (!currentOrderId) return;
        $.ajax({
            url: `../actions/chat_action.php?action=fetch&order_id=${currentOrderId}`,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    const messagesDiv = document.getElementById('chatMessages');
                    messagesDiv.innerHTML = response.data.map(msg => `
                        <div style="margin-bottom: 10px; text-align: ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'right' : 'left'}">
                            <div style="display: inline-block; padding: 8px 12px; border-radius: 15px; background: ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? '#3b82f6' : '#e5e7eb'}; color: ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'white' : 'black'}">
                                ${msg.message}
                            </div>
                            <div style="font-size: 10px; color: #999; margin-top: 2px;">${msg.created_at}</div>
                        </div>
                    `).join('');
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            }
        });
    }

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('chatInput').value;
        $.ajax({
            url: '../actions/chat_action.php?action=send',
            method: 'POST',
            data: { order_id: currentOrderId, message: message },
            success: function(response) {
                if (response.status === 'success') {
                    document.getElementById('chatInput').value = '';
                    fetchMessages();
                }
            }
        });
    });

    // Rating Functions
    function openRating(orderId, buyerId, buyerName) {
        document.getElementById('ratingOrderId').value = orderId;
        document.getElementById('ratingModal').querySelector('h2').textContent = `Rate ${buyerName}`;
        document.getElementById('ratingModal').style.display = 'block';
    }
    
    function closeRatingModal() {
        document.getElementById('ratingModal').style.display = 'none';
    }

    document.getElementById('ratingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const orderId = document.getElementById('ratingOrderId').value;
        const rating = document.getElementById('ratingValue').value;
        const review = document.getElementById('ratingReview').value;
        
        $.ajax({
            url: '../actions/rating_action.php?action=submit',
            method: 'POST',
            data: { order_id: orderId, rating: rating, review: review },
            success: function(response) {
                if (response.status === 'success') {
                    alert('Rating submitted!');
                    closeRatingModal();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Modal helpers
    function closeModal() {
      document.getElementById('offerModal').style.display = 'none';
      document.body.style.overflow = 'auto';
      document.getElementById('offerForm').reset();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('offerModal');
      const chatModal = document.getElementById('chatModal');
      const ratingModal = document.getElementById('ratingModal');
      if (event.target === modal) {
        closeModal();
      }
      if (event.target === chatModal) {
        closeChatModal();
      }
      if (event.target === ratingModal) {
        closeRatingModal();
      }
    }

  </script>
  <?php include '../includes/footer.php'; ?>
</body>
</html>