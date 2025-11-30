<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 3) {
    header("Location: login.php");
    exit;
}

// Set is_admin flag for API compatibility
$_SESSION['is_admin'] = true;

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dispute Management - Admin Panel</title>
  <link rel="stylesheet" href="../css/dispute_chatbot.css">
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
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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

    .back-btn {
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

    .back-btn:hover {
      background: #e5e7eb;
    }

    /* Container */
    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 50px;
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      border: 2px solid #f3f4f6;
      transition: all 0.3s;
    }

    .stat-card:hover {
      border-color: #3b82f6;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }

    .stat-label {
      font-size: 13px;
      color: #666;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-value {
      font-size: 32px;
      font-weight: 700;
      color: #1a1a1a;
    }

    .stat-card.open .stat-value { color: #3b82f6; }
    .stat-card.in-progress .stat-value { color: #f59e0b; }
    .stat-card.resolved .stat-value { color: #10b981; }
    .stat-card.high-priority .stat-value { color: #ef4444; }

    /* Filters */
    .filters {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .filter-group {
      flex: 1;
      min-width: 200px;
    }

    .filter-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #666;
      margin-bottom: 6px;
    }

    .filter-group select,
    .filter-group input {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.2s;
    }

    .filter-group select:focus,
    .filter-group input:focus {
      outline: none;
      border-color: #3b82f6;
    }

    /* Disputes List */
    .disputes-list {
      background: white;
      border-radius: 12px;
      overflow: hidden;
    }

    .dispute-row {
      padding: 20px;
      border-bottom: 1px solid #f3f4f6;
      cursor: pointer;
      transition: background 0.2s;
    }

    .dispute-row:hover {
      background: #f9fafb;
    }

    .dispute-row:last-child {
      border-bottom: none;
    }

    .dispute-row-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 10px;
    }

    .dispute-title {
      font-size: 16px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 5px;
    }

    .dispute-meta {
      display: flex;
      gap: 15px;
      font-size: 13px;
      color: #666;
      flex-wrap: wrap;
    }

    .dispute-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .priority-badge {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
    }

    .priority-low {
      background: #f3f4f6;
      color: #6b7280;
    }

    .priority-medium {
      background: #fef3c7;
      color: #92400e;
    }

    .priority-high {
      background: #fee2e2;
      color: #991b1b;
    }

    /* Dispute Detail Modal */
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
    }

    .modal-content {
      background-color: white;
      margin: 3% auto;
      padding: 0;
      border-radius: 16px;
      width: 90%;
      max-width: 900px;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-header {
      padding: 25px 30px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .modal-header h2 {
      font-size: 22px;
      color: #1a1a1a;
      margin-bottom: 10px;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 28px;
      color: #999;
      cursor: pointer;
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.2s;
    }

    .modal-close:hover {
      background: #f3f4f6;
      color: #333;
    }

    .modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 25px 30px;
    }

    .dispute-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
      padding: 20px;
      background: #f9fafb;
      border-radius: 10px;
    }

    .info-item {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .info-label {
      font-size: 12px;
      color: #666;
      font-weight: 600;
      text-transform: uppercase;
    }

    .info-value {
      font-size: 14px;
      color: #1a1a1a;
      font-weight: 500;
    }

    .messages-section {
      margin-bottom: 25px;
    }

    .messages-section h3 {
      font-size: 16px;
      margin-bottom: 15px;
      color: #1a1a1a;
    }

    .message-thread {
      max-height: 400px;
      overflow-y: auto;
      padding: 15px;
      background: #f9fafb;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .message {
      margin-bottom: 15px;
      max-width: 85%;
    }

    .message-user {
      margin-left: auto;
    }

    .message-admin {
      margin-right: auto;
    }

    .message-sender {
      font-size: 11px;
      font-weight: 600;
      margin-bottom: 4px;
      color: #666;
    }

    .message-content {
      background: white;
      padding: 10px 12px;
      border-radius: 12px;
      font-size: 14px;
      line-height: 1.4;
    }

    .message-admin .message-content {
      background: #3b82f6;
      color: white;
    }

    .message-time {
      font-size: 10px;
      color: #999;
      margin-top: 4px;
    }

    .reply-form {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .reply-form textarea {
      flex: 1;
      padding: 12px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      resize: vertical;
      min-height: 80px;
    }

    .reply-form textarea:focus {
      outline: none;
      border-color: #3b82f6;
    }

    .reply-form button {
      padding: 12px 24px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      align-self: flex-end;
    }

    .reply-form button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .actions-section {
      display: flex;
      gap: 15px;
      padding-top: 20px;
      border-top: 1px solid #e5e7eb;
    }

    .action-group {
      flex: 1;
    }

    .action-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #666;
      margin-bottom: 8px;
    }

    .action-group select {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .empty-state-icon {
      font-size: 64px;
      margin-bottom: 15px;
      opacity: 0.5;
    }

    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }

      .header {
        padding: 20px;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .filters {
        flex-direction: column;
      }

      .modal-content {
        width: 95%;
        margin: 5% auto;
      }

      .dispute-info-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="header-left">
      <div class="logo-icon">⚖️</div>
      <div class="header-text">
        <h1>Dispute Management</h1>
        <p>Admin Panel - <?php echo htmlspecialchars($username); ?></p>
      </div>
    </div>
    <a href="dashboard.php" class="back-btn">
      <span>←</span>
      Back to Dashboard
    </a>
  </div>

  <!-- Main Content -->
  <div class="container">
    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card open">
        <div class="stat-label">Open</div>
        <div class="stat-value" id="statOpen">0</div>
      </div>
      <div class="stat-card in-progress">
        <div class="stat-label">In Progress</div>
        <div class="stat-value" id="statInProgress">0</div>
      </div>
      <div class="stat-card resolved">
        <div class="stat-label">Resolved</div>
        <div class="stat-value" id="statResolved">0</div>
      </div>
      <div class="stat-card high-priority">
        <div class="stat-label">High Priority</div>
        <div class="stat-value" id="statHighPriority">0</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters">
      <div class="filter-group">
        <label>Status</label>
        <select id="statusFilter">
          <option value="">All Statuses</option>
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Type</label>
        <select id="typeFilter">
          <option value="">All Types</option>
          <option value="order_issue">Order Issue</option>
          <option value="seller_issue">Seller Issue</option>
          <option value="buyer_issue">Buyer Issue</option>
          <option value="platform_issue">Platform Issue</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Search</label>
        <input type="text" id="searchInput" placeholder="Search by subject or user...">
      </div>
    </div>

    <!-- Disputes List -->
    <div id="disputesList" class="disputes-list">
      <div class="empty-state">
        <div class="empty-state-icon">⚖️</div>
        <p>Loading disputes...</p>
      </div>
    </div>
  </div>

  <!-- Dispute Detail Modal -->
  <div id="disputeModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h2 id="modalDisputeTitle">Dispute Details</h2>
          <div id="modalDisputeMeta" style="font-size: 13px; color: #666; margin-top: 5px;"></div>
        </div>
        <button class="modal-close" onclick="closeDisputeModal()">×</button>
      </div>
      <div class="modal-body">
        <div id="disputeInfoGrid" class="dispute-info-grid"></div>
        
        <div class="messages-section">
          <h3>Conversation</h3>
          <div id="messageThread" class="message-thread"></div>
          
          <form id="replyForm" class="reply-form">
            <textarea id="replyMessage" placeholder="Type your response..." required></textarea>
            <button type="submit">Send Reply</button>
          </form>
        </div>

        <div class="actions-section">
          <div class="action-group">
            <label>Update Status</label>
            <select id="updateStatus">
              <option value="open">Open</option>
              <option value="in_progress">In Progress</option>
              <option value="resolved">Resolved</option>
              <option value="closed">Closed</option>
            </select>
          </div>
          <div class="action-group">
            <label>Update Priority</label>
            <select id="updatePriority">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let currentDisputeId = null;
    let allDisputes = [];

    // Load stats and disputes on page load
    $(document).ready(function() {
      loadStats();
      loadDisputes();
      
      // Auto-refresh every 30 seconds
      setInterval(() => {
        loadStats();
        loadDisputes();
        if (currentDisputeId) {
          loadDisputeDetails(currentDisputeId);
        }
      }, 30000);
    });

    // Load statistics
    function loadStats() {
      $.ajax({
        url: '../actions/dispute_action.php?action=get_stats',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success' && response.data) {
            $('#statOpen').text(response.data.open || 0);
            $('#statInProgress').text(response.data.in_progress || 0);
            $('#statResolved').text(response.data.resolved || 0);
            $('#statHighPriority').text(response.data.high_priority || 0);
          } else {
            console.error('Stats error:', response);
          }
        },
        error: function(xhr, status, error) {
          console.error('Stats AJAX error:', error, xhr.responseText);
        }
      });
    }

    // Load all disputes
    function loadDisputes() {
      const status = $('#statusFilter').val();
      const url = status ? `../actions/dispute_action.php?action=get_all&status=${status}` : '../actions/dispute_action.php?action=get_all';
      
      $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            allDisputes = response.data || [];
            filterAndDisplayDisputes();
          } else {
            console.error('Error loading disputes:', response.message);
            $('#disputesList').html(`
              <div class="empty-state">
                <div class="empty-state-icon">⚠️</div>
                <p>Error loading disputes: ${response.message || 'Unknown error'}</p>
              </div>
            `);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX error:', status, error);
          console.error('Response:', xhr.responseText);
          $('#disputesList').html(`
            <div class="empty-state">
              <div class="empty-state-icon">⚠️</div>
              <p>Failed to load disputes. Please try again.</p>
            </div>
          `);
        }
      });
    }

    // Filter and display disputes
    function filterAndDisplayDisputes() {
      const typeFilter = $('#typeFilter').val();
      const searchTerm = $('#searchInput').val().toLowerCase();
      
      let filtered = allDisputes.filter(dispute => {
        const matchesType = !typeFilter || dispute.dispute_type === typeFilter;
        const username = dispute.username || 'Unknown User';
        const subject = dispute.subject || '';
        const matchesSearch = !searchTerm || 
          subject.toLowerCase().includes(searchTerm) ||
          username.toLowerCase().includes(searchTerm);
        return matchesType && matchesSearch;
      });

      displayDisputes(filtered);
    }

    // Display disputes
    function displayDisputes(disputes) {
      const container = $('#disputesList');
      
      if (disputes.length === 0) {
        container.html(`
          <div class="empty-state">
            <div class="empty-state-icon">⚖️</div>
            <p>No disputes found</p>
          </div>
        `);
        return;
      }

      container.html(disputes.map(dispute => `
        <div class="dispute-row" onclick="openDisputeModal(${dispute.id})">
          <div class="dispute-row-header">
            <div style="flex: 1;">
              <div class="dispute-title">${escapeHtml(dispute.subject || 'No Subject')}</div>
              <div class="dispute-meta">
                <span>👤 ${escapeHtml(dispute.username || 'Unknown User')}</span>
                <span>${formatDisputeType(dispute.dispute_type)}</span>
                <span>📅 ${formatDate(dispute.created_at)}</span>
                <span>💬 ${dispute.message_count || 0} messages</span>
              </div>
            </div>
            <div style="display: flex; gap: 8px; align-items: flex-start;">
              <span class="status-badge status-${dispute.status}">${(dispute.status || 'open').replace('_', ' ')}</span>
              <span class="priority-badge priority-${dispute.priority}">${dispute.priority || 'medium'}</span>
            </div>
          </div>
        </div>
      `).join(''));
    }

    // Open dispute modal
    function openDisputeModal(disputeId) {
      currentDisputeId = disputeId;
      loadDisputeDetails(disputeId);
      $('#disputeModal').show();
    }

    // Close dispute modal
    function closeDisputeModal() {
      $('#disputeModal').hide();
      currentDisputeId = null;
    }

    // Load dispute details
    function loadDisputeDetails(disputeId) {
      $.ajax({
        url: `../actions/dispute_action.php?action=get_dispute_details&id=${disputeId}`,
        method: 'GET',
        success: function(response) {
          if (response.status === 'success') {
            displayDisputeDetails(response.data);
          }
        }
      });
    }

    // Display dispute details
    function displayDisputeDetails(data) {
      const dispute = data.dispute;
      const messages = data.messages;

      $('#modalDisputeTitle').text(dispute.subject);
      $('#modalDisputeMeta').html(`
        Dispute #${dispute.id} • Created ${formatDate(dispute.created_at)}
      `);

      $('#disputeInfoGrid').html(`
        <div class="info-item">
          <div class="info-label">User</div>
          <div class="info-value">${escapeHtml(dispute.username)}</div>
        </div>
        <div class="info-item">
          <div class="info-label">User Type</div>
          <div class="info-value">${dispute.user_type}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Dispute Type</div>
          <div class="info-value">${formatDisputeType(dispute.dispute_type)}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Status</div>
          <div class="info-value">
            <span class="status-badge status-${dispute.status}">${dispute.status.replace('_', ' ')}</span>
          </div>
        </div>
        <div class="info-item">
          <div class="info-label">Priority</div>
          <div class="info-value">
            <span class="priority-badge priority-${dispute.priority}">${dispute.priority}</span>
          </div>
        </div>
        ${dispute.order_id ? `
        <div class="info-item">
          <div class="info-label">Order ID</div>
          <div class="info-value">#${dispute.order_id}</div>
        </div>
        ` : ''}
      `);

      // Display messages
      if (messages.length === 0) {
        $('#messageThread').html('<div style="text-align: center; color: #999; padding: 20px;">No messages yet</div>');
      } else {
        $('#messageThread').html(messages.map(msg => `
          <div class="message ${msg.sender_type === 'admin' ? 'message-admin' : 'message-user'}">
            <div class="message-sender">${escapeHtml(msg.sender_name)}</div>
            <div class="message-content">${escapeHtml(msg.message)}</div>
            ${msg.attachment ? `<div style="margin-top: 6px; font-size: 12px;">
              <a href="../uploads/disputes/${msg.attachment}" target="_blank" style="color: inherit;">📎 View Attachment</a>
            </div>` : ''}
            <div class="message-time">${formatDate(msg.created_at)}</div>
          </div>
        `).join(''));
      }

      // Scroll to bottom
      $('#messageThread').scrollTop($('#messageThread')[0].scrollHeight);

      // Set current status and priority
      $('#updateStatus').val(dispute.status);
      $('#updatePriority').val(dispute.priority);
    }

    // Send reply
    $('#replyForm').submit(function(e) {
      e.preventDefault();
      
      const message = $('#replyMessage').val();
      if (!message.trim()) return;

      const formData = new FormData();
      formData.append('dispute_id', currentDisputeId);
      formData.append('message', message);

      $.ajax({
        url: '../actions/dispute_action.php?action=send_message',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status === 'success') {
            $('#replyMessage').val('');
            loadDisputeDetails(currentDisputeId);
          } else {
            alert('Error: ' + response.message);
          }
        }
      });
    });

    // Update status
    $('#updateStatus').change(function() {
      const status = $(this).val();
      
      $.ajax({
        url: '../actions/dispute_action.php?action=update_status',
        method: 'POST',
        data: {
          dispute_id: currentDisputeId,
          status: status
        },
        success: function(response) {
          if (response.status === 'success') {
            loadStats();
            loadDisputes();
            loadDisputeDetails(currentDisputeId);
          } else {
            alert('Error: ' + response.message);
          }
        }
      });
    });

    // Update priority
    $('#updatePriority').change(function() {
      const priority = $(this).val();
      
      $.ajax({
        url: '../actions/dispute_action.php?action=update_priority',
        method: 'POST',
        data: {
          dispute_id: currentDisputeId,
          priority: priority
        },
        success: function(response) {
          if (response.status === 'success') {
            loadStats();
            loadDisputes();
            loadDisputeDetails(currentDisputeId);
          } else {
            alert('Error: ' + response.message);
          }
        }
      });
    });

    // Filter events
    $('#statusFilter, #typeFilter').change(function() {
      loadDisputes();
    });

    $('#searchInput').on('input', function() {
      filterAndDisplayDisputes();
    });

    // Close modal on outside click
    window.onclick = function(event) {
      const modal = document.getElementById('disputeModal');
      if (event.target === modal) {
        closeDisputeModal();
      }
    }

    // Utility functions
    function formatDisputeType(type) {
      const types = {
        'order_issue': '📦 Order Issue',
        'seller_issue': '🏪 Seller Issue',
        'buyer_issue': '👤 Buyer Issue',
        'platform_issue': '⚙️ Platform Issue'
      };
      return types[type] || type;
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diff = now - date;
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(diff / 3600000);
      const days = Math.floor(diff / 86400000);

      if (minutes < 1) return 'Just now';
      if (minutes < 60) return `${minutes}m ago`;
      if (hours < 24) return `${hours}h ago`;
      if (days < 7) return `${days}d ago`;
      return date.toLocaleDateString();
    }

    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
  </script>

</body>
</html>
