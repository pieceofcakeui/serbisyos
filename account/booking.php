<?php
require_once '../functions/auth.php';
include 'backend/booking.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Bookings</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/booking.css">
    <style>
        #calendar {
            background: #fff;
            border-radius: 20px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 100%;
            overflow: hidden;
            height: auto !important;
        }

        .fc-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffc107;
            border-radius: 12px;
            padding: 8px 16px;
            color: #000;
            font-weight: 600;
        }

        .fc-toolbar-title {
            color: #000 !important;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .fc-button {
            background: #fff !important;
            color: #000 !important;
            border: 2px solid #ffc107 !important;
            border-radius: 10px !important;
            padding: 6px 12px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .fc-button:hover {
            background: #ffc107 !important;
            color: #000 !important;
        }

        .fc-prev-button,
        .fc-next-button {
            margin: 0 5px !important;
        }

        .fc-button-group .fc-button {
            border-radius: 10px !important;
            background: #fff !important;
            color: #000 !important;
            border: 2px solid #ffc107 !important;
            font-weight: 600 !important;
        }

        .fc-button-group .fc-button.fc-button-active {
            background: #ffc107 !important;
            color: #000 !important;
        }

        .fc-col-header-cell a {
            color: black !important;
            text-decoration: none;
            font-weight: 600;
        }

        .fc-daygrid-day-number {
            color: black !important;
            font-weight: 500;
            padding: 2px; /* Binawasan from 4px */
            float: none !important;
            display: block !important;
            text-align: center !important;
            margin: 0 auto;
            text-decoration: none !important;
        }

        .fc-col-header-cell {
            font-size: 0.8em;
        }

        .fc .fc-scrollgrid-sync-table,
        .fc-daygrid-body {
            min-width: unset !important;
        }

        .fc-daygrid-day {
            background: #fafafa;
            transition: background 0.2s;
        }

        .fc-daygrid-day:hover {
            background: #fffbe6;
        }

        .fc-event {
            border: none !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            background-color: var(--fc-event-bg-color, inherit) !important;
            color: var(--fc-event-text-color, inherit) !important;
            padding: 3px 4px !important;
            font-size: 0.7rem !important;
            line-height: 1.1 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            height: auto !important;
            min-height: 22px !important;
            max-height: 60px !important;
            text-overflow: ellipsis !important;
        }

        .fc-event-title,
        .fc-event-time {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: block !important;
            line-height: 1.1 !important;
            max-width: 100%;
        }

        .fc-event-title {
            font-weight: 600;
        }

        .fc-daygrid-day-events {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .fc-day-today {
            background: #fff8e1 !important;
            border: 1px solid #ffc107 !important;
        }

        .fc-scroller {
            overflow-y: visible !important;
            scrollbar-width: none !important;
        }

        .fc-scroller::-webkit-scrollbar {
            display: none !important;
        }

        .fc-daygrid-body-unbalanced .fc-daygrid-body-body {
            overflow-y: visible !important;
            height: auto !important;
        }

        .fc-daygrid-body-body table {
            height: auto !important;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        #calendar-tab-btn {
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-weight: 500;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
            border: none;
        }

        #calendar-tab-btn:hover {
            background-color: #0056b3;
        }

        #calendar-tab-btn.active {
            background-color: #28a745;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }

        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }

        .modal {
            z-index: 1050;
        }

        .modal-dialog {
            z-index: 1051;
        }

        .modal-backdrop:not(:first-child) {
            display: none;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 10px !important;
            }

            #calendar {
                padding: 5px;
                border-radius: 15px;
            }

            .booking-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 10px;
            }

            .services-booking-category-title {
                font-size: 1.15rem;
                margin-top: 40px;
            }

            #calendar-tab-btn {
                width: 100%;
                justify-content: center;
                padding: 10px 15px;
            }

            .services-booking-tabs {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 8px;
                margin-bottom: 15px;
                -webkit-overflow-scrolling: touch;
                gap: 5px;
            }

            .services-booking-tab {
                flex-shrink: 0;
                padding: 8px 12px;
                font-size: 0.85rem;
                white-space: nowrap;
            }

            .fc-toolbar.fc-header-toolbar {
                display: flex !important;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                padding: 10px 8px;
            }

            .fc-toolbar-chunk:nth-child(2) {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                flex-grow: 1;
            }

            .fc-toolbar-title {
                font-size: 1.1rem !important;
            }


            .fc-button {
                padding: 4px 8px !important;
                font-size: 0.75rem !important;
                margin: 0 !important;
            }

            .fc-button-group .fc-button {
                padding: 4px 6px !important;
                font-size: 0.7rem !important;
            }

            .fc-col-header-cell {
                font-size: 0.65rem !important;
                padding: 4px 2px !important;
            }

            .fc-col-header-cell a {
                font-size: 0.65rem !important;
                padding: 2px !important;
            }

            .fc-daygrid-day-number {
                font-size: 0.7rem !important;
                padding: 1px !important; /* Binawasan from 2px */
            }

            .fc-daygrid-day-frame {
                padding: 2px !important;
                min-height: 55px !important;
            }

            .fc-daygrid-day-header {
                padding: 2px 0 !important;
            }

            .fc-event {
                font-size: 0.6rem !important;
                padding: 2px 3px !important;
                min-height: 18px !important;
                max-height: 50px !important;
                border-radius: 4px !important;
            }

            .fc-event-title {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            .fc-event-time {
                font-size: 0.55rem !important;
            }

            .fc-daygrid-more-link {
                font-size: 0.65rem !important;
            }

            .fc-scrollgrid {
                border: 1px solid #ddd !important;
            }

            .fc-daygrid-body {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .fc-toolbar-title {
                font-size: 0.95rem !important;
            }

            .fc-button {
                padding: 3px 6px !important;
                font-size: 0.7rem !important;
            }

            .fc-button-group .fc-button {
                padding: 3px 5px !important;
                font-size: 0.65rem !important;
            }

            .fc-col-header-cell {
                font-size: 0.6rem !important;
                padding: 3px 1px !important;
            }

            .fc-daygrid-day-number {
                font-size: 0.65rem !important;
            }

            .fc-daygrid-day-frame {
                min-height: 50px !important;
            }

            .fc-event {
                font-size: 0.55rem !important;
                padding: 1px 2px !important;
                min-height: 16px !important;
                max-height: 45px !important;
            }

            .fc-event-title {
                -webkit-line-clamp: 1 !important;
            }

            .fc-event-time {
                font-size: 0.5rem !important;
            }
        }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="services-booking-section" id="booking-section">
            <div class="booking-header">
                <h2 class="services-booking-category-title"><i class="bi bi-check2-square"></i>Manage Service Bookings
                </h2>
                <div id="calendar-tab-btn" class="services-booking-tab" data-status="calendar">View Schedule</div>
            </div>

            <div class="services-booking-tabs">
                <div class="services-booking-tab active" data-status="all">All <span
                        class="badge bg-secondary text-white rounded-pill"><?php echo $status_counts['all']; ?></span>
                </div>
                <div class="services-booking-tab" data-status="pending">Pending <span
                        class="badge bg-warning text-dark rounded-pill"><?php echo $status_counts['Pending']; ?></span>
                </div>
                <div class="services-booking-tab" data-status="accept">Accepted <span
                        class="badge bg-primary text-white rounded-pill"><?php echo $status_counts['Accept']; ?></span>
                </div>
                <div class="services-booking-tab" data-status="completed">Completed <span
                        class="badge bg-success text-white rounded-pill"><?php echo $status_counts['Completed']; ?></span>
                </div>
                <div class="services-booking-tab" data-status="cancelled">Cancelled <span
                        class="badge bg-secondary text-white rounded-pill"><?php echo $status_counts['Cancelled']; ?></span>
                </div>
            </div>

            <div class="booking-content-wrapper" id="bookingContentWrapper">
                <div id='calendar' class="calendar-view" style="display: none;"></div>

                <div id="booking-list-container">
                    <?php if ($booking_result && $booking_result->num_rows > 0): ?>
                        <?php while ($booking = $booking_result->fetch_assoc()):
                            $booking_status = !empty($booking['booking_status']) ? trim($booking['booking_status']) : 'Pending';
                            $booking_badge_class = '';
                            switch (strtolower($booking_status)) {
                                case 'pending':
                                    $booking_badge_class = 'bg-warning text-dark';
                                    break;
                                case 'accept':
                                    $booking_badge_class = 'bg-primary';
                                    break;
                                case 'completed':
                                    $booking_badge_class = 'bg-success';
                                    break;
                                default:
                                    $booking_badge_class = 'bg-secondary';
                                    break;
                            }
                            $profile_picture = !empty($booking['profile_picture']) && $booking['profile_picture'] !== 'profile-user.png' ? '../assets/img/profile/' . $booking['profile_picture'] : 'https://i.imgur.com/6VBx3io.png';
                            ?>
                            <div class="booking-list-item" data-status="<?php echo strtolower($booking_status); ?>">
                                <div class="customer-info"><img src="<?php echo htmlspecialchars($profile_picture); ?>"
                                        alt="avatar"><span><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                                </div>
                                <div class="service-info">
                                    <?php echo htmlspecialchars(str_replace(['[', ']', '"', "'"], '', $booking['service_type'])); ?>
                                </div>
                                <div class="booking-date">
                                    <?php echo htmlspecialchars(date("M j, Y, g:i A", strtotime($booking['created_at']))); ?>
                                </div>
                                <div class="status-badge"><span
                                        class="badge <?php echo $booking_badge_class; ?>"><?php echo htmlspecialchars($booking_status); ?></span>
                                </div>
                                <div class="action-buttons">
                                    <button class="action-btn view-booking" data-id="<?php echo $booking['id']; ?>"
                                        title="View Details"><i class="bi bi-eye"></i></button>
                                    <button class="action-btn message-icon-container"
                                        data-user-id="<?php echo $booking['user_id']; ?>"
                                        data-customer-name="<?php echo htmlspecialchars($booking['customer_name']); ?>"
                                        data-profile-picture="<?php echo htmlspecialchars($profile_picture); ?>"
                                        title="Message Customer"><i class="bi bi-chat-dots"></i></button>
                                    <div class="dropdown">
                                        <button class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            title="More Actions"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if (strtolower($booking_status) === 'pending'): ?>
                                                <li><a class="dropdown-item change-status" href="#" data-status="Accept"
                                                        data-id="<?php echo $booking['id']; ?>"><i
                                                            class="fas fa-check-circle text-success me-2"></i> Accept</a></li>
                                                <li><a class="dropdown-item change-status text-danger" href="#" data-status="Reject"
                                                        data-id="<?php echo $booking['id']; ?>"><i
                                                            class="fas fa-times-circle me-2"></i> Reject</a></li>
                                            <?php elseif (strtolower($booking_status) === 'accept'): ?>
                                                <li><a class="dropdown-item change-status" href="#" data-status="Completed"
                                                        data-id="<?php echo $booking['id']; ?>"><i
                                                            class="fas fa-flag-checkered text-info me-2"></i> Mark as Completed</a>
                                                </li>
                                                <li><a class="dropdown-item change-status text-warning" href="#"
                                                        data-status="Cancelled" data-id="<?php echo $booking['id']; ?>"><i
                                                            class="fas fa-ban me-2"></i> Cancel Booking</a></li>
                                            <?php elseif (strtolower($booking_status) === 'completed' || strtolower($booking_status) === 'reject' || strtolower($booking_status) === 'cancelled'): ?>
                                                <li><a class="dropdown-item change-status text-danger" href="#" data-status="Delete"
                                                        data-id="<?php echo $booking['id']; ?>"><i
                                                            class="fas fa-trash-alt me-2"></i> Delete</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

                <div class="services-booking-empty-state services-booking-empty-state-all" style="display: none;"><i
                        class="fas fa-calendar-times"></i>
                    <h4>No Service Bookings</h4>
                </div>
                <div class="services-booking-empty-state services-booking-empty-state-pending" style="display: none;"><i
                        class="fas fa-clock"></i>
                    <h4>No Pending Bookings</h4>
                </div>
                <div class="services-booking-empty-state services-booking-empty-state-accept" style="display: none;"><i
                        class="fas fa-check-circle"></i>
                    <h4>No Accepted Bookings</h4>
                </div>
                <div class="services-booking-empty-state services-booking-empty-state-completed" style="display: none;">
                    <i class="fas fa-flag-checkered"></i>
                    <h4>No Completed Bookings</h4>
                </div>
                <div class="services-booking-empty-state services-booking-empty-state-cancelled" style="display: none;">
                    <i class="fas fa-times-circle"></i>
                    <h4>No Cancelled Bookings</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content message-modal-content">
                <div class="message-modal-header">
                    <div class="message-recipient-info">
                        <img src="" class="message-recipient-avatar" id="messageRecipientAvatar">
                        <h5 id="messageRecipientName"></h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="message-modal-body" id="messageModalBody">
                    <div class="message-container" id="messageContainer">
                    </div>
                </div>
                <div class="message-modal-footer">
                    <div class="message-input-container">
                        <label for="messageAttachment" class="message-attachment-btn">
                            <i class="fas fa-paperclip"></i>
                        </label>
                        <input type="file" id="messageAttachment" accept="image/*" style="display: none;">

                        <input type="text" class="message-input" id="messageInput" placeholder="Type a message...">
                        <button class="message-send-btn" id="messageSendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="reaction-picker" id="reactionPicker" style="display: none;">
                        <span class="reaction-option" data-reaction="like">👍</span>
                        <span class="reaction-option" data-reaction="heart">❤️</span>
                        <span class="reaction-option" data-reaction="haha">😄</span>
                        <span class="reaction-option" data-reaction="sad">😢</span>
                        <span class="reaction-option" data-reaction="angry">😠</span>
                        <span class="reaction-option" data-reaction="wow">😲</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="reaction-picker-overlay" id="reactionPickerOverlay"></div>

    <?php include 'include/booking-modal.php'; ?>
    <?php include 'include/emergency-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/booking.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/booking-message.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>

    <script>
        function forceCloseAllModals() {
            $('.modal').modal('hide');
            $('.modal').removeClass('show');
            $('.modal').css('display', 'none');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css({
                'padding-right': '',
                'overflow': ''
            });
        }

        function loadBookingModal(bookingId) {
            const bookingDetails = document.getElementById('bookingDetails');
            if (!bookingDetails) return;

            bookingDetails.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading booking details...</p>
                </div>
            `;

            $.ajax({
                url: 'backend/get_booking_details.php',
                type: 'GET',
                data: { id: bookingId },
                success: function (htmlResponse) {
                    if (htmlResponse.includes('alert alert-danger') || htmlResponse.includes('alert alert-warning')) {
                        bookingDetails.innerHTML = `
                            <div class="alert alert-danger m-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error loading booking details
                            </div>
                        `;
                    } else {
                        bookingDetails.innerHTML = htmlResponse;
                    }
                },
                error: function (xhr, status, error) {
                    bookingDetails.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading booking details: ${error}
                        </div>
                    `;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.services-booking-tabs .services-booking-tab');
            const calendarButton = document.getElementById('calendar-tab-btn');
            const allTogglableElements = Array.from(tabs).concat(calendarButton);

            const calendarDiv = document.getElementById('calendar');
            const bookingListContainer = document.getElementById('booking-list-container');
            const emptyStates = document.querySelectorAll('.services-booking-empty-state');

            let calendarInstance = null;
            let calendarInitialized = false;

            $(document).ready(function () {
                $('#bookingModal').on('show.bs.modal', function () {
                    $('body').addClass('modal-open');
                });

                $('#bookingModal').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css({
                        'padding-right': '',
                        'overflow': ''
                    });

                    const bookingDetails = document.getElementById('bookingDetails');
                    if (bookingDetails) {
                        bookingDetails.innerHTML = '';
                    }
                });

                $(document).on('click', '[data-bs-dismiss="modal"]', function () {
                    const modal = $(this).closest('.modal');
                    modal.modal('hide');
                });

                $(document).on('click', '.modal', function (e) {
                    if (e.target === this) {
                        $(this).modal('hide');
                    }
                });
            });

            function initializeCalendar() {
                if (calendarInitialized) {
                    calendarInstance.render();
                    return;
                }

                if (!calendarDiv) return;

                calendarInstance = new FullCalendar.Calendar(calendarDiv, {
                    initialView: 'dayGridMonth',
                    dayHeaders: true,
                    dayHeaderFormat: { weekday: 'narrow' },
                    headerToolbar: {
                        left: 'prev',
                        center: 'title today dayGridMonth,timeGridWeek,timeGridDay',
                        right: 'next'
                    },
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                    events: 'backend/get_calendar_events.php',
                    height: 'auto',
                    contentHeight: 'auto',

                    eventClick: function (info) {
                        info.jsEvent.preventDefault();
                        const bookingId = info.event.extendedProps.booking_id;
                        if (bookingId) {
                            loadBookingModal(bookingId);
                            setTimeout(() => {
                                $('#bookingModal').modal('show');
                            }, 100);
                        }
                    },

                    eventDidMount: function (info) {
                        const bg = info.event.backgroundColor || info.event.extendedProps.backgroundColor;
                        const text = info.event.textColor || '#000';
                        info.el.style.backgroundColor = bg;
                        info.el.style.borderColor = bg;
                        info.el.style.color = text;
                        info.el.style.cursor = 'pointer';
                        info.el.title = `${info.event.extendedProps.customer_name}\n${info.event.extendedProps.service_type}\nStatus: ${info.event.extendedProps.status}`;
                    },

                    eventContent: function (arg) {
                        const startTime = arg.timeText || '';
                        const name = arg.event.extendedProps.customer_name || '';
                        const service = arg.event.extendedProps.service_type || '';
                        const status = arg.event.extendedProps.status || '';

                        let statusBadge = '';
                        if (status === 'pending') {
                            statusBadge = '<span style="background: #fff; color: #000; padding: 1px 4px; border-radius: 3px; font-size: 0.55rem; font-weight: 600;">Pending</span>';
                        } else if (status === 'accept') {
                            statusBadge = '<span style="background: #fff; color: #007bff; padding: 1px 4px; border-radius: 3px; font-size: 0.55rem; font-weight: 600;">Accepted</span>';
                        } else if (status === 'completed') {
                            statusBadge = '<span style="background: #fff; color: #28a745; padding: 1px 4px; border-radius: 3px; font-size: 0.55rem; font-weight: 600;">Completed</span>';
                        }

                        return {
                            html: `
                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 2px;">
                                    <div style="font-weight: 600; font-size: 0.7rem; overflow: hidden; text-overflow: ellipsis;">${name}</div>
                                    <div style="font-size: 0.65rem; opacity: 0.9; overflow: hidden; text-overflow: ellipsis;">${service}</div>
                                    ${startTime ? `<div style="font-size: 0.6rem; opacity: 0.8; margin-top: 1px;">${startTime}</div>` : ''}
                                    <div style="margin-top: 2px;">${statusBadge}</div>
                                </div>`
                        };
                    },
                });

                calendarInstance.render();
                calendarInitialized = true;
            }

            function filterBookings(status) {
                calendarDiv.style.display = 'none';
                if (bookingListContainer) bookingListContainer.style.display = 'none';
                emptyStates.forEach(state => state.style.display = 'none');

                allTogglableElements.forEach(el => el.classList.remove('active'));

                if (status === 'calendar') {
                    calendarDiv.style.display = 'block';
                    calendarButton.classList.add('active');
                    initializeCalendar();

                } else {
                    const listItems = document.querySelectorAll('.booking-list-item');
                    let hasVisibleItems = false;

                    const currentTab = document.querySelector(`.services-booking-tabs [data-status="${status}"]`);
                    if (currentTab) currentTab.classList.add('active');

                    listItems.forEach(item => {
                        const isVisible = (status === 'all' || item.dataset.status === status);
                        item.style.display = isVisible ? 'grid' : 'none';
                        if (isVisible) hasVisibleItems = true;
                    });

                    if (bookingListContainer) {
                        if (hasVisibleItems) {
                            bookingListContainer.style.display = 'block';
                        } else {
                            document.querySelector(`.services-booking-empty-state-${status}`).style.display = 'block';
                        }
                    } else {
                        document.querySelector(`.services-booking-empty-state-${status}`).style.display = 'block';
                    }
                }
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const status = this.dataset.status;
                    filterBookings(status);
                });
            });

            if (calendarButton) {
                calendarButton.addEventListener('click', function () {
                    filterBookings('calendar');
                });
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('.view-booking')) {
                    const button = e.target.closest('.view-booking');
                    const bookingId = button.getAttribute('data-id');
                    loadBookingModal(bookingId);
                    $('#bookingModal').modal('show');
                }
            });

            filterBookings('all');
        });
    </script>
</body>

</html>