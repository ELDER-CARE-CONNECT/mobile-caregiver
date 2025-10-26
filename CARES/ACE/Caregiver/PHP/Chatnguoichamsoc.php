<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Chat với người chăm sóc - CareGiver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../CSS/style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="logo">
      <i class="fas fa-heart-pulse"></i>
      <h2>CareGiver</h2>
    </div>
    
    <nav class="menu">
      <a href="#" class="menu-item">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-user-plus"></i>
        <span>Thêm hồ sơ</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-users"></i>
        <span>Hồ sơ bệnh nhân</span>
      </a>
      
      <a href="Chitietdonhang.php" class="menu-item">
        <i class="fas fa-clipboard-list"></i>
        <span>Lịch sử đặt dịch vụ</span>
        <span class="badge">5</span>
      </a>
      
      <a href="#" class="menu-item active">
        <i class="fas fa-comments"></i>
        <span>Chat với người chăm sóc</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-calendar-check"></i>
        <span>Lịch hẹn</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-chart-line"></i>
        <span>Báo cáo</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-bell"></i>
        <span>Thông báo</span>
        <span class="badge notification">99+</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-cog"></i>
        <span>Cài đặt</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <header class="header">
      <div class="header-left">
        <h1>Chat với người chăm sóc</h1>
        <p>Liên lạc trực tiếp với người chăm sóc của bạn</p>
      </div>
      <div class="header-right">
        <div class="user-profile">
          <img src="https://via.placeholder.com/40" alt="Avatar" />
          <span>Nguyễn Minh Thư</span>
        </div>
      </div>
    </header>

    <!-- Chat Container -->
    <div class="chat-container">
      <!-- Chat Header -->
      <div class="chat-header">
        <div class="chat-user-info">
          <img src="https://via.placeholder.com/50" alt="Caregiver Avatar" class="caregiver-avatar" />
          <div class="user-details">
            <h3>Trần Lan Vy</h3>
            <p class="status online">
              <i class="fas fa-circle"></i>
              Đang hoạt động
            </p>
          </div>
        </div>
        <div class="chat-actions">
          <button class="btn-action" title="Gọi video">
            <i class="fas fa-video"></i>
          </button>
          <button class="btn-action" title="Gọi thoại">
            <i class="fas fa-phone"></i>
          </button>
          <button class="btn-action" title="Thông tin">
            <i class="fas fa-info-circle"></i>
          </button>
        </div>
      </div>

      <!-- Chat Messages -->
      <div class="chat-messages" id="chatMessages">
        <!-- Sample messages -->
        <div class="message received">
          <div class="message-avatar">
            <img src="https://via.placeholder.com/35" alt="Avatar" />
          </div>
          <div class="message-content">
            <div class="message-bubble">
              <p>Xin chào! Tôi là Case Giver, người chăm sóc của bạn. Tôi sẽ đến đúng giờ hẹn vào 9h sáng mai.</p>
            </div>
            <div class="message-time">09:30</div>
          </div>
        </div>

        <div class="message sent">
          <div class="message-content">
            <div class="message-bubble">
              <p>Cảm ơn bạn! Tôi đã chuẩn bị sẵn mọi thứ rồi.</p>
            </div>
            <div class="message-time">09:32</div>
          </div>
        </div>

        <div class="message received">
          <div class="message-avatar">
            <img src="https://via.placeholder.com/35" alt="Avatar" />
          </div>
          <div class="message-content">
            <div class="message-bubble">
              <p>Tuyệt vời! Tôi sẽ mang theo các dụng cụ cần thiết cho việc chăm sóc. Có điều gì đặc biệt cần lưu ý không?</p>
            </div>
            <div class="message-time">09:33</div>
          </div>
        </div>

        <div class="message sent">
          <div class="message-content">
            <div class="message-bubble">
              <p>Bà tôi cần uống thuốc vào 10h sáng, bạn nhớ nhắc nhé!</p>
            </div>
            <div class="message-time">09:35</div>
          </div>
        </div>
      </div>

      <!-- Chat Input Area -->
      <div class="chat-input-container">
        <!-- Collapsible Input Bar -->
        <div class="input-bar" id="inputBar">
          <div class="input-bar-content">
            <i class="fas fa-comment-dots"></i>
            <span>Nhấn để nhắn tin...</span>
            <i class="fas fa-chevron-up" id="expandIcon"></i>
          </div>
        </div>

        <!-- Expanded Input Area -->
        <div class="chat-input-expanded" id="chatInputExpanded">
          <div class="input-tools">
            <button class="input-tool" title="Gửi ảnh">
              <i class="fas fa-image"></i>
            </button>
            <button class="input-tool" title="Gửi file">
              <i class="fas fa-paperclip"></i>
            </button>
            <button class="input-tool" title="Gửi vị trí">
              <i class="fas fa-map-marker-alt"></i>
            </button>
            <button class="input-tool" title="Ghi âm">
              <i class="fas fa-microphone"></i>
            </button>
          </div>
          
          <div class="input-area">
            <textarea 
              id="messageInput" 
              placeholder="Nhập tin nhắn của bạn..." 
              rows="3"
              maxlength="1000"
            ></textarea>
            <div class="input-actions">
              <div class="char-count">
                <span id="charCount">0</span>/1000
              </div>
              <button class="btn-send" id="sendBtn">
                <i class="fas fa-paper-plane"></i>
                Gửi
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions Modal -->
  <div id="quickActionsModal" class="modal">
    <div class="modal-content quick-actions-modal">
      <div class="modal-header">
        <h2>Hành động nhanh</h2>
        <button class="close-btn" id="closeQuickActions">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="quick-actions-grid">
          <button class="quick-action-btn" data-action="schedule">
            <i class="fas fa-calendar-plus"></i>
            <span>Đặt lịch hẹn</span>
          </button>
          <button class="quick-action-btn" data-action="emergency">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Khẩn cấp</span>
          </button>
          <button class="quick-action-btn" data-action="report">
            <i class="fas fa-chart-line"></i>
            <span>Báo cáo tình hình</span>
          </button>
          <button class="quick-action-btn" data-action="feedback">
            <i class="fas fa-star"></i>
            <span>Đánh giá dịch vụ</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="../JS/app.js"></script>
  <script>
    // Chat functionality
    class ChatInterface {
      constructor() {
        this.inputBar = document.getElementById('inputBar');
        this.chatInputExpanded = document.getElementById('chatInputExpanded');
        this.expandIcon = document.getElementById('expandIcon');
        this.messageInput = document.getElementById('messageInput');
        this.sendBtn = document.getElementById('sendBtn');
        this.charCount = document.getElementById('charCount');
        this.chatMessages = document.getElementById('chatMessages');
        this.isExpanded = false;
        
        this.initializeEventListeners();
      }

      initializeEventListeners() {
        // Toggle input area
        this.inputBar.addEventListener('click', () => {
          this.toggleInputArea();
        });

        // Send message
        this.sendBtn.addEventListener('click', () => {
          this.sendMessage();
        });

        // Send on Enter (but not Shift+Enter)
        this.messageInput.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.sendMessage();
          }
        });

        // Character count
        this.messageInput.addEventListener('input', () => {
          this.updateCharCount();
        });

        // Auto-resize textarea
        this.messageInput.addEventListener('input', () => {
          this.autoResizeTextarea();
        });
      }

      toggleInputArea() {
        this.isExpanded = !this.isExpanded;
        
        if (this.isExpanded) {
          this.chatInputExpanded.style.display = 'block';
          this.expandIcon.style.transform = 'rotate(180deg)';
          this.messageInput.focus();
          
          // Smooth animation
          setTimeout(() => {
            this.chatInputExpanded.style.opacity = '1';
            this.chatInputExpanded.style.transform = 'translateY(0)';
          }, 10);
        } else {
          this.chatInputExpanded.style.opacity = '0';
          this.chatInputExpanded.style.transform = 'translateY(20px)';
          
          setTimeout(() => {
            this.chatInputExpanded.style.display = 'none';
          }, 300);
        }
      }

      sendMessage() {
        const message = this.messageInput.value.trim();
        
        if (message) {
          this.addMessage(message, 'sent');
          this.messageInput.value = '';
          this.updateCharCount();
          this.autoResizeTextarea();
          
          // Simulate received message after 2 seconds
          setTimeout(() => {
            this.simulateReceivedMessage();
          }, 2000);
        }
      }

      addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('vi-VN', { 
          hour: '2-digit', 
          minute: '2-digit' 
        });
        
        if (type === 'received') {
          messageDiv.innerHTML = `
            <div class="message-avatar">
              <img src="https://via.placeholder.com/35" alt="Avatar" />
            </div>
            <div class="message-content">
              <div class="message-bubble">
                <p>${content}</p>
              </div>
              <div class="message-time">${timeString}</div>
            </div>
          `;
        } else {
          messageDiv.innerHTML = `
            <div class="message-content">
              <div class="message-bubble">
                <p>${content}</p>
              </div>
              <div class="message-time">${timeString}</div>
            </div>
          `;
        }
        
        this.chatMessages.appendChild(messageDiv);
        this.scrollToBottom();
      }

      simulateReceivedMessage() {
        const responses = [
          "Tôi hiểu rồi, cảm ơn bạn đã thông báo!",
          "Được rồi, tôi sẽ ghi nhớ điều này.",
          "Tuyệt vời! Tôi sẽ thực hiện ngay.",
          "Cảm ơn bạn! Có gì cần hỗ trợ thêm không?",
          "Tôi đã nhận được thông tin. Sẽ cập nhật cho bạn sau."
        ];
        
        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
        this.addMessage(randomResponse, 'received');
      }

      updateCharCount() {
        const count = this.messageInput.value.length;
        this.charCount.textContent = count;
        
        if (count > 800) {
          this.charCount.style.color = '#ef4444';
        } else if (count > 600) {
          this.charCount.style.color = '#f59e0b';
        } else {
          this.charCount.style.color = '#64748b';
        }
      }

      autoResizeTextarea() {
        this.messageInput.style.height = 'auto';
        this.messageInput.style.height = Math.min(this.messageInput.scrollHeight, 120) + 'px';
      }

      scrollToBottom() {
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
      }
    }

    // Initialize chat when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
      new ChatInterface();
    });
  </script>
</body>
</html>
