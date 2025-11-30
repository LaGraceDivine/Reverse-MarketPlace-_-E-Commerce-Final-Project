// Dispute Chatbot JavaScript
class DisputeChatbot {
    constructor() {
        this.isOpen = false;
        this.currentDisputeId = null;
        this.disputes = [];
        this.init();
    }

    init() {
        this.createChatbotHTML();
        this.attachEventListeners();
        this.loadDisputes();
        this.checkUnreadCount();

        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (this.currentDisputeId) {
                this.loadDisputeMessages(this.currentDisputeId);
            }
            this.checkUnreadCount();
        }, 30000);
    }

    createChatbotHTML() {
        const chatbotHTML = `
            <!-- Dispute Chatbot Widget -->
            <div id="disputeChatbot" class="dispute-chatbot">
                <!-- Floating Button -->
                <button id="chatbotToggle" class="chatbot-toggle">
                    <span class="chatbot-icon">💬</span>
                    <span id="chatbotBadge" class="chatbot-badge" style="display: none;">0</span>
                </button>

                <!-- Chatbot Panel -->
                <div id="chatbotPanel" class="chatbot-panel" style="display: none;">
                    <div class="chatbot-header">
                        <h3>Support & Disputes</h3>
                        <button id="chatbotClose" class="chatbot-close">×</button>
                    </div>

                    <!-- Dispute List View -->
                    <div id="disputeListView" class="chatbot-content">
                        <div class="chatbot-actions">
                            <button id="newDisputeBtn" class="btn-new-dispute">+ New Dispute</button>
                        </div>
                        <div id="disputeList" class="dispute-list">
                            <div class="loading-state">Loading disputes...</div>
                        </div>
                    </div>

                    <!-- New Dispute Form -->
                    <div id="newDisputeView" class="chatbot-content" style="display: none;">
                        <button id="backToListBtn" class="btn-back">← Back</button>
                        <form id="newDisputeForm" class="dispute-form">
                            <div class="form-group">
                                <label>Issue Type</label>
                                <select id="disputeType" required>
                                    <option value="">Select type...</option>
                                    <option value="order_issue">Order Issue</option>
                                    <option value="seller_issue">Seller Issue</option>
                                    <option value="buyer_issue">Buyer Issue</option>
                                    <option value="platform_issue">Platform/App Issue</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" id="disputeSubject" placeholder="Brief description..." required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea id="disputeDescription" placeholder="Explain the issue in detail..." rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Order ID (Optional)</label>
                                <input type="number" id="disputeOrderId" placeholder="Enter order ID if applicable">
                            </div>
                            <button type="submit" class="btn-submit">Submit Dispute</button>
                        </form>
                    </div>

                    <!-- Dispute Chat View -->
                    <div id="disputeChatView" class="chatbot-content" style="display: none;">
                        <button id="backToListFromChat" class="btn-back">← Back</button>
                        <div id="disputeInfo" class="dispute-info"></div>
                        <div id="chatMessages" class="chat-messages"></div>
                        <form id="chatMessageForm" class="chat-input-form">
                            <input type="file" id="messageAttachment" accept="image/*,.pdf" style="display: none;">
                            <button type="button" id="attachFileBtn" class="btn-attach">📎</button>
                            <input type="text" id="messageInput" placeholder="Type your message..." required>
                            <button type="submit" class="btn-send">Send</button>
                        </form>
                        <div id="attachmentPreview" style="display: none; padding: 5px; font-size: 12px; color: #666;"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
    }

    attachEventListeners() {
        // Toggle chatbot
        document.getElementById('chatbotToggle').addEventListener('click', () => this.toggleChatbot());
        document.getElementById('chatbotClose').addEventListener('click', () => this.toggleChatbot());

        // Navigation
        document.getElementById('newDisputeBtn').addEventListener('click', () => this.showNewDisputeForm());
        document.getElementById('backToListBtn').addEventListener('click', () => this.showDisputeList());
        document.getElementById('backToListFromChat').addEventListener('click', () => this.showDisputeList());

        // Forms
        document.getElementById('newDisputeForm').addEventListener('submit', (e) => this.submitNewDispute(e));
        document.getElementById('chatMessageForm').addEventListener('submit', (e) => this.sendMessage(e));

        // File attachment
        document.getElementById('attachFileBtn').addEventListener('click', () => {
            document.getElementById('messageAttachment').click();
        });

        document.getElementById('messageAttachment').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('attachmentPreview').style.display = 'block';
                document.getElementById('attachmentPreview').textContent = `📎 ${file.name}`;
            }
        });
    }

    toggleChatbot() {
        this.isOpen = !this.isOpen;
        const panel = document.getElementById('chatbotPanel');
        panel.style.display = this.isOpen ? 'flex' : 'none';

        if (this.isOpen) {
            this.loadDisputes();
        }
    }

    showDisputeList() {
        document.getElementById('disputeListView').style.display = 'block';
        document.getElementById('newDisputeView').style.display = 'none';
        document.getElementById('disputeChatView').style.display = 'none';
        this.loadDisputes();
    }

    showNewDisputeForm() {
        document.getElementById('disputeListView').style.display = 'none';
        document.getElementById('newDisputeView').style.display = 'block';
        document.getElementById('disputeChatView').style.display = 'none';
    }

    showDisputeChat(disputeId) {
        this.currentDisputeId = disputeId;
        document.getElementById('disputeListView').style.display = 'none';
        document.getElementById('newDisputeView').style.display = 'none';
        document.getElementById('disputeChatView').style.display = 'block';
        this.loadDisputeMessages(disputeId);
    }

    async loadDisputes() {
        try {
            const response = await fetch('../actions/dispute_action.php?action=get_user_disputes');
            const data = await response.json();

            if (data.status === 'success') {
                this.disputes = data.data;
                this.renderDisputeList();
            }
        } catch (error) {
            console.error('Error loading disputes:', error);
        }
    }

    renderDisputeList() {
        const container = document.getElementById('disputeList');

        if (this.disputes.length === 0) {
            container.innerHTML = `
                <div class="empty-disputes">
                    <p>No disputes yet</p>
                    <small>Click "New Dispute" to report an issue</small>
                </div>
            `;
            return;
        }

        container.innerHTML = this.disputes.map(dispute => `
            <div class="dispute-item" onclick="disputeChatbot.showDisputeChat(${dispute.id})">
                <div class="dispute-item-header">
                    <strong>${this.escapeHtml(dispute.subject)}</strong>
                    <span class="status-badge status-${dispute.status}">${dispute.status.replace('_', ' ')}</span>
                </div>
                <div class="dispute-item-meta">
                    <span class="dispute-type">${this.formatDisputeType(dispute.dispute_type)}</span>
                    <span class="dispute-date">${this.formatDate(dispute.created_at)}</span>
                </div>
                ${dispute.message_count > 0 ? `<div class="message-count">${dispute.message_count} messages</div>` : ''}
            </div>
        `).join('');
    }

    async submitNewDispute(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('dispute_type', document.getElementById('disputeType').value);
        formData.append('subject', document.getElementById('disputeSubject').value);
        formData.append('description', document.getElementById('disputeDescription').value);

        const orderId = document.getElementById('disputeOrderId').value;
        if (orderId) {
            formData.append('order_id', orderId);
        }

        try {
            const response = await fetch('../actions/dispute_action.php?action=create', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'success') {
                alert('Dispute created successfully! Our support team will respond soon.');
                document.getElementById('newDisputeForm').reset();
                this.showDisputeList();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error creating dispute:', error);
            alert('Failed to create dispute. Please try again.');
        }
    }

    async loadDisputeMessages(disputeId) {
        try {
            const response = await fetch(`../actions/dispute_action.php?action=get_dispute_details&id=${disputeId}`);
            const data = await response.json();

            if (data.status === 'success') {
                this.renderDisputeInfo(data.data.dispute);
                this.renderMessages(data.data.messages);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    renderDisputeInfo(dispute) {
        const container = document.getElementById('disputeInfo');
        container.innerHTML = `
            <div class="dispute-header-info">
                <h4>${this.escapeHtml(dispute.subject)}</h4>
                <span class="status-badge status-${dispute.status}">${dispute.status.replace('_', ' ')}</span>
            </div>
            <p class="dispute-meta-info">
                ${this.formatDisputeType(dispute.dispute_type)} • Created ${this.formatDate(dispute.created_at)}
            </p>
        `;
    }

    renderMessages(messages) {
        const container = document.getElementById('chatMessages');

        if (messages.length === 0) {
            container.innerHTML = '<div class="no-messages">No messages yet. Our support team will respond soon.</div>';
            return;
        }

        container.innerHTML = messages.map(msg => `
            <div class="message ${msg.sender_type === 'admin' ? 'message-admin' : 'message-user'}">
                <div class="message-sender">${this.escapeHtml(msg.sender_name)}</div>
                <div class="message-content">${this.escapeHtml(msg.message)}</div>
                ${msg.attachment ? `<div class="message-attachment">
                    <a href="../uploads/disputes/${msg.attachment}" target="_blank">📎 View Attachment</a>
                </div>` : ''}
                <div class="message-time">${this.formatDate(msg.created_at)}</div>
            </div>
        `).join('');

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }

    async sendMessage(e) {
        e.preventDefault();

        const message = document.getElementById('messageInput').value;
        const fileInput = document.getElementById('messageAttachment');

        if (!message.trim() && !fileInput.files[0]) {
            return;
        }

        const formData = new FormData();
        formData.append('dispute_id', this.currentDisputeId);
        formData.append('message', message);

        if (fileInput.files[0]) {
            formData.append('attachment', fileInput.files[0]);
        }

        try {
            const response = await fetch('../actions/dispute_action.php?action=send_message', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'success') {
                document.getElementById('messageInput').value = '';
                fileInput.value = '';
                document.getElementById('attachmentPreview').style.display = 'none';
                this.loadDisputeMessages(this.currentDisputeId);
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
        }
    }

    async checkUnreadCount() {
        try {
            const response = await fetch('../actions/dispute_action.php?action=get_unread_count');
            const data = await response.json();

            if (data.status === 'success' && data.count > 0) {
                const badge = document.getElementById('chatbotBadge');
                badge.textContent = data.count;
                badge.style.display = 'block';
            } else {
                document.getElementById('chatbotBadge').style.display = 'none';
            }
        } catch (error) {
            console.error('Error checking unread count:', error);
        }
    }

    formatDisputeType(type) {
        const types = {
            'order_issue': '📦 Order Issue',
            'seller_issue': '🏪 Seller Issue',
            'buyer_issue': '👤 Buyer Issue',
            'platform_issue': '⚙️ Platform Issue'
        };
        return types[type] || type;
    }

    formatDate(dateString) {
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

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize chatbot when DOM is ready
let disputeChatbot;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        disputeChatbot = new DisputeChatbot();
    });
} else {
    disputeChatbot = new DisputeChatbot();
}
