@extends('admin.layouts.app')
@section('title', 'Quản lý chat')

@push('page-css')
    <style>
        /* Reset và base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .chat-container {
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
            padding: 30px 0;
        }

        /* User List Styles */
        .user-list-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            height: 700px;
            display: flex;
            flex-direction: column;
            border: 1px solid #e8ecef;
        }

        .user-list-header {
            background: white;
            padding: 25px;
            border-bottom: 2px solid #f1f3f5;
        }

        .user-list-header h5 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
        }

        .user-list-header h5::before {
            content: '\f086';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #3498db;
        }

        .search-user {
            margin-top: 15px;
            position: relative;
        }

        .search-user input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 2px solid #e8ecef;
            border-radius: 25px;
            background: #f8f9fa;
            color: #2c3e50;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-user input:focus {
            outline: none;
            border-color: #3498db;
            background: white;
        }

        .search-user input::placeholder {
            color: #95a5a6;
        }

        .search-user i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }

        #userList {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background: white;
        }

        #userList::-webkit-scrollbar {
            width: 6px;
        }

        #userList::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #userList::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        #userList::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            background: white;
            border: 1px solid transparent;
        }

        .user-item:hover {
            background: #f8f9fa;
            border-color: #e2e8f0;
            transform: translateX(5px);
        }

        .user-item.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .user-item.active .user-name {
            color: white;
        }

        .user-item.active .user-status {
            color: rgba(255, 255, 255, 0.9);
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 3px solid #f1f3f5;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .user-item.active .user-avatar {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .user-info-list {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            font-size: 15px;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .user-status {
            font-size: 12px;
            color: #95a5a6;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #27ae60;
            box-shadow: 0 0 0 2px rgba(39, 174, 96, 0.2);
        }

        /* Chat Box Styles */
        .chat-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            height: 700px;
            display: flex;
            flex-direction: column;
            border: 1px solid #e8ecef;
        }

        .chat-header {
            background: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f3f5;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info .avatar img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f1f3f5;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .user-details .name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 17px;
            margin-bottom: 2px;
        }

        .user-details .status {
            font-size: 13px;
            color: #95a5a6;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .chat-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #3498db;
            color: white;
            border-color: #3498db;
            transform: scale(1.1);
        }

        /* Chat Messages Area */
        #chatBox {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
            background: #fafbfc;
        }

        #chatBox::-webkit-scrollbar {
            width: 6px;
        }

        #chatBox::-webkit-scrollbar-track {
            background: transparent;
        }

        #chatBox::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        #chatBox::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #95a5a6;
        }

        .empty-chat i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.3;
            color: #cbd5e0;
        }

        .empty-chat p {
            color: #95a5a6;
            font-size: 16px;
        }

        /* Message Bubbles */
        .message-wrapper {
            display: flex;
            margin-bottom: 20px;
            opacity: 0;
            animation: fadeIn 0.3s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .message-bubble {
            padding: 12px 18px;
            border-radius: 20px;
            max-width: 65%;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.5;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .message-bubble.admin {
            background: #3498db;
            color: white;
            border-bottom-right-radius: 5px;
            margin-left: 10px;
        }

        .message-bubble.user {
            background: white;
            color: #2c3e50;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 5px;
            margin-right: 10px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
        }

        /* Chat Input Area */
        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 2px solid #f1f3f5;
        }

        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .attach-btn {
            background: transparent;
            border: none;
            color: #95a5a6;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 8px;
        }

        .attach-btn:hover {
            color: #3498db;
            transform: scale(1.1);
        }

        #adminMessageInput {
            flex: 1;
            border: 2px solid #e8ecef;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        #adminMessageInput:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        #sendAdminMessage {
            background: #3498db;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #sendAdminMessage:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        #sendAdminMessage:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
        }

        /* Typing indicator */
        .typing-indicator {
            display: inline-flex;
            align-items: center;
            padding: 12px 18px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            margin-left: 46px;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background: #95a5a6;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.5;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chat-container {
                padding: 15px 0;
            }
            
            .user-list-card,
            .chat-card {
                height: 500px;
                margin-bottom: 20px;
            }

            .message-bubble {
                max-width: 80%;
            }
        }

        /* Hover effects */
        .user-item,
        .action-btn,
        .attach-btn,
        #sendAdminMessage {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
@endpush

@section('content')
    <div class="chat-container">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Danh sách khách hàng -->
                <div class="col-lg-4">
                    <div class="user-list-card">
                        <div class="user-list-header">
                            <h5>Tin nhắn</h5>
                            <div class="search-user">
                                <input type="text" placeholder="Tìm kiếm người dùng..." id="searchUserInput">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <ul id="userList" class="list-unstyled">
                            @foreach ($users as $user)
                                <li class="user-item" onclick="openChat({{ $user->id }})" data-user="{{ $user->id }}">
                                    <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('admins/assets/img/default-avatar.webp') }}"
                                        alt="Avatar" class="user-avatar" onerror="this.src='{{ asset('admins/assets/img/default-avatar.webp') }}'">
                                    <div class="user-info-list">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-status">
                                            <span class="status-dot"></span>
                                            <span>Đang hoạt động</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Khung chat -->
                <div class="col-lg-8">
                    <div class="chat-card">
                        <div class="chat-header">
                            <div class="user-info">
                                <div class="avatar">
                                    <img src="{{ asset('admins/assets/img/default-avatar.webp') }}" id="header-avatar" 
                                         onerror="this.src='{{ asset('admins/assets/img/default-avatar.webp') }}'">
                                </div>
                                <div class="user-details">
                                    <div class="name" id="header-name">Chọn người dùng</div>
                                    <div class="status">
                                        <span class="status-dot"></span>
                                        Đang hoạt động
                                    </div>
                                </div>
                            </div>
                            <div class="chat-actions">
                                <button class="action-btn"><i class="fas fa-phone"></i></button>
                                <button class="action-btn"><i class="fas fa-video"></i></button>
                                <button class="action-btn"><i class="fas fa-ellipsis-v"></i></button>
                            </div>
                        </div>
                        
                        <div id="chatBox">
                            <div class="empty-chat">
                                <i class="fas fa-comments"></i>
                                <p>Chọn một cuộc trò chuyện để bắt đầu</p>
                            </div>
                        </div>
                        
                        <div class="chat-input-area">
                            <div class="input-group">
                                <button class="attach-btn"><i class="fas fa-paperclip"></i></button>
                                <button class="attach-btn"><i class="fas fa-image"></i></button>
                                <input type="text" id="adminMessageInput" placeholder="Nhập tin nhắn...">
                                <button id="sendAdminMessage">
                                    <i class="fas fa-paper-plane"></i>
                                    Gửi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentUserId = null;
        let lastMessageCount = 0;
        let isScrolledToBottom = true;

        document.addEventListener('DOMContentLoaded', () => {
            const firstUser = document.querySelector('#userList .user-item');
            if (firstUser) {
                const firstUserId = firstUser.getAttribute('onclick').match(/\d+/)[0];
                openChat(firstUserId);
                firstUser.classList.add('active'); 
            }

            // Search functionality
            document.getElementById('searchUserInput').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.user-item').forEach(item => {
                    const userName = item.querySelector('.user-name').textContent.toLowerCase();
                    item.style.display = userName.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        });

        // Track scroll position
        document.getElementById('chatBox').addEventListener('scroll', function() {
            const chatBox = document.getElementById('chatBox');
            isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 1;
        });

        function openChat(userId) {
            currentUserId = userId;
            lastMessageCount = 0; // Reset message count
            loadMessages(true); // Force reload all messages
            
            // Highlight active user
            document.querySelectorAll('#userList li').forEach(li => li.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }

        function loadMessages(forceReload = false) {
            if (!currentUserId) return;
            
            fetch(`/admin/messages/${currentUserId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    // Only update if messages count changed or forced reload
                    if (forceReload || data.messages.length !== lastMessageCount) {
                        lastMessageCount = data.messages.length;
                        renderMessages(data);
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                });
        }

        function renderMessages(data) {
            let html = '';
            const avatar = data.user && data.user.avatar ?
                `{{ Storage::url('') }}${data.user.avatar}` :
                '{{ asset('admins/assets/img/default-avatar.webp') }}';
            const adminAvatar = '{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('admins/assets/img/default-avatar.webp') }}';
            
            data.messages.forEach(msg => {
                const messageTime = new Date(msg.created_at).toLocaleTimeString('vi-VN', {
                    hour: '2-digit', 
                    minute:'2-digit'
                });
                
                if (msg.from_id == {{ auth()->id() }}) {
                    // Admin message
                    html += `
                    <div class="message-wrapper d-flex justify-content-end align-items-end">
                        <div class="message-bubble admin">
                            ${msg.message}
                            <div class="message-time">${messageTime}</div>
                        </div>
                        <img src="${adminAvatar}" alt="Admin Avatar" class="chat-avatar ms-2"
                             onerror="this.src='{{ asset('admins/assets/img/default-avatar.webp') }}'">
                    </div>`;
                } else {
                    // User message
                    html += `
                    <div class="message-wrapper d-flex justify-content-start align-items-end">
                        <img src="${avatar}" alt="User Avatar" class="chat-avatar me-2"
                             onerror="this.src='{{ asset('admins/assets/img/default-avatar.webp') }}'">
                        <div class="message-bubble user">
                            ${msg.message}
                            <div class="message-time">${messageTime}</div>
                        </div>
                    </div>`;
                }
            });
            
            document.getElementById('chatBox').innerHTML = html;
            
            // Only scroll to bottom if user was already at bottom
            if (isScrolledToBottom) {
                document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;
            }
            
            // Update header
            document.getElementById('header-avatar').src = avatar;
            document.getElementById('header-name').textContent = data.user.name;
        }

        document.getElementById('sendAdminMessage').addEventListener('click', () => {
            let msg = document.getElementById('adminMessageInput').value;
            if (!msg.trim() || !currentUserId) return;

            // Disable button while sending
            document.getElementById('sendAdminMessage').disabled = true;

            fetch('/admin/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    to_id: currentUserId,
                    message: msg
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(() => {
                document.getElementById('adminMessageInput').value = '';
                loadMessages(true);
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn');
            })
            .finally(() => {
                document.getElementById('sendAdminMessage').disabled = false;
            });
        });

        // Handle Enter key to send message
        document.getElementById('adminMessageInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('sendAdminMessage').click();
            }
        });

        // Only refresh if there are new messages
        setInterval(() => loadMessages(false), 2000);
    </script>
@endsection