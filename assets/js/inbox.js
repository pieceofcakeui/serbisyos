let currentChat = null;
let currentChatUserId = null;
let lastMessageId = 0;
let processedMessageIds = new Set();
let currentImageUrl = '';
let statusInterval = null;
let currentMessageIdForReaction = null;

function hideReactionPicker() {
    const picker = document.getElementById('reactionPicker');
    const backdrop = document.getElementById('reactionPickerBackdrop');
    picker.style.display = 'none';
    backdrop.style.display = 'none';
    document.removeEventListener('click', hideReactionPickerOnClickOutside, true);
}

function hideReactionPickerOnClickOutside(event) {
    const picker = document.getElementById('reactionPicker');
    const clickedOption = event.target.closest('.reaction-option');
    if (!picker.contains(event.target) || clickedOption) {
        hideReactionPicker();
    }
}

function showReactionPicker(messageId) {
    if (!messageId) return;
    currentMessageIdForReaction = messageId;
    const picker = document.getElementById('reactionPicker');
    const backdrop = document.getElementById('reactionPickerBackdrop');
    picker.style.display = 'flex';
    backdrop.style.display = 'block';
    setTimeout(() => {
        document.addEventListener('click', hideReactionPickerOnClickOutside, true);
    }, 0);
}

function addReaction(reactionType) {
    if (!currentMessageIdForReaction) return;
    const formData = new FormData();
    formData.append('message_id', currentMessageIdForReaction);
    formData.append('reaction_type', reactionType);
    fetch('inbox-backend/update_reaction.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateReactionOnBubble(currentMessageIdForReaction, reactionType);
        } else {
            showError(data.message || 'Failed to add reaction');
        }
    })
    .catch(error => {
        showError('Failed to add reaction');
    });
    hideReactionPicker();
}

function filterConversations() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const messageList = document.getElementById('messageList');
    const messageItems = messageList.querySelectorAll('.message-item');
    const originalPlaceholder = messageList.querySelector('.no-messages-placeholder:not(.no-results-placeholder *)');
    let noResultsEl = messageList.querySelector('.no-results-placeholder');
    if (!noResultsEl) {
        noResultsEl = document.createElement('div');
        noResultsEl.className = 'no-results-placeholder';
        noResultsEl.style.display = 'none';
        noResultsEl.innerHTML = `
            <div class="no-messages-placeholder">
                <i class="fas fa-search"></i>
                <h2>No Messages or Conversation Found</h2>
                <p>Your search did not match any conversations.</p>
            </div>
        `;
        messageList.appendChild(noResultsEl);
    }
    let hasVisibleItems = false;
    const isSearching = searchTerm.length > 0;
    messageItems.forEach(item => {
        const name = item.getAttribute('data-search-name') || '';
        const message = item.getAttribute('data-search-message') || '';
        const isMatch = name.includes(searchTerm) || message.includes(searchTerm);
        if (isSearching) {
            if (isMatch) {
                item.style.display = 'flex';
                hasVisibleItems = true;
            } else {
                item.style.display = 'none';
            }
        } else {
            item.style.display = 'flex';
        }
    });
    if (isSearching) {
        if (originalPlaceholder) {
            originalPlaceholder.style.display = 'none';
        }
        noResultsEl.style.display = hasVisibleItems ? 'none' : 'block';
    } else {
        noResultsEl.style.display = 'none';
        if (originalPlaceholder) {
            originalPlaceholder.style.display = messageItems.length > 0 ? 'none' : 'block';
        }
    }
}

function viewImageFullScreen(imageUrl) {
    currentImageUrl = imageUrl;
    const viewer = document.getElementById('imageViewer');
    const img = document.getElementById('fullScreenImage');
    img.src = imageUrl;
    viewer.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageViewer() {
    document.getElementById('imageViewer').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function downloadImage() {
    if (!currentImageUrl) return;
    const link = document.createElement('a');
    link.href = currentImageUrl;
    link.download = `image-${Date.now()}.jpg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function toggleSettingsMenu() {
    const menu = document.getElementById('chatSettingsMenu');
    menu.classList.toggle('show');
}

function closeSettingsMenu() {
    document.getElementById('chatSettingsMenu').classList.remove('show');
}

function showDeleteConversationModal() {
    const modal = new bootstrap.Modal(document.getElementById('deleteConversationModal'));
    modal.show();
    closeSettingsMenu();
}

function deleteConversation() {
    if (!currentChatUserId) return;
    const formData = new FormData();
    formData.append('other_id', currentChatUserId);
    fetch('inbox-backend/delete_conversation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                closeChat();
                const item = document.querySelector(`.message-item[data-user-id="${currentChatUserId}"]`);
                if (item) item.remove();
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConversationModal'));
                modal.hide();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showError('Failed to delete conversation: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            showError('Failed to delete conversation');
        });
}

function formatRelativeTime(timestamp) {
    const now = Math.floor(Date.now() / 1000);
    const diff = now - timestamp;
    let statusText, statusClass, indicatorClass;
    if (diff < 60) {
        statusText = 'Active now';
        statusClass = 'active-status';
        indicatorClass = 'active';
    } else if (diff < 300) {
        const minutes = Math.floor(diff / 60);
        statusText = `Active ${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
        statusClass = 'active-status';
        indicatorClass = 'active';
    } else if (diff < 86400) {
        const hours = Math.floor(diff / 3600);
        statusText = `Active ${hours} hour${hours !== 1 ? 's' : ''} ago`;
        statusClass = 'idle-status';
        indicatorClass = 'idle';
    } else if (diff < 2592000) {
        const days = Math.floor(diff / 86400);
        statusText = `Active ${days} day${days !== 1 ? 's' : ''} ago`;
        statusClass = 'idle-status';
        indicatorClass = 'idle';
    } else {
        statusText = 'Offline';
        statusClass = 'offline-status';
        indicatorClass = 'offline';
    }
    return {
        text: `<span class="status-indicator ${indicatorClass}"></span><span class="status-text">${statusText}</span>`,
        class: statusClass
    };
}

function updateUserStatus(userId) {
    fetch(`inbox-backend/check_activity.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            const statusElement = document.getElementById('chatUserStatus');
            if (!statusElement) return;
            switch(data.status) {
                case 'online':
                    statusElement.innerHTML = `
                        <span class="status-indicator active"></span>
                        <span class="status-text">Active now</span>
                    `;
                    statusElement.className = 'active-status';
                    break;
                case 'idle':
                    statusElement.innerHTML = `
                        <span class="status-indicator idle"></span>
                        <span class="status-text">Active ${data.minutes} min ago</span>
                    `;
                    statusElement.className = 'idle-status';
                    break;
                case 'offline':
                    statusElement.innerHTML = `
                        <span class="status-indicator offline"></span>
                        <span class="status-text">Offline</span>
                    `;
                    statusElement.className = 'offline-status';
                    break;
                default:
                    console.error('Unknown status:', data.status);
            }
        })
        .catch(error => {
            console.error('Error checking user status:', error);
        });
}

function startStatusPolling(userId) {
    updateUserStatus(userId);
    return setInterval(() => updateUserStatus(userId), 15000);
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

function openChat(element) {
    let userId, name, displayName, profilePicture, profileType;

    if (element && element.dataset) {
        userId = parseInt(element.dataset.userId, 10);
        name = element.dataset.name;
        displayName = element.dataset.displayName;
        profilePicture = element.dataset.profilePicture;
        profileType = element.dataset.profileType;
    } else {
        showError("Failed to open chat: Invalid user data.");
        return;
    }

    if (isNaN(userId)) {
        showError("Failed to open chat: Invalid User ID.");
        return;
    }

    const chatPanel = document.getElementById('chatPanel');
    const inboxContainer = document.getElementById('inboxContainer');
    const chatAvatarImg = document.getElementById('chatAvatarImg');
    const chatUserName = document.getElementById('chatUserName');

    const formData = new FormData();
    formData.append('sender_id', userId);
    fetch('inbox-backend/mark_as_read.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                element.classList.remove('unread');
                const unreadBadge = element.querySelector('.unread-badge');
                if (unreadBadge) unreadBadge.remove();
                updateUnreadCount();
            }
        });

    if (statusInterval) clearInterval(statusInterval);
    statusInterval = startStatusPolling(userId);

    currentChatUserId = userId;
    currentChat = true;
    processedMessageIds.clear();
    lastMessageId = 0;

    document.querySelectorAll('.message-item').forEach(item => item.classList.remove('active'));
    element.classList.add('active');

    if (chatAvatarImg) {
        const isShop = profileType === 'owner' || displayName !== name;

        const defaultImage = isShop ?
            'uploads/shop_logo/logo.jpg' :
            'assets/img/profile/profile-user.png';

        const hasValidProfilePicture = profilePicture &&
            profilePicture !== '' &&
            profilePicture !== 'profile-user.png' &&
            profilePicture !== 'logo.jpg' &&
            profilePicture !== 'assets/img/profile/profile-user.png' &&
            profilePicture !== 'uploads/shop_logo/logo.jpg';

        if (hasValidProfilePicture) {
            chatAvatarImg.src = profilePicture;
            chatAvatarImg.style.display = 'block';
            chatAvatarImg.alt = displayName;

            chatAvatarImg.onerror = function() {
                this.src = defaultImage;
                this.onerror = null;
            };
        } else {
            chatAvatarImg.src = defaultImage;
            chatAvatarImg.style.display = 'block';
            chatAvatarImg.alt = displayName;
            chatAvatarImg.onerror = null;
        }
    }

    if (chatUserName) {
        chatUserName.textContent = displayName;
    }

    inboxContainer.classList.remove('full-width');
    chatPanel.classList.remove('hidden');

    if (window.innerWidth <= 768) {
        inboxContainer.classList.add('chat-open');
        chatPanel.classList.add('open');
    }

    loadMessages(userId);

    setTimeout(() => {
        const chatInput = document.getElementById('chatInput');
        if (chatInput) {
            chatInput.focus();
        }
    }, 300);
}

function renderMessage(msg) {
    if (processedMessageIds.has(msg.id)) return null;
    processedMessageIds.add(msg.id);
    lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
    
    const messageType = msg.is_sender ? 'sent' : 'received';
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${messageType}`;
    bubble.setAttribute('data-message-id', msg.id);
    bubble.setAttribute('onclick', `showReactionPicker(${msg.id})`);
    
    let attachmentContent = '';
    if (msg.attachment_url && msg.is_image) {
        attachmentContent = `
            <div class="image-content">
                <img src="${msg.attachment_url}" 
                     alt="Shared image" 
                     style="max-width: 200px; max-height: 200px; border-radius: 8px; cursor: pointer;"
                     onerror="this.style.display='none';">
            </div>`;
    }
    
    let reactionContent = '';
    if (msg.reactions && msg.reactions.length > 0) {
        reactionContent = '<div class="reaction-container">';
        msg.reactions.forEach(reaction => {
            reactionContent += `<div class="reaction" data-user-id="${reaction.user_id}">
                ${getReactionEmoji(reaction.type)}
            </div>`;
        });
        reactionContent += '</div>';
    }
    
    bubble.innerHTML = `
        ${attachmentContent}
        <div class="text-content">${msg.message}</div>
        ${reactionContent}
        <div class="time">${msg.created_at}</div>
    `;
    
    const img = bubble.querySelector('img');
    if (img) {
        img.addEventListener('click', (event) => {
            event.stopPropagation();
            viewImageFullScreen(img.src);
        });
    }
    
    return bubble;
}

function getDefaultProfilePicture(profileType, isShop) {
    if (profileType === 'owner' || isShop) {
        return 'uploads/shop_logo/logo.jpg';
    } else {
        return 'assets/img/profile/profile-user.png';
    }
}

function isValidProfilePicture(profilePicture) {
    if (!profilePicture || profilePicture === '') return false;
    
    const invalidPictures = [
        'profile-user.png',
        'logo.jpg',
        'assets/img/profile/profile-user.png',
        'uploads/shop_logo/logo.jpg'
    ];
    
    return !invalidPictures.some(invalid => profilePicture.includes(invalid));
}

function updateReactionOnBubble(messageId, reactionType) {
    const bubble = document.querySelector(`.message-bubble[data-message-id="${messageId}"]`);
    if (!bubble) return;
    let reactionContainer = bubble.querySelector('.reaction-container');
    if (reactionType) {
        const newEmoji = getReactionEmoji(reactionType);
        if (reactionContainer) {
            reactionContainer.querySelector('.reaction').innerHTML = newEmoji;
        } else {
            reactionContainer = document.createElement('div');
            reactionContainer.className = 'reaction-container';
            reactionContainer.innerHTML = `<div class="reaction">${newEmoji}</div>`;
            bubble.appendChild(reactionContainer);
        }
    } else {
        if (reactionContainer) {
            reactionContainer.remove();
        }
    }
}



function loadMessages(userId) {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '<div class="loading">Loading messages...</div>';
    document.querySelectorAll('[data-temp-id]').forEach(bubble => bubble.remove());
    const formData = new FormData();
    formData.append('other_id', userId);
    formData.append('last_message_id', 0);
    fetch('inbox-backend/fetch_messages.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            chatMessages.innerHTML = '';
            processedMessageIds.clear();
            if (data.error) {
                chatMessages.innerHTML = `<div class="error">Error: ${data.error}</div>`;
                return;
            }
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const bubble = renderMessage(msg);
                    if (bubble) chatMessages.appendChild(bubble);
                });
                scrollToBottom();
            } else {
                chatMessages.innerHTML = '<div class="no-messages">No messages yet</div>';
            }
        })
        .catch(error => {
            chatMessages.innerHTML = '<div class="error">Failed to load messages</div>';
        });
}

function sendMessage() {
    if (!currentChatUserId) {
        showError('No chat user selected');
        return;
    }
    const input = document.getElementById('chatInput');
    const messageText = input.value.trim();
    if (!messageText) {
        return;
    }
    const clientMessageId = 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const tempBubble = appendMessageBubble(messageText, 'sent');
    tempBubble.setAttribute('data-temp-id', clientMessageId);
    tempBubble.classList.add('sending');
    input.value = '';
    autoResize(input);
    input.focus();
    scrollToBottom();
    const formData = new FormData();
    formData.append('receiver_id', currentChatUserId);
    formData.append('message', messageText);
    formData.append('client_message_id', clientMessageId);
    fetch('inbox-backend/all_messages_send.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            tempBubble.remove();
            if (data.status === 'success') {
                const actualBubble = renderMessage({
                    id: data.message_id,
                    message: messageText,
                    created_at: data.created_at,
                    is_sender: true,
                    reaction: null,
                    attachment_url: null,
                    is_image: false
                });
                if (actualBubble) {
                    document.getElementById('chatMessages').appendChild(actualBubble);
                }
                scrollToBottom();
            } else {
                showError('Failed to send message: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            tempBubble.remove();
            showError('Failed to send message');
        });
}

function appendMessageBubble(content, type, time, senderName = '', messageId = null) {
    const chatMessages = document.getElementById('chatMessages');
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${type}`;
    if (messageId) {
        bubble.setAttribute('data-message-id', messageId);
        bubble.setAttribute('onclick', `showReactionPicker(${messageId})`);
    }
    const timeStr = time || new Date().toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    bubble.innerHTML = `
        <div class="text-content">${content}</div>
        <div class="time">${timeStr}</div>
    `;
    chatMessages.appendChild(bubble);
    return bubble;
}

function appendImageMessage(imageUrl, type, time, senderName = '', messageId = null) {
    const chatMessages = document.getElementById('chatMessages');
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${type}`;
    if (messageId) {
        bubble.setAttribute('data-message-id', messageId);
        bubble.setAttribute('onclick', `showReactionPicker(${messageId})`);
    }
    const timeStr = time || new Date().toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    bubble.innerHTML = `
        <div class="image-content">
            <img src="${imageUrl}" alt="Shared image" onclick="event.stopPropagation(); viewImageFullScreen('${imageUrl}')" style="max-width: 200px; max-height: 200px; border-radius: 8px; cursor: pointer;">
        </div>
        <div class="time">${timeStr}</div>
    `;
    chatMessages.appendChild(bubble);
    return bubble;
}

function updateUnreadCount() {
    fetch('inbox-backend/get_unread_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const unreadElements = document.querySelectorAll('.unread-count');
                unreadElements.forEach(el => {
                    el.textContent = data.count > 0 ? data.count : '';
                    el.style.display = data.count > 0 ? 'block' : 'none';
                });
            }
        });
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    setTimeout(() => {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 50);
}

function handleKeyDown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    scrollToBottom();
}

function closeChat() {
    const chatPanel = document.getElementById('chatPanel');
    const inboxContainer = document.getElementById('inboxContainer');
    if (statusInterval) {
        clearInterval(statusInterval);
        statusInterval = null;
    }
    currentChat = null;
    currentChatUserId = null;
    processedMessageIds.clear();
    lastMessageId = 0;
    chatPanel.classList.add('hidden');
    if (window.innerWidth > 768) {
        inboxContainer.classList.add('full-width');
    } else {
        inboxContainer.classList.remove('chat-open');
        chatPanel.classList.remove('open');
    }
    document.querySelectorAll('.message-item').forEach(item => item.classList.remove('active'));
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file || !currentChatUserId) return;
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        showError('Please select a valid image file');
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        showError('Image too large. Maximum size is 10MB.');
        return;
    }
    const clientMessageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const formData = new FormData();
    formData.append('attachment', file);
    formData.append('receiver_id', currentChatUserId);
    formData.append('client_message_id', clientMessageId);
    const loadingBubble = document.createElement('div');
    loadingBubble.className = 'message-bubble sent';
    loadingBubble.innerHTML = `
        <div class="text-content">Sending image...</div>
        <div class="time">${new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
    `;
    document.getElementById('chatMessages').appendChild(loadingBubble);
    scrollToBottom();
    fetch('inbox-backend/all_messages_send.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loadingBubble.remove();
            if (data.status === 'success') {
                const newBubble = renderMessage({
                    id: data.message_id,
                    message: '',
                    created_at: data.created_at,
                    is_sender: true,
                    reaction: null,
                    attachment_url: data.attachment_url,
                    is_image: true
                });
                if (newBubble) document.getElementById('chatMessages').appendChild(newBubble);
                scrollToBottom();
            } else {
                showError('Failed to send image: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            loadingBubble.remove();
            showError('Failed to send image');
        })
        .finally(() => {
            event.target.value = '';
        });
}

function getInitials(name) {
    const names = name.split(' ');
    let initials = '';
    let count = 0;
    for (const n of names) {
        if (count >= 2) break;
        if (n.trim()) {
            initials += n.charAt(0).toUpperCase();
            count++;
        }
    }
    return initials || '?';
}

function getColorFromName(name) {
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % colors.length;
    return colors[index];
}

function getReactionEmoji(type) {
    const emojis = {
        'like': '👍',
        'heart': '❤️',
        'haha': '😄',
        'sad': '😢',
        'angry': '😠',
        'wow': '😲'
    };
    return emojis[type] || '';
}

function startMessagePolling() {
    setInterval(() => {
        if (currentChatUserId) {
            const formData = new FormData();
            formData.append('other_id', currentChatUserId);
            formData.append('last_message_id', lastMessageId);
            fetch('inbox-backend/fetch_messages.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        let hasNewMessages = false;
                        data.messages.forEach(msg => {
                            const existingBubble = document.querySelector(`.message-bubble[data-message-id="${msg.id}"]`);
                            if (existingBubble) {
                                updateReactionOnBubble(msg.id, msg.reaction);
                            } else {
                                const bubble = renderMessage(msg);
                                if (bubble) {
                                    document.getElementById('chatMessages').appendChild(bubble);
                                    hasNewMessages = true;
                                }
                            }
                        });
                        if (hasNewMessages) {
                            scrollToBottom();
                        }
                    }
                })
                .catch(error => {});
        }
    }, 3000);
}

window.addEventListener('resize', () => {
    const inboxContainer = document.getElementById('inboxContainer');
    const chatPanel = document.getElementById('chatPanel');
    if (window.innerWidth > 768) {
        inboxContainer.classList.remove('chat-open');
        chatPanel.classList.remove('open');
        inboxContainer.style.transform = '';
        chatPanel.style.transform = '';
        if (!currentChat) {
            chatPanel.classList.add('hidden');
            inboxContainer.classList.add('full-width');
        } else {
            chatPanel.classList.remove('hidden');
            inboxContainer.classList.remove('full-width');
        }
    } else {
        inboxContainer.classList.remove('full-width');
        if (!currentChat) {
            inboxContainer.classList.remove('chat-open');
            chatPanel.classList.remove('open');
            chatPanel.classList.add('hidden');
        } else {
            inboxContainer.classList.add('chat-open');
            chatPanel.classList.add('open');
            chatPanel.classList.remove('hidden');
        }
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && currentChat) {
        closeChat();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const chatPanel = document.getElementById('chatPanel');
    const inboxContainer = document.getElementById('inboxContainer');
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user_id');

    if (userId) {
        const item = document.querySelector(`.message-item[data-user-id="${userId}"]`);

        if (item) {
            openChat(item);

        } else {
            currentChatUserId = parseInt(userId, 10);
            currentChat = true;
            processedMessageIds.clear();
            lastMessageId = 0;

            if (statusInterval) clearInterval(statusInterval);
            statusInterval = startStatusPolling(currentChatUserId);

            inboxContainer.classList.remove('full-width');
            chatPanel.classList.remove('hidden');

            if (window.innerWidth <= 768) {
                inboxContainer.classList.add('chat-open');
                chatPanel.classList.add('open');
            }

            setTimeout(() => {
                const chatInput = document.getElementById('chatInput');
                if (chatInput) {
                    chatInput.focus();
                }
            }, 300);
        }

    } else {
        if (window.innerWidth > 768) {
            inboxContainer.classList.add('full-width');
            chatPanel.classList.add('hidden');
        } else {
            chatPanel.classList.add('hidden');
        }
    }

    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages.children.length > 1) {
        scrollToBottom();
    }

    const deleteButton = document.getElementById('confirmDeleteConversation');
    if (deleteButton) {
        deleteButton.addEventListener('click', deleteConversation);
    }

    startMessagePolling();
});

document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.querySelector('.search-inbox');
    const searchInput = document.getElementById('conversation-search');
    if (searchBox && searchInput) {
        const dropdown = document.createElement('div');
        dropdown.className = 'search-dropdown';
        dropdown.id = 'shop-search-dropdown';
        searchBox.appendChild(dropdown);
        const style = document.createElement('style');
        style.textContent = `.search-dropdown{position:absolute;width:100%;max-height:300px;overflow-y:auto;background:white;border:1px solid #ddd;border-top:none;z-index:1000;display:none;box-shadow:0 2px 5px rgba(0,0,0,0.1);margin-right:-50px}.search-result-item{padding:10px;display:flex;align-items:center;cursor:pointer}.search-result-item:hover{background-color:#f5f5f5}.search-result-avatar{width:40px;height:40px;border-radius:50%;margin-right:10px;object-fit:cover}.no-results{padding:10px;text-align:center;color:#666}`;
        document.head.appendChild(style);
        let searchTimeout = null;
        function searchShops(query) {
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            if (!query || query.length < 1) {
                dropdown.innerHTML = '';
                dropdown.style.display = 'none';
                return;
            }
            dropdown.innerHTML = '<div class="no-results">Searching...</div>';
            dropdown.style.display = 'block';
            searchTimeout = setTimeout(function() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `backend/search_shops.php?query=${encodeURIComponent(query)}`, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.error) {
                                dropdown.innerHTML = `<div class="no-results">${data.error}</div>`;
                                dropdown.style.display = 'block';
                                return;
                            }
                            dropdown.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(function(shop) {
                                    const item = document.createElement('div');
                                    item.className = 'search-result-item';
                                    item.setAttribute('data-user-id', shop.id);
                                    const imgSrc = shop.logo;
                                    const imgOnError = "this.onerror=null;this.src='../account/uploads/shop_logo/logo.jpg';";
                                    item.innerHTML = `<img src="${imgSrc}" alt="${shop.name}" class="search-result-avatar" onerror="${imgOnError}"><div class="search-result-name">${shop.name}</div>`;
                                    item.addEventListener('click', function() {
                                        const userId = this.getAttribute('data-user-id');
                                        const encryptedId = URLSecurityV2.encryptId(userId);
                                        window.location.href = `?user_id=${encryptedId}`;
                                    });
                                    dropdown.appendChild(item);
                                });
                                dropdown.style.display = 'block';
                            } else {
                                const noResults = document.createElement('div');
                                noResults.className = 'no-results';
                                noResults.textContent = 'No shops found';
                                dropdown.appendChild(noResults);
                                dropdown.style.display = 'block';
                            }
                        } catch (e) {
                            console.error('Error parsing JSON:', e, xhr.responseText);
                            dropdown.innerHTML = '<div class="no-results">Error processing search results</div>';
                            dropdown.style.display = 'block';
                        }
                    } else {
                        dropdown.innerHTML = '<div class="no-results">Error searching for shops</div>';
                        dropdown.style.display = 'block';
                    }
                };
                xhr.onerror = function() {
                    console.error('Network error occurred');
                    dropdown.innerHTML = '<div class="no-results">Network error</div>';
                    dropdown.style.display = 'block';
                };
                xhr.send();
            }, 150);
        }
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            searchShops(query);
        });
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 1) {
                searchShops(query);
            }
        });
        document.addEventListener('click', function(event) {
            if (searchBox && !searchBox.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
    pollForNewConversations();
    let isSending = false;
    let currentConversationId = null;
    let lastMessageId = 0;
    function updateViewportHeight() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    updateViewportHeight();
    window.addEventListener('resize', updateViewportHeight);
    const messageInput = document.querySelector('.message-input');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            const maxHeight = parseInt(getComputedStyle(this).lineHeight) * 5;
            this.style.overflowY = this.scrollHeight > maxHeight ? 'auto' : 'hidden';
            this.style.height = Math.min(this.scrollHeight, maxHeight) + 'px';
        });
    }
    function scrollToBottom() {
        const chatBox = document.getElementById('chat-messages');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }
    scrollToBottom();
    function fetchMessages() {
        if (!currentConversationId) return;
        $.ajax({
            url: 'inbox-backend/fetch_messages.php',
            type: 'POST',
            data: {
                shop_owner_id: currentConversationId,
                last_message_id: lastMessageId
            },
            dataType: 'json',
            success: function(data) {
                if (data.messages && data.messages.length > 0) {
                    displayMessages(data.messages);
                    lastMessageId = data.messages[data.messages.length - 1].id;
                }
            },
            complete: function() {
                setTimeout(fetchMessages, 3000);
            }
        });
    }
    function displayMessages(messages) {
        const chatBox = document.getElementById('chat-messages');
        if (!chatBox) return;
        messages.forEach(msg => {
            if (document.querySelector(`.message-bubble[data-message-id="${msg.id}"]`)) return;
            const bubbleClass = msg.is_sender ? 'sent' : 'received';
            const textContent = msg.message ? `<div class="text-content">${escapeHtml(msg.message)}</div>` : '';
            const timeContent = msg.created_at ? `<div class="time">${msg.created_at}</div>` : '';
            const messageHTML = `
                <div class="message-bubble ${bubbleClass}" data-message-id="${msg.id}">
                    ${textContent}
                    ${timeContent}
                </div>
            `;
            chatBox.insertAdjacentHTML('beforeend', messageHTML);
        });
        scrollToBottom();
    }
    const messageForm = document.getElementById('message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    function sendMessage() {
        if (isSending) return;
        const messageInput = document.querySelector('.message-input');
        const fileInput = document.getElementById('file-upload');
        const message = messageInput.value.trim();
        const hasFile = fileInput.files && fileInput.files.length > 0;
        if (!message && !hasFile) {
            alert('Please enter a message or attach an image');
            return;
        }
        isSending = true;
        const tempId = 'temp_' + Date.now();
        let tempContent = '';
        if (hasFile) {
            const file = fileInput.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                tempContent = `<div class="message-image-container"><img src="${e.target.result}" class="message-image sending">${message ? `<div class="message-text">${escapeHtml(message)}</div>` : ''}</div>`;
                addTempMessage(tempId, tempContent);
                actuallySendMessage(tempId);
            };
            reader.readAsDataURL(file);
        } else {
            tempContent = `<div class="message-text">${escapeHtml(message)}</div>`;
            addTempMessage(tempId, tempContent);
            actuallySendMessage(tempId);
        }
    }
    function addTempMessage(id, content) {
        const chatBox = document.getElementById('chat-messages');
        if (!chatBox) return;
        const messageHTML = `<div class="message-group me"><div class="message-bubble sent sending" data-temp-id="${id}">${content}<div class="message-time">Sending...</div></div></div>`;
        chatBox.innerHTML += messageHTML;
        scrollToBottom();
    }
    function actuallySendMessage(tempId) {
        const formData = new FormData(document.getElementById('message-form'));
        formData.append('client_message_id', tempId);
        fetch('inbox-backend/all_messages_send.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateMessageAfterSuccess(tempId, data);
                } else {
                    showFailedMessage(tempId);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFailedMessage(tempId);
            })
            .finally(() => {
                isSending = false;
                clearInputs();
            });
    }
    function updateMessageAfterSuccess(tempId, data) {
        const tempMessage = document.querySelector(`[data-temp-id="${tempId}"]`);
        if (!tempMessage) return;
        let content = '';
        if (data.is_image && data.attachment_url) {
            let imageUrl = data.attachment_url;
            if (!imageUrl.startsWith('/assets/uploads/attachments/')) {
                imageUrl = '/assets/uploads/attachments/' + imageUrl;
            }
            content = `<div class="message-image-container"><img src="${imageUrl}" class="message-image" onclick="openImageModal('${imageUrl}')">${data.message && data.message !== 'Sent an image' ? `<div class="message-text">${escapeHtml(data.message)}</div>` : ''}</div>`;
        } else {
            content = `<div class="message-text">${escapeHtml(data.message)}</div>`;
        }
        tempMessage.innerHTML = `${content}<div class="message-status"><i class="fas fa-check"></i></div>`;
        tempMessage.setAttribute('data-id', data.message_id);
        tempMessage.classList.remove('sending');
    }
    function showFailedMessage(tempId) {
        const tempMessage = document.querySelector(`[data-temp-id="${tempId}"]`);
        if (!tempMessage) return;
        tempMessage.innerHTML += `<div class="message-status failed">Failed to send. <button onclick="retryMessage('${tempId}')">Retry</button></div>`;
        tempMessage.classList.remove('sending');
    }
    window.retryMessage = function(tempId) {
        const tempMessage = document.querySelector(`[data-temp-id="${tempId}"]`);
        if (!tempMessage) return;
        tempMessage.querySelector('.message-status').remove();
        tempMessage.classList.add('sending');
        tempMessage.querySelector('.message-time').textContent = 'Sending...';
        actuallySendMessage(tempId);
    }
    function clearInputs() {
        const messageInput = document.querySelector('.message-input');
        const fileInput = document.getElementById('file-upload');
        const previewContainer = document.getElementById('attachment-preview-container');
        if (messageInput) messageInput.value = '';
        if (fileInput) fileInput.value = '';
        if (previewContainer) previewContainer.style.display = 'none';
        if (messageInput) messageInput.style.height = 'auto';
    }
    window.previewAttachment = function(input) {
        const previewContainer = document.getElementById('attachment-preview-container');
        const previewImg = document.getElementById('attachment-preview');
        const fileNameSpan = document.getElementById('attachment-name');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Only JPG, PNG, GIF, and WEBP images are allowed');
                input.value = '';
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('Image too large (max 10MB)');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                fileNameSpan.textContent = file.name;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    };
    window.cancelAttachment = function() {
        const fileInput = document.getElementById('file-upload');
        const previewContainer = document.getElementById('attachment-preview-container');
        fileInput.value = '';
        previewContainer.style.display = 'none';
    };
    window.sendQuickMessage = function(text) {
        const messageInput = document.querySelector('.message-input');
        if (messageInput) {
            messageInput.value = text;
            messageInput.focus();
        }
    };
    window.openImageModal = function(src) {
        const modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'image-modal';
        modal.onclick = closeImageModal;
        modal.innerHTML = `<span class="close-modal" onclick="event.stopPropagation(); closeImageModal()">&times;</span><div class="modal-content" onclick="event.stopPropagation()"><img src="${src}" class="modal-image-content"><div class="image-actions"><button onclick="downloadImage('${src}')"><i class="fas fa-download"></i> Download</button></div></div>`;
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
    };
    window.closeImageModal = function() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.remove();
            document.body.style.overflow = 'auto';
        }
    };
    window.downloadImage = function(src) {
        const link = document.createElement('a');
        link.href = src;
        link.download = src.split('/').pop() || 'download.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    if (currentConversationId) {
        fetchMessages();
    }
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-reply-btn')) {
            e.preventDefault();
            const buttonLabel = e.target.innerText.trim();
            const shopReplyMessage = e.target.getAttribute('data-response');
            const shopOwnerId = currentConversationId;
            const loggedInUserId = user_id;
            if (!buttonLabel || !shopReplyMessage || !shopOwnerId || !loggedInUserId) {
                console.error('Missing data for quick reply. Cannot proceed.');
                return;
            }
            const quickRepliesContainer = e.target.closest('.quick-replies-container');
            if (quickRepliesContainer) {
                quickRepliesContainer.style.display = 'none';
            }
            const userFormData = new FormData();
            userFormData.append('receiver_id', shopOwnerId);
            userFormData.append('message', buttonLabel);
            fetch('inbox-backend/all_messages_send.php', {
                method: 'POST',
                body: userFormData
            })
            .then(response => response.json())
            .then(userData => {
                if (userData.status !== 'success') {
                    console.error("Failed to send user's quick reply:", userData.message);
                    if (quickRepliesContainer) quickRepliesContainer.style.display = 'block';
                    return;
                }
                const userMessageForUI = {
                    id: userData.message_id,
                    message: buttonLabel,
                    created_at: userData.created_at,
                    is_sender: true
                };
                displayMessages([userMessageForUI]);
                const shopFormData = new FormData();
                shopFormData.append('sender_id', shopOwnerId);
                shopFormData.append('receiver_id', loggedInUserId);
                shopFormData.append('message', shopReplyMessage);
                shopFormData.append('is_automated', 1);
                return fetch('view-details-backend/store_automated_response.php', {
                    method: 'POST',
                    body: shopFormData
                });
            })
            .then(response => response.json())
            .then(shopData => {
                if (shopData.status === 'success') {
                    const shopMessageForUI = {
                        id: shopData.message_id,
                        message: shopReplyMessage,
                        created_at: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                        is_sender: false
                    };
                    displayMessages([shopMessageForUI]);
                } else {
                    console.error("Failed to store shop's reply:", shopData.message);
                }
            })
            .catch(error => {
                console.error("An error occurred during the quick reply process:", error);
                if (quickRepliesContainer) quickRepliesContainer.style.display = 'block';
            });
        }
    });
});

$(document).ready(function() {
    toastr.options = {
        "closeButton": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "timeOut": "2000"
    };

    function updateOptionStatus() {
        let filledCount = 0;
        for (let i = 1; i <= 5; i++) {
            const label = $(`#option${i}Label`).val();
            const response = $(`#option${i}Response`).val();
            const isFilled = (label && label.trim() !== '') && (response && response.trim() !== '');
            const statusBadge = $(`#statusBadge${i}`);
            if (isFilled) {
                statusBadge.removeClass('bg-secondary').addClass('bg-success').text('Filled');
                filledCount++;
            } else {
                statusBadge.removeClass('bg-success').addClass('bg-secondary').text('Empty');
            }
        }
        $('#filledCountBadge').text(`${filledCount}/5 filled`);
    }

    $('.option-input').on('input keyup change paste', function() {
        setTimeout(updateOptionStatus, 10);
    });

    $('#autoMessagesModal').on('shown.bs.modal', function() {
        updateOptionStatus();
    });
    
    updateOptionStatus();

    $('#resultOkBtn').on('click', function() {
        const resultModalEl = document.getElementById('resultModal');
        const resultModal = bootstrap.Modal.getInstance(resultModalEl);
        
        if (!$('#resultModal #successIcon').hasClass('d-none')) {
            window.location.reload();
        } else {
            if (resultModal) {
                resultModal.hide();
            }
            
            const settingsModalEl = document.getElementById('autoMessagesModal');
            let settingsModal = bootstrap.Modal.getInstance(settingsModalEl);
            if (!settingsModal) {
                 settingsModal = new bootstrap.Modal(settingsModalEl);
            }
            settingsModal.show();
        }
    });

    $('#autoMessagesForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const spinner = submitBtn.find('.spinner-border');
        const btnText = submitBtn.find('span:not(.spinner-border)');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Saving...');

        const settingsModal = bootstrap.Modal.getInstance(document.getElementById('autoMessagesModal'));
        if (settingsModal) {
            settingsModal.hide();
        }
        
        const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#resultModal #successIcon').removeClass('d-none');
                $('#resultModal #errorIcon').addClass('d-none');
                $('#resultModal #resultTitle').text('Settings Saved!');
                $('#resultModal #resultMessage').text('Your automated messages have been updated successfully.');
                resultModal.show();
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred while saving.';
                $('#resultModal #successIcon').addClass('d-none');
                $('#resultModal #errorIcon').removeClass('d-none');
                $('#resultModal #resultTitle').text('Error!');
                $('#resultModal #resultMessage').text(errorMsg);
                resultModal.show();
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text('Save Settings');
            }
        });
    });
});