let currentRecipientId = null;
let currentMessageIdForReaction = null;

function openMessageWindow(userId, customerName, profilePicture) {
    currentRecipientId = userId;

    $('#messageRecipientName').text(customerName);
    $('#messageRecipientAvatar').attr('src', profilePicture);

    $('#messageContainer').empty();

    const messageModalElement = document.getElementById('messageModal');
    const messageModal = bootstrap.Modal.getInstance(messageModalElement) || new bootstrap.Modal(messageModalElement);

    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();

    messageModal.show();

    loadMessages(userId);

    setupMessageWindowEvents();
}

$('#messageModal').on('hidden.bs.modal', function () {
    currentRecipientId = null;
    $('#messageInput').val('').trigger('input');
    $('#messageAttachment').val('');

    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
});

function setupMessageWindowEvents() {
    $('#messageInput').off('keypress').on('keypress', function (e) {
        if (e.which === 13) {
            sendMessage();
        }
    });

    $('#messageSendBtn').off('click').on('click', sendMessage);

    $('#messageAttachment').off('change').on('change', function () {
        if (this.files && this.files[0]) {
            sendMessage(this.files[0]);
        }
    });

    $(document).on('click', '.message-bubble', function (e) {
        if ($(e.target).hasClass('reaction-option') || $(e.target).closest('.reactions-container').length) {
            return;
        }

        const messageId = $(this).closest('.message-container').data('id');
        if (!messageId) return;

        currentMessageIdForReaction = messageId;
        const picker = $('#reactionPicker');

        picker.css({
            'position': 'fixed',
            'top': '50%',
            'left': '50%',
            'transform': 'translate(-50%, -50%)',
            'z-index': '9999'
        }).show();

        if (picker.parent()[0] !== document.body) {
            picker.appendTo('body');
        }
    });

    $('.reaction-option').off('click').on('click', function () {
        const reactionType = $(this).data('reaction');
        addReaction(currentMessageIdForReaction, reactionType);
        $('#reactionPicker').hide();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#reactionPicker').length && !$(e.target).closest('.message-bubble').length) {
            $('#reactionPicker').hide();
        }
    });
}

function loadMessages(userId) {
    $.ajax({
        url: 'inbox-backend/fetch_messages.php',
        method: 'POST',
        data: { other_id: userId },
        dataType: 'json',
        success: function (response) {
            if (response.messages && response.messages.length > 0) {
                renderMessages(response.messages);
            } else {
                $('#messageContainer').html('<div class="text-center text-muted py-3">No messages yet</div>');
            }

            const messageBody = $('#messageModalBody');
            messageBody.scrollTop(messageBody[0].scrollHeight);
        },
        error: function () {
            $('#messageContainer').html('<div class="text-center text-danger py-3">Failed to load messages</div>');
        }
    });
}


function renderMessages(messages) {
    const container = $('#messageContainer');
    container.empty();

    messages.forEach(message => {
        const messageClass = message.is_sender ? 'sent' : 'received';
        const messageHtml = `
            <div class="message-container" data-id="${message.id}">
                <div class="message-bubble ${messageClass}">
                    ${message.message}
                    ${message.is_image ? `<img src="${message.attachment_url}" class="message-attachment">` : ''}
                    <div class="message-time">${message.created_at}</div>
                    ${message.reactions && message.reactions.length > 0 ?
                `<div class="reactions-container">${renderReactionsHtml(message.reactions)}</div>` : ''}
                </div>
            </div>
        `;
        container.append(messageHtml);
    });
}

function renderReactionsHtml(reactions) {
    const reactionCounts = reactions.reduce((acc, reaction) => {
        acc[reaction.type] = (acc[reaction.type] || 0) + 1;
        return acc;
    }, {});

    let html = '';
    for (const [type, count] of Object.entries(reactionCounts)) {
        html += `<span class="reaction-item">${getReactionEmoji(type)}</span>`;
    }
    return html;
}

function getReactionEmoji(type) {
    const emojis = {
        'like': '👍', 'heart': '❤️', 'haha': '😄', 'sad': '😢', 'angry': '😠', 'wow': '😲'
    };
    return emojis[type] || '';
}

$(document).ready(function () {
    $('#messageSendBtn').prop('disabled', true);

    $('#messageInput').on('input', function () {
        const hasText = $(this).val().trim().length > 0;
        $('#messageSendBtn').prop('disabled', !hasText);
    });

    $(document).on('click', '.message-icon-container', function () {
        const userId = $(this).data('user-id');
        const customerName = $(this).data('customer-name');
        const profilePicture = $(this).data('profile-picture');

        openMessageWindow(userId, customerName, profilePicture);
    });

    $('#messageModal').on('shown.bs.modal', function () {
        const messageBody = $('#messageModalBody');
        setTimeout(function () {
            messageBody.scrollTop(messageBody[0].scrollHeight);
        }, 10);

        $('#messageInput').val('').trigger('input');
    });
});

function sendMessage(attachmentFile = null) {
    const messageText = $('#messageInput').val().trim();
    if (!messageText && !attachmentFile) return;

    const formData = new FormData();
    formData.append('receiver_id', currentRecipientId);
    if (messageText) formData.append('message', messageText);
    if (attachmentFile) formData.append('attachment', attachmentFile);

    $.ajax({
        url: 'inbox-backend/all_messages_send.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                $('#messageInput').val('').trigger('input');
                $('#messageAttachment').val('');

                const messageHtml = `
                    <div class="message-container" data-id="${response.message_id}">
                        <div class="message-bubble sent">
                            ${response.message}
                            ${response.attachment_url ? `<img src="${response.attachment_url}" class="message-attachment">` : ''}
                            <div class="message-time">${response.created_at}</div>
                        </div>
                    </div>
                `;
                $('#messageContainer').append(messageHtml);

                setTimeout(() => {
                    $('#messageModalBody').scrollTop($('#messageModalBody')[0].scrollHeight);
                }, 100);
            } else {
                showToast('Failed to send message: ' + (response.message || 'Unknown error'));
            }
        },
        error: function () {
            showToast('A network error occurred while sending the message.');
        }
    });
}

function showToast(message, type = 'danger') {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    const toastIcon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    const toastHeader = type === 'success' ? 'Success' : 'Error';

    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-${type} text-white">
                <i class="fas ${toastIcon} me-2"></i>
                <strong class="me-auto">${toastHeader}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();
    toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
}

function addReaction(messageId, reactionType) {
    if (!messageId || !reactionType) return;

    const messageElement = $(`.message-container[data-id="${messageId}"] .message-bubble`);
    if (messageElement.length) {
        let reactionsContainer = messageElement.find('.reactions-container');
        if (reactionsContainer.length === 0) {
            reactionsContainer = $('<div class="reactions-container"></div>');
            messageElement.append(reactionsContainer);
        }

        reactionsContainer.append(`<span class="reaction-item optimistic-reaction">${getReactionEmoji(reactionType)}</span>`);
    }

    $.ajax({
        url: 'inbox-backend/update_reaction.php',
        method: 'POST',
        data: {
            message_id: messageId,
            reaction_type: reactionType
        },
        dataType: 'json',
        success: function (response) {
            if (response.status !== 'success') {
                messageElement.find('.optimistic-reaction').remove();
                showToast('Failed to add reaction: ' + (response.message || 'Unknown error'));
            } else {
                messageElement.find('.optimistic-reaction').remove();
                loadMessages(currentRecipientId);
            }
        },
        error: function () {
            messageElement.find('.optimistic-reaction').remove();
            console.error('AJAX error while adding reaction.');
            showToast('A network error occurred while adding the reaction.');
        }
    });
}