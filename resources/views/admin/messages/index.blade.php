@extends('admin.layouts.app')
@section('title', 'Quản lý chat')

@push('page-css')
    <style>
        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info .avatar {
            margin-right: 16px;
        }

        .user-info .avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-details .name {
            font-weight: 600;
            color: #495057;
            font-size: 16px;
        }

        .user-details .status {
            font-size: 14px;
            color: #28a745;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: white;
            border-bottom: 1px solid #e9ecef;
        }

        .chat-actions {
            display: flex;
            gap: 8px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Danh sách khách hàng -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom p-4">
                        <h5 class="mb-0 text-dark fw-bold">Danh sách khách hàng</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul id="userList" class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                            @foreach ($users as $user)
                                <li class="list-group-item d-flex align-items-center py-3 px-4 user-item hover-bg-light"
                                    onclick="openChat({{ $user->id }})" data-user="{{ $user->id }}" style="cursor: pointer;">
                                    <!-- Avatar -->
                                    <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('admins/assets/img/default-avatar.webp') }}"
                                        alt="Avatar" class="rounded-circle me-3"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    <!-- Tên -->
                                    <span class="text-dark font-weight-medium">{{ $user->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Khung chat -->
            <div class="col-lg-8">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="chat-header">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="{{ asset('admins/assets/img/default-avatar.webp') }}" id="header-avatar">
                            </div>
                            <div class="user-details">
                                <div class="name" id="header-name"></div>
                            </div>
                        </div>

                        <div class="chat-actions">
                            <button class="action-btn"><i class="fas fa-search"></i></button>
                            <button class="action-btn"><i class="fas fa-info-circle"></i></button>
                            <button class="action-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column p-4" style="height: 600px; overflow-y: auto;" id="chatBox">
                        <p class="text-muted text-center my-auto">Chọn khách hàng để bắt đầu chat</p>
                    </div>
                    <div class="card-footer bg-light border-top">
                        <div class="input-group">
                            <input type="text" id="adminMessageInput" class="form-control" placeholder="Nhập tin nhắn..."
                                aria-label="Nhập tin nhắn">
                            <button class="btn btn-primary" id="sendAdminMessage">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentUserId = null;

        document.addEventListener('DOMContentLoaded', () => {
            const firstUser = document.querySelector('#userList .user-item');
            if (firstUser) {
                const firstUserId = firstUser.getAttribute('onclick').match(/\d+/)[0];
                openChat(firstUserId);
                firstUser.classList.add('bg-light'); 
            }
        });


        function openChat(userId) {
            currentUserId = userId;
            loadMessages();
            // Highlight active user
            document.querySelectorAll('#userList li').forEach(li => li.classList.remove('bg-light'));
            event.currentTarget.classList.add('bg-light');
        }

        function loadMessages() {
            if (!currentUserId) return;
            fetch(`/admin/messages/${currentUserId}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    const avatar = data.user && data.user.avatar ?
                        `/storage/${data.user.avatar}` :
                        '{{ asset('admins/assets/img/default-avatar.webp') }}';
                    const adminAvatar =
                        '{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('admins/assets/img/default-avatar.webp') }}';
                    data.messages.forEach(msg => {
                        if (msg.from_id == {{ auth()->id() }}) {
                            // Tin nhắn của Admin
                            html += `
        <div class="d-flex justify-content-end align-items-end mb-3">
            <div class="message-bubble admin">
                ${msg.message}
            </div>
            <img src="${adminAvatar}" alt="Admin Avatar" 
                 class="chat-avatar ms-2">
        </div>`;
                        } else {
                            // Tin nhắn của User
                            html += `
        <div class="d-flex justify-content-start align-items-end mb-3">
            <img src="${avatar}" alt="User Avatar" 
                 class="chat-avatar me-2">
            <div class="message-bubble user">
                ${msg.message}
            </div>
        </div>`;
                        }
                    });
                    document.getElementById('chatBox').innerHTML = html;
                    document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;
                    document.getElementById('header-avatar').src = avatar;
                    document.getElementById('header-name').textContent = data.user.name;
                });
        }

        document.getElementById('sendAdminMessage').addEventListener('click', () => {
            let msg = document.getElementById('adminMessageInput').value;
            if (!msg.trim() || !currentUserId) return;

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
                .then(res => res.json())
                .then(() => {
                    document.getElementById('adminMessageInput').value = '';
                    loadMessages();
                });
        });

        // Handle Enter key to send message
        document.getElementById('adminMessageInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                document.getElementById('sendAdminMessage').click();
            }
        });

        setInterval(loadMessages, 2000);
    </script>

    <style>
        .user-item:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .card {
            border-radius: 0.5rem;
        }

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .card-footer {
            border-radius: 0 0 0.5rem 0.5rem;
        }

        /* Avatar trong chat */
        .chat-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        /* Bong bóng chat */
        .message-bubble {
            padding: 10px 14px;
            border-radius: 18px;
            max-width: 65%;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.4;
        }

        .message-bubble.admin {
            background-color: #0d6efd;
            color: #fff;
            border-bottom-right-radius: 4px;
            /* Góc nhọn */
        }

        .message-bubble.user {
            background-color: #f1f1f1;
            color: #333;
            border-bottom-left-radius: 4px;
            /* Góc nhọn */
        }

        /* Khung chat */
        #chatBox {
            background: #fafafa;
            border-radius: 12px;
            padding: 15px;
        }
    </style>
@endsection
