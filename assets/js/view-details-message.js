let activeChatUser = null;
let messagePollingInterval = null;
let currentImageUrl = '';
let userHasScrolled = false;
let receivedMessageIds = [];
let pendingMessages = {};
let showLastMessageOnLoad = false;
let currentMessageIdForReaction = null;
let automatedMessageShown = false;

function getReactionEmoji(type) {
    const emojis = {
        'like': '👍', 'heart': '❤️', 'haha': '😄', 'sad': '😢', 'angry': '😠', 'wow': '😲'
    };
    return emojis[type] || '';
}

function showReactionPicker(event, messageId) {
    event.stopPropagation();
    hideReactionPicker();
    if (!messageId) return;
    currentMessageIdForReaction = messageId;
    const bubble = event.currentTarget;
    const messageContainer = bubble.closest('.message-container');
    const picker = document.createElement('div');
    picker.className = 'reaction-picker';
    picker.id = 'dynamicReactionPicker';
    picker.innerHTML = `
        <span class="reaction-option" onclick="addReaction('like')">👍</span>
        <span class="reaction-option" onclick="addReaction('heart')">❤️</span>
        <span class="reaction-option" onclick="addReaction('haha')">😄</span>
        <span class="reaction-option" onclick="addReaction('sad')">😢</span>
        <span class="reaction-option" onclick="addReaction('angry')">😠</span>
        <span class="reaction-option" onclick="addReaction('wow')">😲</span>
    `;
    if (messageContainer) {
        messageContainer.appendChild(picker);
    }
    picker.addEventListener('click', e => e.stopPropagation());
    setTimeout(() => {
        document.addEventListener('click', hideReactionPicker, { once: true });
    }, 0);
}

function hideReactionPicker() {
    const picker = document.getElementById('dynamicReactionPicker');
    if (picker) {
        picker.remove();
    }
}

function addReaction(reactionType) {
    if (!currentMessageIdForReaction) return;
    const messageId = currentMessageIdForReaction;

    hideReactionPicker();

    const messageElement = document.querySelector(`.message-container[data-id="${messageId}"]`);
    if (messageElement) {
        const messageBubble = messageElement.querySelector('.message-bubble');
        if (messageBubble) {
            let reactionsContainer = messageBubble.querySelector('.reactions-container');
            if (!reactionsContainer) {
                reactionsContainer = document.createElement('div');
                reactionsContainer.className = 'reactions-container';
                messageBubble.appendChild(reactionsContainer);
            }
            const existingOptimisticReaction = reactionsContainer.querySelector('.optimistic-reaction');
            if (existingOptimisticReaction) {
                existingOptimisticReaction.remove();
            }
            const emoji = getReactionEmoji(reactionType);
            const newReactionHTML = `<div class="reaction-item optimistic-reaction">${emoji}</div>`;
            reactionsContainer.insertAdjacentHTML('beforeend', newReactionHTML);
        }
    }

    $.ajax({
        url: BASE_URL + '/account/inbox-backend/update_reaction',
        method: 'POST',
        data: { message_id: messageId, reaction_type: reactionType },
        dataType: 'json',
        success: function (response) {
            if (response.status !== 'success') {
                alert('Failed to apply reaction: ' + (response.message || 'Unknown error'));
                if (messageElement) {
                    const optimisticReaction = messageElement.querySelector('.optimistic-reaction');
                    if (optimisticReaction) {
                        optimisticReaction.remove();
                    }
                }
            }
        },
        error: function () {
            alert('An error occurred while applying the reaction.');
            if (messageElement) {
                const optimisticReaction = messageElement.querySelector('.optimistic-reaction');
                if (optimisticReaction) {
                    optimisticReaction.remove();
                }
            }
        }
    });
}


function renderReactions(messageBubble, reactions) {
    let reactionsContainer = messageBubble.querySelector('.reactions-container');
    if (!reactions || reactions.length === 0) {
        if (reactionsContainer) reactionsContainer.remove();
        return;
    }
    if (!reactionsContainer) {
        reactionsContainer = document.createElement('div');
        reactionsContainer.className = 'reactions-container';
        messageBubble.appendChild(reactionsContainer);
    }
    const reactionCounts = reactions.reduce((acc, reaction) => {
        acc[reaction.type] = (acc[reaction.type] || 0) + 1;
        return acc;
    }, {});
    let reactionsHTML = '';
    for (const [type, count] of Object.entries(reactionCounts)) {
        reactionsHTML += `<div class="reaction-item">${getReactionEmoji(type)}</div>`;
    }
    reactionsContainer.innerHTML = reactionsHTML;
}

document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.getElementById('chatContainer');
    const senderId = chatContainer ? chatContainer.getAttribute('data-user-id') : null;
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('quick-reply-btn')) {
            const buttonLabel = e.target.innerText.trim();
            const shopReplyMessage = e.target.getAttribute('data-response');
            const shopOwnerId = activeChatUser;
            if (buttonLabel && shopReplyMessage && shopOwnerId && senderId) {
                const clientMessageId = 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const tempMessage = {
                    id: clientMessageId, message: buttonLabel, is_sender: true,
                    created_at: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), status: 'sending'
                };
                addMessageToUI(tempMessage);
                scrollToBottom();
                const formData = new FormData();
                formData.append('sender_id', senderId);
                formData.append('receiver_id', shopOwnerId);
                formData.append('message', buttonLabel);
                formData.append('client_message_id', clientMessageId);
                $.ajax({
                    url: BASE_URL + '/account/view-details-backend/send_message',
                    method: 'POST', data: formData, contentType: false, processData: false,
                    success: function (apiResponse) {
                        const data = typeof apiResponse === 'string' ? JSON.parse(apiResponse) : apiResponse;
                        if (data.status === 'success') {
                            updateMessageAfterSuccess(clientMessageId, data.message_id, '');

                            setTimeout(() => {
                                const shopClientMessageId = 'auto_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                const shopMessageObj = {
                                    id: shopClientMessageId, message: shopReplyMessage, is_sender: false, is_automated: true
                                };
                                addMessageToUI(shopMessageObj);
                                scrollToBottom();
                                const shopFormData = new FormData();
                                shopFormData.append('sender_id', shopOwnerId);
                                shopFormData.append('receiver_id', senderId);
                                shopFormData.append('message', shopReplyMessage);
                                shopFormData.append('is_automated', 1);
                                shopFormData.append('client_message_id', shopClientMessageId);
                                $.ajax({
                                    url: BASE_URL + '/account/view-details-backend/store_automated_response',
                                    method: 'POST', data: shopFormData, contentType: false, processData: false,
                                    success: function (storeResponse) {
                                        if (storeResponse.status === 'success' && storeResponse.message_id) {
                                            const tempMsgElement = document.querySelector(`[data-client-message-id="${shopClientMessageId}"]`);
                                            if (tempMsgElement) {
                                                tempMsgElement.setAttribute('data-id', storeResponse.message_id);
                                            }
                                            receivedMessageIds.push(storeResponse.message_id);
                                        }
                                    }
                                });
                            }, 1000);
                        } else {
                            updateMessageStatus(clientMessageId, 'failed');
                        }
                    },
                    error: function () {
                        updateMessageStatus(clientMessageId, 'failed');
                    }
                });
            }
        }
    });
    document.getElementById('chatMessages').addEventListener('scroll', function () {
        const chatMessages = this;
        const threshold = 50;
        userHasScrolled = chatMessages.scrollHeight - chatMessages.clientHeight - chatMessages.scrollTop > threshold;
    });
});

function toggleChat(shopOwnerId) {
    const chatContainer = document.getElementById('chatContainer');
    const body = document.body;
    if (chatContainer.classList.contains('active')) {
        chatContainer.classList.remove('active');
        body.classList.remove('body-no-scroll');
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
            messagePollingInterval = null;
        }
        activeChatUser = null;
    } else {
        const chatMessages = document.getElementById('chatMessages');
        if (activeChatUser !== shopOwnerId) {
            chatMessages.innerHTML = '';
            receivedMessageIds = [];
            automatedMessageShown = false;
        }
        chatContainer.classList.add('active');
        body.classList.add('body-no-scroll');
        if (shopOwnerId) {
            activeChatUser = shopOwnerId;
            showLastMessageOnLoad = true;
            fetchMessages(shopOwnerId);
            if (messagePollingInterval) clearInterval(messagePollingInterval);
            messagePollingInterval = setInterval(() => fetchMessages(shopOwnerId), 3000);
        }
        document.getElementById('messageInput').focus();
    }
}

function showTypingIndicator() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages.querySelector('.typing-indicator')) return;
    const typingHTML = `
        <div class="typing-indicator">
            <div class="typing-bubble">
                <div class="typing-dots"><span></span><span></span><span></span></div>
            </div>
        </div>`;
    chatMessages.insertAdjacentHTML('beforeend', typingHTML);
    scrollToBottom();
}

function removeTypingIndicator() {
    const typingIndicator = document.querySelector('.typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

function fetchAutomatedMessage(shopOwnerId) {
    $.ajax({
        url: BASE_URL + '/account/fetch_automated_message',
        method: 'POST',
        data: { shop_owner_id: shopOwnerId },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data.error || !data.welcome_message) {
                    removeTypingIndicator();
                    return;
                }

                if (data.welcome_message) {
                    showTypingIndicator();

                    setTimeout(() => {
                        removeTypingIndicator();

                        const chatMessages = document.getElementById('chatMessages');
                        const welcomeHTML = `
                            <div class="automated-message-container">
                                <div class="automated-message-bubble">
                                    <div class="message-text">${data.welcome_message}</div>
                                </div>
                            </div>`;
                        chatMessages.insertAdjacentHTML('beforeend', welcomeHTML);

                        if (data.quick_replies && data.quick_replies.options) {
                            setTimeout(() => {
                                const filledOptions = data.quick_replies.options.filter(opt => opt.is_filled);
                                if (filledOptions.length > 0) {
                                    let quickRepliesHTML = '<div class="quick-replies-container">';
                                    filledOptions.forEach(option => {
                                        quickRepliesHTML += `
                                            <button class="quick-reply-btn" data-response="${option.response.replace(/"/g, '&quot;')}">
                                                ${option.label}
                                            </button>`;
                                    });
                                    quickRepliesHTML += '</div>';
                                    chatMessages.insertAdjacentHTML('beforeend', quickRepliesHTML);
                                    scrollToBottom();
                                }
                            }, 1000);
                        }
                        scrollToBottom();
                    }, 2000);
                }
            } catch (e) {
                console.error("Error in fetchAutomatedMessage:", e);
                removeTypingIndicator();
            }
        },
        error: function () {
            removeTypingIndicator();
        }
    });
}

function fetchMessages(shopOwnerId, forceRefresh = false) {

    const shouldShowTyping = forceRefresh || showLastMessageOnLoad;

    if (shouldShowTyping) {
        showTypingIndicator();
    }

    const lastMessageId = forceRefresh ? 0 : getLastMessageId();
    $.ajax({
        url: BASE_URL + '/account/view-details-backend/fetch_message',
        method: 'POST',
        data: {
            shop_owner_id: shopOwnerId,
            last_message_id: lastMessageId,
            received_message_ids: forceRefresh ? '' : receivedMessageIds.join(',')
        },
        success: function (response) {
            try {
                if (shouldShowTyping) {
                    removeTypingIndicator();
                }

                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.error) {
                    return;
                }

                if (response.messages && response.messages.length > 0) {
                    const wasAtBottom = isScrolledToBottom();
                    displayMessages(response.messages, forceRefresh);

                    if (forceRefresh) {
                        receivedMessageIds = [];
                    }

                    response.messages.forEach(msg => {
                        if (!receivedMessageIds.includes(msg.id)) {
                            receivedMessageIds.push(msg.id);
                        }
                        if (receivedMessageIds.length > 100) receivedMessageIds.shift();
                    });

                    if (wasAtBottom || forceRefresh) {
                        scrollToBottom();
                    }
                }

                if (!automatedMessageShown && (!response.messages || response.messages.length === 0)) {
                    automatedMessageShown = true;
                    setTimeout(() => {
                        fetchAutomatedMessage(shopOwnerId);
                    }, 1000);
                }

                if (showLastMessageOnLoad) {
                    showLastMessageOnLoad = false;
                    scrollToBottom();
                }
            } catch (e) {
                console.error("Error parsing messages:", e);
                if (shouldShowTyping) {
                    removeTypingIndicator();
                }
            }
        },
        error: function () {
            if (shouldShowTyping) {
                removeTypingIndicator();
            }
        }
    });
}

function displayMessages(messages, forceRefresh = false) {
    const chatMessages = document.getElementById('chatMessages');
    if (forceRefresh) {
        chatMessages.innerHTML = '';
    }

    messages.forEach(msg => {
        const existingMessageContainer = document.querySelector(`.message-container[data-id="${msg.id}"]`);

        if (existingMessageContainer) {
            const messageBubble = existingMessageContainer.querySelector('.message-bubble');
            if (messageBubble) {
                renderReactions(messageBubble, msg.reactions);
            }
            return;
        }

        const isOutgoing = msg.is_sender;
        const messageClass = isOutgoing ? 'message-outgoing' : 'message-incoming';
        let content = msg.is_image ?
            `<div class="message-image-container"><img src="${msg.attachment_url}" class="message-image" onclick="openImageModal('${msg.attachment_url}')"></div>
             ${msg.message && msg.message !== 'Sent an image' ? `<div class="message-text">${msg.message}</div>` : ''}` :
            `<div class="message-text">${msg.message}</div>`;
        const messageHTML = `
            <div class="message-container ${messageClass}" data-id="${msg.id}">
                <div class="message-bubble" onclick="showReactionPicker(event, ${msg.id})">
                    ${content}
                    <span class="message-time">${msg.created_at}</span>
                </div>
            </div>`;
        chatMessages.insertAdjacentHTML('beforeend', messageHTML);
        const newBubble = chatMessages.querySelector(`.message-container[data-id="${msg.id}"] .message-bubble`);
        if (newBubble && msg.reactions) {
            renderReactions(newBubble, msg.reactions);
        }
    });

    if (!forceRefresh) {
        if (isScrolledToBottom()) {
            scrollToBottom();
        }
    }
}

function addMessageToUI(message) {
    const isOutgoing = message.is_sender;
    const messageClass = isOutgoing ? 'message-outgoing' : 'message-incoming';
    const isAutomated = message.is_automated;
    let content = message.is_image ?
        `<div class="message-image-container"><img src="${message.attachment_url}" class="message-image" onclick="openImageModal('${message.attachment_url}')"></div>` :
        `<div class="message-text">${message.message}</div>`;
    const messageHTML = `
        <div class="message-container ${messageClass}" data-client-message-id="${message.id}" data-is-automated="${isAutomated || 0}">
            <div class="message-bubble">
                ${content}
                <span class="message-time">${message.created_at || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                ${message.status === 'sending' ? '<div class="message-status">Sending...</div>' : ''}
                ${message.status === 'failed' ? '<div class="message-status failed">Failed. Click to retry</div>' : ''}
            </div>
        </div>`;
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.insertAdjacentHTML('beforeend', messageHTML);
    scrollToBottom();
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        setTimeout(() => { chatMessages.scrollTop = chatMessages.scrollHeight; }, 100);
    }
}

function isScrolledToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    const threshold = 30;
    return chatMessages.scrollHeight - chatMessages.clientHeight - chatMessages.scrollTop <= threshold;
}

function getLastMessageId() {
    const allMessages = document.querySelectorAll('#chatMessages .message-container[data-id]');
    if (allMessages.length === 0) return 0;
    return Array.from(allMessages).reduce((maxId, message) => {
        const id = parseInt(message.dataset.id) || 0;
        return id > maxId ? id : maxId;
    }, 0);
}

function handleKeyPress(e, senderId, receiverId) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage(senderId, receiverId);
    }
}

function sendMessage(senderId, receiverId) {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    const fileInput = document.getElementById('file-upload');
    if (message === '' && fileInput.files.length === 0) return;
    const clientMessageId = 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const tempMessage = {
        id: clientMessageId, message: message, is_sender: true, is_image: fileInput.files.length > 0,
        attachment_url: fileInput.files.length > 0 ? URL.createObjectURL(fileInput.files[0]) : '',
        created_at: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        status: 'sending'
    };
    pendingMessages[clientMessageId] = {
        senderId: senderId, receiverId: receiverId, message: message,
        file: fileInput.files.length > 0 ? fileInput.files[0] : null, retries: 0
    };
    addMessageToUI(tempMessage);
    messageInput.value = '';
    fileInput.value = '';
    document.getElementById('attachmentPreview').innerHTML = '';
    scrollToBottom();
    actuallySendMessage(clientMessageId);
}

function actuallySendMessage(clientMessageId) {
    const pending = pendingMessages[clientMessageId];
    if (!pending) return;
    const formData = new FormData();
    formData.append('sender_id', pending.senderId);
    formData.append('receiver_id', pending.receiverId);
    formData.append('message', pending.message);
    formData.append('client_message_id', clientMessageId);
    if (pending.file) {
        formData.append('attachment', pending.file);
    }
    updateMessageStatus(clientMessageId, 'sending');
    $.ajax({
        url: BASE_URL + '/account/view-details-backend/send_message',
        method: 'POST', data: formData, contentType: false, processData: false,
        success: function (response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            if (data.status === 'success') {
                receivedMessageIds.push(data.message_id);
                updateMessageAfterSuccess(clientMessageId, data.message_id, data.attachment_url);
                delete pendingMessages[clientMessageId];
            } else {
                updateMessageStatus(clientMessageId, 'failed');
            }
        },
        error: function () {
            updateMessageStatus(clientMessageId, 'failed');
        }
    });
}

function updateMessageStatus(clientMessageId, status) {
    const messageElement = document.querySelector(`[data-client-message-id="${clientMessageId}"]`);
    if (!messageElement) return;
    let statusElement = messageElement.querySelector('.message-status');
    if (!statusElement) {
        statusElement = document.createElement('div');
        statusElement.className = 'message-status';
        messageElement.querySelector('.message-bubble').appendChild(statusElement);
    }
    statusElement.innerHTML = status === 'sending' ? 'Sending...' : status === 'failed' ? 'Failed. Click to retry' : '';
    if (status === 'failed') {
        statusElement.classList.add('failed');
        statusElement.style.cursor = 'pointer';
        statusElement.onclick = () => {
            pendingMessages[clientMessageId].retries = 0;
            actuallySendMessage(clientMessageId);
        };
    } else {
        statusElement.classList.remove('failed');
        statusElement.onclick = null;
    }
}

function updateMessageAfterSuccess(clientMessageId, serverMessageId, finalAttachmentUrl) {
    const messageElement = document.querySelector(`[data-client-message-id="${clientMessageId}"]`);
    if (!messageElement) return;
    messageElement.setAttribute('data-id', serverMessageId);
    messageElement.removeAttribute('data-client-message-id');
    const bubble = messageElement.querySelector('.message-bubble');
    if (bubble) {
        bubble.setAttribute('onclick', `showReactionPicker(event, ${serverMessageId})`);
    }
    if (finalAttachmentUrl) {
        const img = messageElement.querySelector('img');
        if (img) img.src = finalAttachmentUrl;
    }
    const statusElement = messageElement.querySelector('.message-status');
    if (statusElement) statusElement.remove();
    delete pendingMessages[clientMessageId];
}

function previewAttachment(input) {
    const previewContainer = document.getElementById('attachmentPreview');
    previewContainer.innerHTML = '';
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'attachment-preview';
                previewDiv.innerHTML = `<img src="${e.target.result}" alt="Preview"><span class="remove-attachment">&times;</span>`;
                previewContainer.appendChild(previewDiv);
                previewDiv.querySelector('.remove-attachment').onclick = function () {
                    previewContainer.innerHTML = '';
                    input.value = '';
                };
            };
            reader.readAsDataURL(file);
        }
    }
}

function openImageModal(imageUrl) {
    currentImageUrl = imageUrl;
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('view-shop-modalImage');
    modalImage.src = imageUrl;
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function downloadImage() {
    if (!currentImageUrl) return;
    const link = document.createElement('a');
    link.href = currentImageUrl;
    link.download = currentImageUrl.split('/').pop() || 'download.jpg';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function resetScrollTracking() {
    userHasScrolled = false;
}