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
  <title>Buyer Dashboard - Reverse Marketplace</title>
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
      text-decoration: none;
    }

    .tab:hover {
      color: #3b82f6;
    }

    .tab.active {
      color: #3b82f6;
      border-bottom-color: #3b82f6;
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
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
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

    .post-request-btn {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      padding: 14px 28px;
      border-radius: 10px;
      border: none;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }

    .post-request-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(168, 85, 247, 0.4);
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
      margin-bottom: 30px;
    }

    .empty-state-btn {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      padding: 14px 28px;
      border-radius: 10px;
      border: none;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }

    .empty-state-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(66, 120, 200, 0.4);
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
      background: #d1fae5;
      color: #059669;
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
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: #666;
    }

    .budget-box {
      background: #f8f9fb;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
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

    .waiting-text {
      text-align: center;
      padding: 30px;
      font-size: 16px;
      color: #666;
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
      margin-bottom: 30px;
      color: #1a1a1a;
      font-size: 28px;
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
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
      outline: none;
      border-color: #4c7ed0ff;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
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
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
    }

    .modal-btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(74, 134, 203, 0.4);
    }

    @media (max-width: 768px) {
      .header, .tabs, .container {
        padding-left: 20px;
        padding-right: 20px;
      }

      .page-header {
        flex-direction: column;
        gap: 20px;
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
    <div class="tab active" id="requestsTab">
      <span class="tab-icon">💬</span>
      <span>My Requests (<span id="requestCount">0</span>)</span>
    </div>
    <div class="tab" id="ordersTab">
      <span class="tab-icon">📦</span>
      <span>My Orders (0)</span>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <!-- Payment Status Messages -->
    <?php if (isset($_GET['payment'])): ?>
      <div class="status-badge" style="display: block; margin-bottom: 20px; padding: 15px; border-radius: 10px; <?php echo $_GET['payment'] === 'success' ? 'background: #d1fae5; color: #059669;' : 'background: #fee2e2; color: #dc2626;'; ?>">
        <?php if ($_GET['payment'] === 'success'): ?>
          ✅ Payment successful! Your order has been confirmed.
        <?php else: ?>
          ❌ Payment failed. Please try again or contact support.
        <?php endif; ?>
      </div>
    <?php endif; ?>
    
    <!-- My Requests View -->
    <div id="requestsView">
      <div class="page-header">
        <div class="page-title">
          <h2>My Requests</h2>
          <p>Post what you need and review offers from sellers</p>
        </div>
        <button class="post-request-btn" onclick="openModal()">
          <span>+</span>
          Post Request
        </button>
      </div>

      <div id="requestsContainer">
        <!-- Empty state will be shown initially -->
        <div class="empty-state">
          <div class="empty-icon">💬</div>
          <h3>No requests yet</h3>
          <p>Start by posting what you're looking for</p>
          <button class="empty-state-btn" onclick="openModal()">
            <span>+</span>
            Post Your First Request
          </button>
        </div>
      </div>
    </div>

    <!-- My Orders View -->
    <div id="ordersView" style="display: none;">
      <div class="page-header">
        <div class="page-title">
          <h2>My Orders</h2>
          <p>Track your accepted offers and deliveries</p>
        </div>
      </div>

      <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>No orders yet</h3>
        <p>Accept an offer to create your first order</p>
      </div>
    </div>
  </div>

  <!-- Post Request Modal -->
  <div id="requestModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Post a Request</h2>
      
      <form id="requestForm">
        <div class="form-group">
          <label>What are you looking for?</label>
          <input type="text" id="requestTitle" placeholder="e.g., Black dress, size M" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea id="requestDescription" placeholder="Provide more details about what you need..." required></textarea>
        </div>

        <div class="form-group">
          <label>Category</label>
          <select id="requestCategory" required>
            <option value="">Select a Category</option>
            <option value="Fashion">Fashion</option>
            <option value="Electronics">Electronics</option>
            <option value="Home & Garden">Home & Garden</option>
            <option value="Sports">Sports</option>
            <option value="Books">Books</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Maximum Budget (GH₵)</label>
          <input type="number" id="requestBudget" placeholder="e.g. 200" min="1" step="0.01" required>
        </div>

  <!-- Image Upload in Request Modal -->
        <div class="form-group">
          <label>Upload Image (Optional)</label>
          <input type="file" id="requestImage" accept="image/*">
        </div>

        <div class="modal-buttons">
          <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="submit" class="modal-btn modal-btn-submit">Post Request</button>
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
      <h2>Rate Seller</h2>
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

  <!-- Offers Modal -->
  <div id="offersModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
      <span class="close" onclick="closeOffersModal()">&times;</span>
      <h2>Offers for <span id="offersRequestTitle"></span></h2>
      <div id="offersContainer">
        <!-- Offers will be loaded here -->
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Global variables
    let currentOrderId = 0;

    // Tab switching
    document.getElementById('requestsTab').addEventListener('click', function() {
      document.getElementById('requestsTab').classList.add('active');
      document.getElementById('ordersTab').classList.remove('active');
      document.getElementById('requestsView').style.display = 'block';
      document.getElementById('ordersView').style.display = 'none';
      fetchRequests();
    });

    document.getElementById('ordersTab').addEventListener('click', function() {
      document.getElementById('ordersTab').classList.add('active');
      document.getElementById('requestsTab').classList.remove('active');
      document.getElementById('ordersView').style.display = 'block';
      document.getElementById('requestsView').style.display = 'none';
      fetchOrders();
    });

    // Fetch Requests
    function fetchRequests() {
        $.ajax({
            url: '../actions/request_action.php?action=fetch_buyer',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    displayRequests(response.data);
                }
            }
        });
    }

    function displayRequests(requests) {
        console.log("Requests Data:", requests); // Debugging
        const container = document.getElementById('requestsContainer');
        document.getElementById('requestCount').textContent = requests.length;

        if (requests.length === 0) {
            container.innerHTML = `
              <div class="empty-state">
                <div class="empty-icon">💬</div>
                <h3>No requests yet</h3>
                <p>Start by posting what you're looking for</p>
                <button class="empty-state-btn" onclick="openModal()">
                  <span>+</span>
                  Post Your First Request
                </button>
              </div>
            `;
            return;
        }

        container.innerHTML = requests.map(request => `
            <div class="request-card">
              <div class="request-header">
                <div class="request-title">
                  <h3>${request.title}</h3>
                  <span class="status-badge">${request.status}</span>
                </div>
                ${request.offer_count > 0 ? `<button class="modal-btn modal-btn-submit" style="padding: 5px 15px; font-size: 14px;" onclick="viewOffers(${request.id}, '${request.title}')">View ${request.offer_count} Offers</button>` : ''}
              </div>
              
              <p class="request-description">${request.description}</p>
              
              ${request.image ? `<div style="margin-bottom: 20px;"><img src="../uploads/requests/${request.image}" style="max-width: 200px; border-radius: 10px; border: 1px solid #eee;"></div>` : ''}

              <div class="request-meta">
                <div class="meta-item">
                  <span>🏷️</span>
                  <span>${request.category}</span>
                </div>
                <div class="meta-item">
                  <span>💰</span>
                  <span>Budget: GH₵ ${request.max_budget}</span>
                </div>
                ${request.lowest_offer ? `
                <div class="meta-item" style="color: #059669; font-weight: bold;">
                  <span>⬇️</span>
                  <span>Lowest Offer: GH₵ ${request.lowest_offer}</span>
                </div>` : ''}
              </div>
            </div>
        `).join('');
    }

    // Post Request
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('title', document.getElementById('requestTitle').value);
        formData.append('description', document.getElementById('requestDescription').value);
        formData.append('category', document.getElementById('requestCategory').value);
        formData.append('max_budget', document.getElementById('requestBudget').value);
        
        const imageFile = document.getElementById('requestImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: '../actions/request_action.php?action=create',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    closeModal();
                    fetchRequests();
                    alert('Request posted successfully!');
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // View Offers
    function viewOffers(requestId, title) {
        document.getElementById('offersRequestTitle').textContent = title;
        document.getElementById('offersModal').style.display = 'block';
        window.currentOffersRequestId = requestId;
        
        $.ajax({
            url: `../actions/request_action.php?action=get_details&id=${requestId}`,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data.offers) {
                    displayOffers(response.data.offers);
                }
            }
        });
    }

    function displayOffers(offers) {
        const container = document.getElementById('offersContainer');
        if (offers.length === 0) {
            container.innerHTML = '<p style="text-align:center; color:#666; padding: 20px;">No offers yet.</p>';
            return;
        }

        container.innerHTML = offers.map(offer => `
            <div class="offer-item" style="border: 1px solid #eee; padding: 20px; border-radius: 12px; margin-bottom: 15px; background: #f9f9f9;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px;">
                    <div>
                        <h4 style="margin:0 0 5px 0; font-size: 18px; color: #333;">GH₵ ${offer.price}</h4>
                        <span style="font-size:13px; color:#666; background: #e5e7eb; padding: 4px 8px; border-radius: 4px;">${offer.delivery_time}</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                      <button class="modal-btn modal-btn-submit" style="padding: 8px 20px;" onclick="console.log('Accept clicked for offer:', ${offer.id}); acceptOffer(${offer.id})">Accept Offer</button>
                      <button class="modal-btn modal-btn-cancel" style="padding: 8px 20px;" onclick="console.log('Decline clicked for offer:', ${offer.id}); declineOffer(${offer.id})">Decline</button>
                    </div>
                </div>
                
                <div style="margin-bottom:15px;">
                    <p style="color:#444; line-height: 1.5;">${offer.message}</p>
                </div>

                ${(offer.brand || offer.image) ? `
                <div style="border-top: 1px solid #eee; padding-top: 15px; display: flex; gap: 20px; align-items: center;">
                    ${offer.brand ? `
                    <div>
                        <span style="font-size: 12px; color: #666; display: block; margin-bottom: 2px;">Brand</span>
                        <span style="font-weight: 600; color: #333;">${offer.brand}</span>
                    </div>` : ''}
                    
                    ${offer.image ? `
                    <div>
                        <span style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">Product Image</span>
                        <img src="../${offer.image}" style="max-width:100px; max-height: 100px; border-radius:8px; border: 1px solid #eee;">
                    </div>` : ''}
                </div>` : ''}
            </div>
        `).join('');
    }

    function acceptOffer(offerId) {
        if (!confirm('Are you sure you want to accept this offer?')) return;
        console.log('Accepting offer ID:', offerId);
        
        $.ajax({
            url: '../actions/offer_action.php?action=accept',
            method: 'POST',
            data: { offer_id: offerId },
            dataType: 'json',
            success: function(response) {
                console.log('Accept response:', response);
                console.log('Accept response status:', response.status);
                console.log('Accept response message:', response.message);
                if (response.status === 'success') {
                    // Close offers modal and refresh requests list
                    closeOffersModal();
                    fetchRequests(); // Refresh the main requests list
                    
                    // Switch to My Orders tab and refresh orders
                    document.getElementById('requestsTab').classList.remove('active');
                    document.getElementById('ordersTab').classList.add('active');
                    document.getElementById('requestsView').style.display = 'none';
                    document.getElementById('ordersView').style.display = 'block';
                    fetchOrders(); // Refresh orders to show the new order
                    
                    alert('Offer accepted! Order created successfully. You can now proceed with payment.');
                } else {
                    alert(response.message || 'Failed to accept offer');
                }
            },
            error: function(xhr, status, error) {
                console.error('Accept AJAX error:', status, error, xhr.responseText);
                alert('Error accepting offer: ' + error);
            }
        });
    }

    function initializePayment(orderId) {
        console.log('Initializing payment for order:', orderId);
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = 'Processing...';
        btn.disabled = true;

        $.ajax({
            url: `../actions/payment_action.php?action=initialize&order_id=${orderId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Payment init response:', response);
                if (response.status === 'success') {
                    console.log('Redirecting to:', response.authorization_url);
                    window.location.href = response.authorization_url;
                } else {
                    alert('Payment Error: ' + (response.message || 'Unknown error'));
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                console.error('Payment AJAX error:', status, error, xhr.responseText);
                alert('Connection Error: ' + error + '\nCheck console for details.');
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });
    }

    function declineOffer(offerId) {
        if (!confirm('Are you sure you want to decline this offer?')) return;
        console.log('Declining offer ID:', offerId);
        $.ajax({
            url: '../actions/offer_action.php?action=decline',
            method: 'POST',
            data: { offer_id: offerId },
            dataType: 'json',
            success: function(response) {
                console.log('Decline response:', response);
                console.log('Decline response status:', response.status);
                console.log('Decline response message:', response.message);
                if (response.status === 'success') {
                    // Refresh offers for this request and main requests list
                    if (window.currentOffersRequestId) {
                        viewOffers(window.currentOffersRequestId, document.getElementById('offersRequestTitle').textContent);
                    }
                    fetchRequests(); // Always refresh the main requests list
                } else {
                    alert(response.message || 'Failed to decline offer');
                }
            },
            error: function(xhr, status, error) {
                console.error('Decline AJAX error:', status, error, xhr.responseText);
                alert('Error declining offer: ' + error);
            }
        });
    }

    // Fetch Orders
    function fetchOrders() {
        $.ajax({
            url: '../actions/order_action.php?action=fetch_buyer',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    displayOrders(response.data);
                }
            }
        });
    }

    function displayOrders(orders) {
        const container = document.getElementById('ordersView'); // Use ordersView directly or a container inside it
        // Reset to header
        container.innerHTML = `
          <div class="page-header">
            <div class="page-title">
              <h2>My Orders</h2>
              <p>Track your accepted offers and deliveries</p>
            </div>
          </div>
        `;

        if (orders.length === 0) {
            container.innerHTML += `
              <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>No orders yet</h3>
                <p>Accept an offer to create your first order</p>
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
              
              <p>Seller: ${order.seller_name} ${order.company_name ? `(${order.company_name})` : ''}</p>
              <p style="font-size: 13px; color: #666; margin-top: 5px;">Order #${order.order_id} | Invoice: ${order.invoice_no}</p>
              
              <div class="modal-buttons" style="margin-top: 15px;">
                ${order.payment_status === 'pending' ? `
                  <button class="modal-btn modal-btn-submit" onclick="initializePayment(${order.order_id})" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    💳 Pay Now - GH₵ ${order.total_amount}
                  </button>
                ` : ''}
                
                ${order.payment_status === 'paid' && order.delivery_status === 'pending' ? `
                  <button class="modal-btn modal-btn-submit" onclick="openChat(${order.order_id}, '${order.seller_name}')">
                    💬 Chat with Seller
                  </button>
                  <p style="color: #666; font-size: 14px; margin-top: 10px;">⏳ Waiting for seller to ship the order</p>
                ` : ''}
                
                ${order.payment_status === 'paid' && order.delivery_status === 'in_transit' ? `
                  <button class="modal-btn modal-btn-submit" onclick="openChat(${order.order_id}, '${order.seller_name}')">
                    💬 Chat with Seller
                  </button>
                  <button class="modal-btn modal-btn-submit" onclick="markDelivered(${order.order_id})" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    ✓ Confirm Delivery
                  </button>
                ` : ''}
                
                ${order.delivery_status === 'delivered' ? (
                    order.has_rated == 0 ? `
                  <button class="modal-btn modal-btn-submit" onclick="openRating(${order.order_id}, ${order.seller_id}, '${order.seller_name}')" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    ⭐ Rate Seller
                  </button>
                    ` : `
                  <div style="text-align: center; color: #f59e0b; font-weight: 600; padding: 10px; background: #fffbeb; border-radius: 8px; border: 1px solid #fcd34d;">
                    ⭐ You rated this seller
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

    // Chat Functions
    function openChat(orderId, sellerName) {
        currentOrderId = orderId;
        document.getElementById('chatOrderId').value = orderId;
        document.getElementById('chatTitle').textContent = `Chat with ${sellerName}`;
        document.getElementById('chatModal').style.display = 'block';
        fetchMessages();
        // Start polling
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
    function openRating(orderId, sellerId, sellerName) {
        document.getElementById('ratingOrderId').value = orderId;
        window.currentRatedUserId = sellerId; // Store seller ID for rating submission
        document.getElementById('ratingModal').querySelector('h2').textContent = `Rate ${sellerName}`;
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

    function markDelivered(orderId) {
        if (!confirm('Confirm delivery?')) return;
        $.ajax({
            url: '../actions/order_action.php?action=mark_delivered',
            method: 'POST',
            data: { id: orderId },
            success: function(response) {
                if (response.status === 'success') {
                    fetchOrders();
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Modal helpers
    function openModal() { document.getElementById('requestModal').style.display = 'block'; }
    function closeModal() { document.getElementById('requestModal').style.display = 'none'; }
    function closeOffersModal() { document.getElementById('offersModal').style.display = 'none'; }
    
    // Debug: Check if functions exist
    console.log('acceptOffer function exists:', typeof acceptOffer);
    console.log('declineOffer function exists:', typeof declineOffer);
    
    // Initial fetch
    fetchRequests();
    fetchOrders();
  </script>
  <?php include '../includes/footer.php'; ?>
</body>
</html>