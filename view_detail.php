<?php
session_start();
include 'functions/base-path.php';
include 'view-details-backend/view-details.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> <?php echo htmlspecialchars($shop['shop_name']); ?></title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/img/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/img/favicon.png">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/account-required.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Montserrat', sans-serif;
    color: var(--text-color);
    line-height: 1.6;
    background: linear-gradient(135deg, #f5f7fa 70%, #e4e8f0 100%);
}

.view-shop-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
    margin-top: 80px;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
    margin-bottom: 30px;
}

.view-shop-profile-card {
    background: #fff;
    padding: 24px 20px 40px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    display: flex;
    flex-direction: column;
    align-items: center;
    align-self: start;
}

.view-shop-profile-image-wrapper {
    position: relative;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    margin-bottom: 18px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
}

   .view-shop-profile-image-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin-bottom: 18px;
        overflow: visible; 
    }

    .view-shop-shop-logo-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
    }
    
.verified-badge-icon {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 30px;
    height: 30px;
    background-color: #1d9bf0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    z-index: 2;
}

.verified-badge-icon .fas.fa-check {
    color: #fff;
    font-size: 14px;
}


.view-shop-shop-logo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.view-shop-profile-title {
    text-align: center;
    font-weight: 500;
    font-size: 24px;
    margin-bottom: 6px;
    color: #252525;
    line-height: 1.3;
    font-family: 'Montserrat', sans-serif;
}

.view-shop-profile-subtitle {
    font-size: 14px;
    color: #333;
    text-align: center;
    line-height: 1.4;
    font-family: 'Montserrat', sans-serif;
    margin-bottom: 8px;
}

.view-shop-profile-subtitle svg {
    vertical-align: middle;
    margin-right: 4px;
    fill: #505050;
    width: 14px;
    height: 14px;
}

.view-shop-action-container {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    width: 100%;
}

.view-shop-action-icons-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: -30px;
}

.view-shop-action-buttons-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    width: 100%;
    margin-top: 50px;
}

.view-shop-action-icons-container i {
    font-size: 20px;
}

.view-shop-save-icon-wrapper i.fa-bookmark {
    color: #ffc107;
    transition: color 0.3s ease;
}

.view-shop-save-icon-wrapper.saved i.fa-bookmark {
    color: #FFD700 !important;
}

.view-shop-save-shop-container,
.view-shop-message-shop-icon,
.view-shop-report-shop-icon {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    color: #666;
    transition: color 0.2s;
}

.view-shop-save-shop-container:hover,
.view-shop-message-shop-icon:hover,
.view-shop-report-shop-icon:hover {
    color: #333;
}

.view-shop-save-icon-wrapper {
    font-size: 20px;
}

.view-shop-save-icon-wrapper.saved {
    color: #f7b500;
}

.view-shop-message-shop-icon i,
.view-shop-report-shop-icon i {
    font-size: 20px;
}

.view-shop-book-now-btn {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
    padding: 8px 16px;
    font-size: 14px;
    white-space: nowrap;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
}

.view-shop-emergency-request-btn {
    background-color: #dc3545;
    border-color: #dc3545;
    padding: 8px 16px;
    font-size: 14px;
    white-space: nowrap;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    color: white;
}

@media (max-width: 576px) {
    .view-shop-action-buttons-container {
        flex-direction: column;
        gap: 8px;
    }

    .view-shop-book-now-btn,
    .view-shop-emergency-request-btn {
        width: 100% !important;
    }
}

.view-shop-tabs {
    margin-top: 32px;
    width: 100%;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-start;
    padding: 0;
    position: relative;
}

.view-shop-tab-button {
    flex-grow: 1;
    padding: 14px 0;
    font-weight: 600;
    color: #444;
    text-align: center;
    cursor: pointer;
    border: none;
    background: none;
    font-size: 15px;
    position: relative;
    font-family: 'Montserrat', sans-serif;
}

.view-shop-tab-button.active {
    color: #000;
    font-family: 'Montserrat', sans-serif;
}

.view-shop-tab-button.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: #f7b500;
    border-radius: 3px 3px 0 0;
}

.view-shop-tab-panels>section {
    padding: 15px 0;
    outline: none;
    border: none;
    box-shadow: none;
}

#messageContainer {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 12px 25px;
    background-color: #343a40;
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
    font-size: 15px;
    font-weight: 500;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#messageContainer.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.view-shop-contact-details-group {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 15px;
    align-items: flex-start;
}

.view-shop-contact-details-group p {
    margin: 0;
    font-size: 15px;
    color: #4a4a4a;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    text-align: left !important;
}

.view-shop-contact-details-group p i {
    flex-shrink: 0;
}

.view-shop-profile-card .view-shop-social-media-links {
    display: flex;
    justify-content: flex-start;
    gap: 15px;
    margin-top: 5px;
    margin-bottom: 5px;
    align-self: flex-start;
    width: 100%;
}

.view-shop-profile-card .view-shop-social-media-links a {
    color: #000;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
}

.view-shop-profile-card .view-shop-social-media-links a i {
    font-size: 16px;
}

.view-shop-profile-card .view-shop-social-media-links .social-link {
    display: inline-block;
    margin: 10px;
    font-size: 16px;
    text-decoration: none;
    color: #333;
}

.view-shop-profile-card .view-shop-social-media-links .social-link i {
    margin-right: 8px;
    font-size: 18px;
}

.view-shop-description {
    margin-top: 5px;
    text-align: left;
    align-self: flex-start;
    width: 100%;
}

.view-shop-description p {
    margin: 0;
    font-size: 15px;
    color: #444;
    line-height: 1.6;
}

.view-shop-right-panel {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-top: -30px;
}

.brand-vehicle,
.location-map,
.photos-shop {
    margin-top: -25px !important;
}

.view-shop-section {
    background: #fff;
    padding: 24px 24px 32px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
}

.view-shop-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eee;
    color: #333;
    font-family: 'Montserrat', sans-serif;
}

.view-shop-section-header svg {
    width: 20px;
    height: 20px;
    fill: #666;
}

.view-shop-categories-list {
    margin-top: 16px;
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
}

.view-shop-category-item {
    font-family: 'Montserrat', sans-serif;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    color: #c48c00;
}

.view-shop-category-item svg {
    width: 18px;
    height: 18px;
    fill: #ffc107;
    filter: drop-shadow(0 0 0.2px #daaa00);
}

.view-shop-location-text {
    margin-top: 16px;
    font-size: 15px;
    color: #444;
    display: flex;
    align-items: center;
    gap: 12px;
    line-height: 1.4;
}

.view-shop-location-text svg {
    width: 18px;
    height: 18px;
    fill: #777;
    flex-shrink: 0;
}

@media (max-width: 1024px) {
    .view-shop-container {
        grid-template-columns: 1fr;
        padding: 0 15px;
        margin-top: 110px;
    }

    .view-shop-profile-card {
        max-width: 100%;
        margin: 0 auto 20px;
        padding: 20px;
    }

    .view-shop-right-panel {
        padding: 0 15px;
        gap: 15px;
    }

    .view-shop-section {
        padding: 20px;
    }

    .view-shop-profile-subtitle {
        text-align: center;
        padding: 0 10px;
    }

    .view-shop-contact-details-group p {
        margin-left: 0;
        text-align: left !important;
        width: 100%;
        justify-content: flex-start;
    }
    
    .view-shop-profile-card .view-shop-social-media-links {
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .view-shop-profile-card .view-shop-social-media-links .social-link {
        margin: 5px;
    }

    .view-shop-shop-description {
        padding: 0 10px;
    }
}

.view-shop-write-review-btn-container {
    text-align: center;
    margin-bottom: 15px;
    padding-top: 10px;
}

.view-shop-write-review-btn {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
    min-height: 35px;
    line-height: 1.2;
    font-weight: 500;
    transition: none;
    text-decoration: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    color: #0d6efd;
    font-family: sans-serif;
    outline: none;
    box-shadow: none;
    font-size: 0.95em;
}

.view-shop-write-review-btn:hover {
    text-decoration: underline;
}

.view-shop-write-review-btn i {
    color: #0d6efd;
    margin-right: 5px;
}

.view-shop-overall-rating-section-card {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 15px;
    border: 1px solid #eee;
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.view-shop-overall-rating-summary-and-breakdown {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.view-shop-overall-rating-main {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.view-shop-overall-rating-score {
    font-size: 3.5em;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.view-shop-overall-rating-details {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}

.view-shop-overall-rating-text {
    font-size: 1em;
    color: #666;
}

.view-shop-rating-breakdown {
    flex-grow: 1;
    min-width: 180px;
}

.view-shop-rating-row {
    display: flex;
    align-items: center;
    margin-bottom: 3px;
}

.view-shop-rating-label {
    width: 18px;
    font-size: 0.85em;
    color: #555;
    text-align: right;
    margin-right: 4px;
    white-space: nowrap;
}

.view-shop-progress {
    flex-grow: 1;
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.view-shop-progress-bar {
    background-color: #f7b500;
    height: 100%;
    border-radius: 4px;
}

.view-shop-rating-count {
    width: 35px;
    text-align: left;
    margin-left: 8px;
    font-size: 0.85em;
    color: #555;
    white-space: nowrap;
}

.view-shop-review-list {
    margin-top: 20px;
}

.view-shop-review-item {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    margin-bottom: 10px;
    border: 1px solid #eee;
}

.view-shop-review-item:last-child {
    margin-bottom: 0;
}

.view-shop-review-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 8px;
}

.view-shop-reviewer-profile-pic {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.view-shop-reviewer-text-info {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    align-items: flex-start;
}

.view-shop-reviewer-name {
    font-weight: 600;
    color: #333;
    font-size: 1em;
    margin-bottom: 2px;
}

.view-shop-review-date {
    font-size: 0.8em;
    color: #777;
    margin-top: 2px;
}

.view-shop-star-rating {
    color: #f7b500;
    font-size: 1.1em;
    margin-bottom: 5px;
}

.view-shop-star-rating .far.fa-star {
    color: #ddd;
}

.view-shop-review-comment {
    color: #444;
    line-height: 1.5;
    margin-bottom: 10px;
    font-size: 0.95em;
    text-align: left;
}

.view-shop-shop-response {
    margin-top: 10px;
    padding: 12px;
    background: #e9f5ff;
    border-left: 4px solid #007bff;
    border-radius: 4px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    text-align: left;
}

.view-shop-shop-response div {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
}

.view-shop-shop-response strong {
    font-size: 13px;
    color: #0056b3;
}

.view-shop-shop-response span {
    font-size: 10px;
    color: #6c757d;
}

.view-shop-shop-response p {
    margin: 0;
    font-size: 13px;
    color: #343a40;
    line-height: 1.4;
}

.view-shop-review-actions {
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: left;
}

.view-shop-like-button {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #6c757d;
    transition: color 0.2s ease;
}

.view-shop-like-button:hover {
    color: #0d6efd;
}

.view-shop-like-button .fas.fa-thumbs-up {
    color: #0d6efd;
}

.view-shop-pagination-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 20px;
}

.view-shop-pagination-nav-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #6b7280;
}

.view-shop-pagination-nav-btn:hover:not(:disabled) {
    background-color: #f0f0f0;
    border-color: #d1d5db;
}

.view-shop-pagination-nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.view-shop-pagination-nav-btn svg {
    width: 16px;
    height: 16px;
}

.view-shop-pagination-current {
    min-width: 45px;
    height: 40px;
    border: none;
    background: #ffc107;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 15px;
    cursor: default;
    margin: 0 4px;
}

#view-shop-remainingCount {
    min-width: 45px;
    height: 40px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-weight: 500;
    font-size: 13px;
}

@media (max-width: 768px) {
    .view-shop-overall-rating-summary-and-breakdown {
        flex-direction: column;
        align-items: flex-start;
    }

    .view-shop-overall-rating-main {
        margin-bottom: 10px;
    }

    .view-shop-rating-breakdown {
        width: 100%;
        margin-top: 15px;
    }

    .view-shop-review-header {
        flex-wrap: wrap;
    }

    .view-shop-reviewer-text-info {
        flex-direction: column;
        align-items: flex-start;
        flex-grow: 1;
    }

    .view-shop-reviewer-name {
        white-space: normal;
    }

    .view-shop-star-rating {
        margin-left: 0;
        width: 100%;
        order: unset;
        text-align: left;
    }

    .view-shop-review-date {
        margin-left: 0;
        width: 100%;
        order: unset;
        text-align: left;
    }
}

.view-shop-contact-details-group .d-flex.flex-wrap {
    display: inline-flex !important;
    flex-wrap: nowrap;
    white-space: nowrap;
}

.view-shop-contact-details-group .dropdown-toggle {
    white-space: nowrap;
    transition: all 0.2s ease;
}

.view-shop-contact-details-group .dropdown-toggle:hover {
    text-decoration: underline !important;
}

@media (max-width: 767.98px) {
    .view-shop-contact-details-group .dropdown-menu {
        left: 10% !important;
        transform: translateX(-50%) !important;
        right: auto !important;
        max-width: 90vw;
        width: auto;
        white-space: nowrap;
    }

    .view-shop-contact-details-group .dropdown-item {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        white-space: normal;
    }
}

.dropdown-item {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.dropdown-item .text-success {
    color: #198754 !important;
}

.dropdown-item .text-danger {
    color: #dc3545 !important;
}

@media (max-width: 480px) {
    .view-shop-overall-rating-score {
        font-size: 3em;
    }

    .view-shop-review-item {
        padding: 15px;
    }

    .view-shop-reviewer-profile-pic {
        width: 40px;
        height: 40px;
    }

    .view-shop-reviewer-name {
        font-size: 1em;
    }

    .view-shop-review-date {
        font-size: 0.75em;
    }

    .view-shop-star-rating {
        font-size: 1em;
    }

    .view-shop-shop-response {
        padding: 10px;
    }

    .view-shop-shop-response p {
        font-size: 13px;
    }

    .view-shop-like-button {
        font-size: 12px;
        gap: 4px;
    }
}

html {
    box-sizing: border-box;
}

.view-shop-gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
  margin-top: 15px;
}

.view-shop-gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    aspect-ratio: 1;
    cursor: pointer;
    transition: transform 0.3s ease;
    background-color: #f8f9fa;
}

.view-shop-gallery-item:hover {
    transform: scale(1.03);
}

.view-shop-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.view-shop-gallery-item img:hover {
    filter: brightness(1.05);
}

@media (max-width: 992px) {
    .view-shop-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }
}

@media (max-width: 768px) {
    .view-shop-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 8px;
    }
}

@media (max-width: 576px) {
    .view-shop-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 6px;
    }
}

.view-shop-gallery-item::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%),
        linear-gradient(-45deg, #f8f9fa 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #f8f9fa 75%),
        linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    opacity: 0.5;
}

.view-shop-gallery-item img[src]::after {
    opacity: 0;
}

.view-shop-modal-content {
    border: none;
    border-radius: 12px;
}

.view-shop-modal-body {
    padding: 0;
}

#view-shop-modalImage {
    border-radius: 0 0 12px 12px;
    max-height: 70vh;
    width: auto;
    margin: 0 auto;
    display: block;
}

.modal-actions {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 1050;
}

.download-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
}

.download-btn:hover {
    background-color: #0069d9;
}

.gallery-item-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 8px;
}

.gallery-item-container:hover .gallery-item-overlay {
    opacity: 1;
}

.gallery-view-icon {
    color: white;
    font-size: 2rem;
    transition: transform 0.3s ease;
}

.gallery-item-container:hover .gallery-view-icon {
    transform: scale(1.2);
}

.chat-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 500px;
    background-color: #ccc;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    display: none;
    flex-direction: column;
    z-index: 1000;
    transition: all 0.3s ease-in-out;
    max-width: 95vw;
    max-height: 90vh;
    overflow: hidden;
    transform: translateY(20px);
    opacity: 0;
}

.chat-container.active {
    display: flex;
    transform: translateY(0);
    opacity: 1;
}

.chat-header {
    padding: 15px 20px;
    background: #ffffff;
    color: #000;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.5);
}

.chat-header .user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.chat-header .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.8);
}

.chat-header h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.close-chat {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    font-size: 20px;
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.close-chat:hover {
    opacity: 1;
}

.chat-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background-color: #f9f9f9;
    display: flex;
    flex-direction: column;
    gap: 12px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    scroll-behavior: smooth;
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.chat-messages::-webkit-scrollbar {
    display: none;
}

.chat-input-area {
    padding: 15px;
    border-top: 1px solid #e0e0e0;
    background-color: white;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    flex-shrink: 0;
}

.chat-input {
    display: block;
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    border-radius: 25px;
    padding: 5px;
    background-color: white;
    border: 1px solid #ccc;
}

.message-input {
    flex: 1;
    padding: 8px 12px;
    border: none;
    outline: none;
    background: transparent;
    font-size: 15px;
    box-shadow: none;
}

.message-input:focus {
    outline: none;
    border: none;
    box-shadow: none;
}

.attachment-btn {
    padding: 8px;
    cursor: pointer;
    color: #6c757d;
    font-size: 20px;
    background: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.attachment-btn:hover {
    color: #ffc107;
}

.send-btn {
    background: linear-gradient(to right, #ffc107, #e0a800);
    color: white;
    border: none;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
}

.send-btn:hover {
    background: linear-gradient(to right, #e0a800, #d39e00);
    box-shadow: 0 4px 10px rgba(255, 193, 7, 0.4);
}

.send-btn:active {
    box-shadow: 0 1px 3px rgba(255, 193, 7, 0.2);
}

.message-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 0;
}

.message-group.me {
    align-items: flex-end;
}

.message-group.other {
    align-items: flex-start;
}

.message {
    max-width: 85%;
    padding: 10px 15px;
    border-radius: 20px;
    position: relative;
    word-wrap: break-word;
    overflow-wrap: break-word;
    font-size: 15px;
    line-height: 1.4;
}

.message.me {
    background-color: #007bff;
    color: white;
    border-bottom-right-radius: 8px;
}

.message.other {
    background-color: #e2e6ea;
    color: #333;
    border-bottom-left-radius: 8px;
}

.message-time {
    font-size: 11px;
    color: rgba(0, 0, 0, 0.6);
    margin-top: 3px;
}

.message.me .message-time {
    color: rgba(255, 255, 255, 0.8);
    text-align: right;
}

.message img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin-top: 8px;
}

.chat-messages .message-image-preview {
    max-width: 200px;
    max-height: 200px;
    width: auto;
    height: auto;
    border-radius: 8px;
    margin-top: 8px;
    cursor: pointer;
    transition: transform 0.2s ease;
    border: 1px solid #e0e0e0;
}

@media (max-width: 576px) {
    .chat-messages .message-image-preview {
        max-width: 150px;
        max-height: 150px;
    }
}

.attachment-preview {
    position: relative;
    display: inline-block;
}

.attachment-preview-container {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
    padding: 8px;
    border-top: 1px solid #f0f0f0;
    background-color: #fcfcfc;
    border-radius: 8px;
    max-height: 120px;
    overflow-y: auto;
    width: 100%;
}

.attachment-preview {
    position: relative;
    display: inline-block;
    flex-shrink: 0;
}

.attachment-preview img {
    max-width: 90px;
    max-height: 90px;
    width: auto;
    height: auto;
    border-radius: 8px;
    object-fit: contain;
    border: 1px solid #ddd;
    background-color: #f5f5f5;
}

.remove-attachment {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    z-index: 1;
    border: 2px solid white;
    transition: background-color 0.2s ease;
}

.remove-attachment:hover {
    background-color: #c82333;
}

.modal-actions {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
}

.download-btn {
    background-color: #ffc107;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.message-image-container {
    max-width: 100%;
    margin: 0 auto;
    position: relative;
}

.message-image {
    max-width: 100%;
    max-height: 250px;
    height: auto;
    width: auto;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
    object-fit: contain;
    background-color: #f5f5f5;
    border: 1px solid #e0e0e0;
}

.message.me .message-image {
    border-color: rgba(255, 255, 255, 0.2);
}

.message.other .message-image {
    border-color: rgba(0, 0, 0, 0.1);
}

.message-container {
    max-width: 80%;
    margin-bottom: 8px;
    display: flex;
    position: relative;
}

.message-outgoing {
    justify-content: flex-end;
    margin-left: auto;
}

.message-outgoing .message-bubble {
    background-color: #007bff;
    color: white;
    border-radius: 18px 18px 4px 18px;
}

.message-incoming {
    justify-content: flex-start;
    margin-right: auto;
}

.message-incoming .message-bubble {
    background-color: #e9ecef;
    color: #212529;
    border-radius: 18px 18px 18px 4px;
}

.message-bubble {
    padding: 10px 15px;
    position: relative;
    word-wrap: break-word;
    font-size: 15px;
    line-height: 1.4;
    max-width: 100%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-time {
    display: block;
    font-size: 11px;
    margin-top: 4px;
    opacity: 0.8;
}

.message-outgoing .message-time {
    color: rgba(255, 255, 255, 0.8);
    text-align: right;
}

.message-incoming .message-time {
    color: rgba(0, 0, 0, 0.6);
    text-align: left;
}

.message-image-container {
    max-width: 100%;
    margin: 0 auto;
}

.message-image {
    max-width: 100%;
    max-height: 250px;
    height: auto;
    width: auto;
    border-radius: 8px;
    display: block;
    object-fit: contain;
    background-color: #f5f5f5;
    border: 1px solid #e0e0e0;
}

.message-outgoing .message-image {
    border-color: rgba(255, 255, 255, 0.2);
}

.message-incoming .message-image {
    border-color: rgba(0, 0, 0, 0.1);
}

.message-image-preview {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    margin-top: 8px;
    cursor: pointer;
    transition: transform 0.2s ease;
    border: 1px solid #e0e0e0;
}

.message-image-preview:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.message.me .message-image-preview {
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.message.other .message-image-preview {
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.view-shop-map-container {
    position: relative;
}

.view-shop-map-container #map {
    height: 500px;
    width: 100%;
    border-radius: 8px;
    background-color: #e9ecef;
}

.map-directions-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid #ccc;
    color: #333;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.map-directions-btn:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-1px);
    border-color: #888;
    color: #000;
}

.map-directions-btn i {
    font-size: 16px;
    margin-right: 6px;
}

@media (max-width: 768px) {
    .view-shop-map-container #map {
        height: 300px;
    }
}

.no-map-available {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 8px;
    color: #6c757d;
}

.brand-logos-list {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.brand-logo-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    min-width: 100px;
    text-align: center;
    font-size: 12px;
    color: #333;
}

.brand-logo-img {
    width: 75px;
    height: 75px;
    object-fit: contain;
    margin-bottom: 5px;
    border-radius: 4px;
}

.brand-name {
    word-break: break-word;
    margin-top: 5px;
    font-family: 'Montserrat', sans-serif;
}

.view-shop-write-review-btn {
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.view-shop-write-review-btn:hover {
    text-decoration: none;
}

.view-shop-write-review-btn:focus,
.view-shop-write-review-btn:active {
    outline: none;
    box-shadow: none;
}

.view-shop-write-review-btn i {
    font-size: 12px;
}

.gallery-item-container {
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease;
    border-radius: 8px;
}

.gallery-item-container:hover {
    transform: scale(1.02);
}

.gallery-item-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.gallery-item-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 8px;
}

.gallery-item-container:hover .gallery-item-overlay {
    opacity: 1;
}

.gallery-view-icon {
    color: white;
    font-size: 2rem;
    transition: transform 0.3s ease;
}

.gallery-item-container:hover .gallery-view-icon {
    transform: scale(1.2);
}

#floatingImageViewer {
    transition: opacity 0.3s ease;
}

#floatingImageViewer.show {
    display: block !important;
    opacity: 1;
}

#closeFloatingViewer:hover,
#downloadFloatingImage:hover {
    background-color: rgba(255, 255, 255, 0.3) !important;
    transform: scale(1.05);
    transition: all 0.2s ease;
}

#downloadFloatingImage {
    transition: all 0.2s ease;
}

@media (max-width: 576px) {
    #downloadFloatingImage span {
        display: none;
    }

    #downloadFloatingImage {
        padding: 8px !important;
    }
}

.automated-message-container {
    display: flex;
    justify-content: flex-start;
    margin: 10px;
}

.automated-message-bubble {
    max-width: 80%;
    background-color: #f1f1f1;
    border-radius: 18px;
    padding: 12px 16px;
    color: #333;
    font-size: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.quick-replies-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.quick-reply-btn {
    background-color: #e9f3ff;
    border: 1px solid #0084ff;
    border-radius: 18px;
    color: #0084ff;
    padding: 6px 12px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.quick-reply-btn:hover {
    background-color: #d8e9ff;
}

.quick-reply-btn:active {
    background-color: #c8e0ff;
}

.typing-indicator {
    display: flex;
    justify-content: flex-start;
    margin: 10px;
}

.typing-bubble {
    background-color: #f1f1f1;
    border-radius: 18px;
    padding: 12px 16px;
}

.typing-dots {
    display: flex;
    align-items: center;
    height: 17px;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    margin: 0 2px;
    background-color: #666;
    border-radius: 50%;
    display: inline-block;
    animation: typingAnimation 1.4s infinite ease-in-out both;
}

.typing-dots span:nth-child(1) {
    animation-delay: 0s;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typingAnimation {

    0%,
    80%,
    100% {
        transform: scale(0);
    }

    40% {
        transform: scale(1.0);
    }
}

.message-container[data-is-automated="1"] .message-bubble {
    background-color: #f0f7ff;
    border: 1px solid #d0e3ff;
}

.quick-reply-btn {
    background-color: #e9f3ff;
    border: 1px solid #0084ff;
    border-radius: 18px;
    color: #0084ff;
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    margin: 4px;
    white-space: normal;
    word-break: break-word;
    max-width: 100%;
    text-align: center;
}

.message-bubble {
    position: relative;
    padding-bottom: 25px;
}

.message-options {
    position: absolute;
    top: -15px;
    right: 5px;
    display: none;
    background: white;
    border-radius: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    z-index: 5;
}

.message-bubble:hover .message-options {
    display: block;
}

.react-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 16px;
    padding: 4px 8px;
    opacity: 0.7;
}

.react-btn:hover {
    opacity: 1;
}

.reaction-picker {
    position: absolute;
    display: none;
    background: white;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    padding: 5px;
    z-index: 100;
    display: flex;
    gap: 5px;
}

.reaction-picker .emoji {
    font-size: 24px;
    cursor: pointer;
    padding: 5px;
    transition: transform 0.1s ease-in-out;
}

.reaction-picker .emoji:hover {
    transform: scale(1.3);
}

.reactions-container {
    position: absolute;
    bottom: -10px;
    right: 10px;
    display: flex;
    gap: 4px;
    background: #f0f2f5;
    border-radius: 10px;
    padding: 2px 5px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    font-size: 14px;
}

.message-outgoing .reactions-container {
    background: #e7f3ff;
}

.reaction-item {
    display: flex;
    align-items: center;
}

.reaction-item span {
    font-size: 12px;
    color: #65676b;
    margin-left: 2px;
}

.quick-reply-btn:hover {
    background-color: #d8e9ff;
    transform: translateY(-1px);
}

.quick-reply-btn:active {
    background-color: #c8e0ff;
    transform: translateY(0);
}

.message-status {
    font-size: 11px;
    color: #666;
    margin-top: 4px;
    text-align: right;
}

.message-status.sending {
    color: #666;
}

.message-status.failed {
    color: #ff4444;
    cursor: pointer;
    text-decoration: underline;
}

.accordion-button:focus {
    box-shadow: none;
}

.accordion-button:not(.collapsed) {
    color: #212529;
    background-color: #ffffff;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
}

.accordion-button:not(.collapsed)::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.services-grid .accordion,
.services-grid .accordion-item {
    height: 100%;
}

.badge-container {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 5px;
    z-index: 2;
}

.shop-badge {
    padding: 0 5px;
    border-radius: 8px;
    font-size: 0.70rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    white-space: nowrap;
}

.shop-badge.top-rated i,
.shop-badge.top-booking i {
    font-size: 0.70rem !important;
}

.shop-badge.top-rated {
    background-color: #ffc107;
    color: #212529;
}

.shop-badge.top-booking {
    background-color: #00A3BF;
    color: white;
}

.view-shop-section {
    margin: 2rem 0;
}

.view-shop-section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
}

.services-list-wrapper {
    position: relative;
}

.services-list-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
}

.service-name-badge {
    background-color: #f1f3f5;
    color: #495057;
    padding: 0.6em 1em;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.85rem;
}

.service-name-badge:hover {
    background-color: #e9ecef;
}

.badge-text {
    font-size: 0.80rem;
}

.badge-separator-icon {
    color: #adb5bd;
    font-size: 0.8rem;
}

.services-list-container:not(.is-expanded) .service-name-badge:nth-child(n + 4) {
    display: none;
}

.toggle-services-btn {
    background-color: transparent;
    border: none;
    color: #0d6efd;
    font-weight: 600;
    cursor: pointer;
    padding: 0.5rem 0;
    margin-top: 0.75rem;
    font-size: 0.9rem;
}

.toggle-services-btn:hover {
    text-decoration: underline;
}

.no-items {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
    border: 2px dashed #e9ecef;
    border-radius: 8px;
}

@media (max-width: 767.98px) {
    .view-shop-contact-details-group .dropdown-menu {
        left: 10% !important;
        transform: translateX(-50%) !important;
        right: auto !important;
        max-width: 90vw;
        width: auto;
        white-space: nowrap;
    }

    .view-shop-contact-details-group .dropdown-item {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        white-space: normal;
    }
}

.view-shop-gallery-item::before,
.view-shop-gallery-item::after {
    display: none !important;
    content: none !important;
    background: none !important;
}

.view-shop-gallery-item img {
    opacity: 1 !important;
    filter: none !important;
    z-index: 1;
}

.view-shop-gallery-item {
    background: none !important;
}
.shop-status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
    margin-bottom: 5px;
    text-align: center;
}

.shop-status-badge.temporarily-closed {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.shop-status-badge.permanently-closed {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.shop-status-badge i {
    margin-right: 5px;
}
</style>
</head>
<body>

    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <main class="view-shop-container" role="main" aria-label="Listing detail and location details">
        <section class="view-shop-profile-card" aria-label="Business profile and contact details">
            <div class="view-shop-profile-image-wrapper" aria-hidden="true">
                <?php
                $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                $final_logo_url = $default_logo_url;
                if (!empty($shop['shop_logo'])) {
                    $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
                    $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                    if (file_exists($logo_file_path)) {
                        $final_logo_url = BASE_URL . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                    }
                }
                ?>
                <img src="<?php echo htmlspecialchars($final_logo_url); ?>" alt="Shop cover" class="view-shop-shop-logo-img">
                <div class="verified-badge-icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <?php
            $user_id = $_SESSION['user_id'] ?? null;
            ?>

            <?php if ($topRated || $mostBooked): ?>
                <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                    <?php if ($topRated): ?>
                        <div class="shop-badge top-rated" data-bs-toggle="tooltip" title="Top Rated">
                            <i class="fas fa-star" style="font-size: 1rem;"></i> Top Rated
                        </div>
                    <?php endif; ?>
                    <?php if ($mostBooked): ?>
                        <div class="shop-badge top-booking" data-bs-toggle="tooltip" title="Most Booked">
                            <i class="fas fa-calendar-check" style="font-size: 1rem;"></i> Most Booked
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <h1 class="view-shop-profile-title" id="profileTitle"><?php echo htmlspecialchars($shop['shop_name']); ?></h1>

            <?php
$shop_status = $shop['shop_status'] ?? 'open';

if ($shop_status == 'temporarily_closed') :
?>
    <div class="shop-status-badge temporarily-closed">
        <i class="fas fa-exclamation-triangle"></i> Temporarily Closed
    </div>
<?php elseif ($shop_status == 'permanently_closed') : ?>
    <div class="shop-status-badge permanently-closed">
        <i class="fas fa-store-slash"></i> Permanently Closed
    </div>
<?php endif; ?>

            <p class="view-shop-profile-subtitle" id="profileAddress" aria-describedby="profileTitle">
                <i class="fas fa-map-marker-alt" style=" font-size: 14px;"></i>
                <?php echo $display_address; ?>
            </p>

            <?php if (!empty($facebook) || !empty($instagram) || !empty($website)): ?>
                <div class="view-shop-social-media-links text-center">
                    <?php if (!empty($instagram)): ?>
                        <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" class="social-link">
                            <i class="fab fa-instagram" style="color: #E1306C;"></i> Instagram
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($facebook)): ?>
                        <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" class="social-link">
                            <i class="fab fa-facebook" style="color: #1877F2;"></i> Facebook
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($website)): ?>
                        <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="social-link">
                            <i class="fas fa-globe" style="color: #0066CC;"></i> Website
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty(trim($shop['description'] ?? ''))) { ?>
                <div class="view-shop-shop-description">
                    <p><?php echo nl2br(htmlspecialchars($shop['description'])); ?></p>
                </div>
            <?php } ?>

           <div class="view-shop-action-container">
           <div class="view-shop-action-icons-container">

   <div class="view-shop-save-shop-container" title="Favorites" onclick="handleActionClick('favorites', '<?php echo $shop_slug; ?>', '<?php echo urlencode($shop['shop_name']); ?>')" data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
       <i class="bi bi-bookmark"></i>
   </div>

   <div class="view-shop-message-shop-icon" title="Message" onclick="handleActionClick('message', '<?php echo $shop_slug; ?>', '<?php echo urlencode($shop['shop_name']); ?>')" data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
       <i class="bi bi-chat-dots"></i>
   </div>

   <div class="view-shop-report-shop-icon" title="Report" onclick="handleActionClick('report', '<?php echo $shop_slug; ?>', '<?php echo urlencode($shop['shop_name']); ?>')" data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
       <i class="bi bi-flag"></i>
   </div>

</div>
<?php
$is_any_shop_owner = false;
if (isset($_SESSION['user_id'])) {
    $profileCheckStmt = $conn->prepare("SELECT profile_type FROM users WHERE id = ? LIMIT 1");
    if ($profileCheckStmt) {
        $profileCheckStmt->bind_param("i", $_SESSION['user_id']);
        $profileCheckStmt->execute();
        $profileResult = $profileCheckStmt->get_result();
        if ($profileResult->num_rows > 0) {
            $user = $profileResult->fetch_assoc();
            $is_any_shop_owner = ($user['profile_type'] === 'owner');
        }
        $profileCheckStmt->close();
    }
}

$hasActionButtons = ($shop['show_book_now'] || $shop['show_emergency']) && $shop_status == 'open';
?>

<?php if ($hasActionButtons) : ?>
    <div class="view-shop-action-buttons-container">
        <?php if ($shop['show_book_now']) : ?>
            <a href="#" class="view-shop-book-now-btn text-white" style="font-weight: 500;" onclick="handleActionClick('book', '<?php echo $shop_slug; ?>', '<?php echo urlencode($shop['shop_name']); ?>')" data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
                Book Now
            </a>
        <?php endif; ?>

        <?php if ($shop['show_emergency']) : ?>
            <a href="#" class="view-shop-emergency-request-btn" style="font-weight: 500;" onclick="handleActionClick(
                    'emergency',
                    '<?php echo $shop_slug; ?>',
                    '<?php echo urlencode($shop['shop_name']); ?>',
                    '<?php echo $shop['phone']; ?>'
                )" data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
                Emergency
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
            <nav role="tablist" class="view-shop-tabs" aria-label="Profile tabs navigation">
                <button class="view-shop-tab-button active" aria-selected="true" aria-controls="contactDetails" id="tabContact" role="tab" tabindex="0">Overview</button>
                <button class="view-shop-tab-button" aria-selected="false" aria-controls="reviewsTab" id="tabReviews" role="tab" tabindex="-1">Reviews (<?php echo $total_reviews; ?>)</button>
            </nav>
            <div class="view-shop-tab-panels">
                <section id="contactAndBusinessInfo" role="tabpanel" aria-labelledby="tabContact">
                    <div class="view-shop-contact-details-group">

                        <h5 class="mb-2 fw-semibold">Contact Information</h5>
                        <p><i class="fas fa-phone" style="color: #000000;"></i> <?= htmlspecialchars($shop['phone']); ?></p>
                        <p><i class="fas fa-envelope" style="color: #000000;"></i> <?= htmlspecialchars($shop['email']); ?></p>

                        <hr class="my-3" style="border-top: 1px solid #ccc;">

                        <h5 class="mb-2 fw-semibold" style="margin-top: -20px;">Business Information</h5>
                        <p><i class="fas fa-certificate"></i> <?= htmlspecialchars($shop['years_operation']); ?> Years in Operation</p>

                        <?php
                        if (!empty($shop['opening_time_am']) && !empty($shop['closing_time_am']) && !empty($shop['days_open'])) {
                            date_default_timezone_set('Asia/Manila');

                            $days_list = array_map('trim', explode(',', $shop['days_open']));
                            $schedule = [];

                            foreach ($days_list as $day) {
                                $normalized_day = ucfirst(strtolower($day));
                                if (!empty($normalized_day)) {
                                    $schedule[$normalized_day] = [
                                        'open_am'  => $shop['opening_time_am'],
                                        'close_am' => $shop['closing_time_am'],
                                        'open_pm'  => $shop['opening_time_pm'] ?? null,
                                        'close_pm' => $shop['closing_time_pm'] ?? null,
                                    ];
                                }
                            }

                            $current_day = date('l');
                            $current_time_str = date('H:i');
                            $is_open = false;

                            if (isset($schedule[$current_day])) {
                                $todays_schedule = $schedule[$current_day];
                                $current_timestamp = strtotime($current_time_str);

                                $open_am_ts = strtotime($todays_schedule['open_am']);
                                $close_am_ts = strtotime($todays_schedule['close_am']);
                                if ($current_timestamp >= $open_am_ts && $current_timestamp <= $close_am_ts) {
                                    $is_open = true;
                                }

                                if (!$is_open && !empty($todays_schedule['open_pm'])) {
                                    $open_pm_ts = strtotime($todays_schedule['open_pm']);
                                    $close_pm_ts = strtotime($todays_schedule['close_pm']);
                                    if ($current_timestamp >= $open_pm_ts && $current_timestamp <= $close_pm_ts) {
                                        $is_open = true;
                                    }
                                }
                            }

                            $current_status = $is_open ? 'Open' : 'Closed';
                            $status_class = $is_open ? 'bg-success' : 'bg-danger';
                        ?>
                        
                               
                                <?php if ($shop_status == 'open'): ?>
<div class="d-flex align-items-center flex-wrap">
    <div class="d-flex align-items-center me-2">
        <i class="fas fa-clock me-1"></i>
        <span class="badge ms-2 <?= $status_class ?>">
            <?= $current_status ?>
        </span>
    </div>
    <button class="view-schedule btn btn-sm btn-link p-0 text-decoration-none" type="button" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View Full Schedule">
        View Schedule
    </button>
</div>
<?php endif; ?>
                       
                        <?php
                        }
                        ?>
                    </div>
                </section>

                <section id="reviewsTab" role="tabpanel" aria-labelledby="tabReviews" tabindex="0" aria-hidden="true" hidden>
                    <div class="view-shop-write-review-btn-container">
                        <button type="button" class="btn view-shop-write-review-btn"
                            data-shop-name="<?php echo htmlspecialchars($shop['shop_name']); ?>"
                            data-shop-id="<?php echo htmlspecialchars($shop['id']); ?>"
                            onclick="handleActionClick('review', '<?php echo $shop_slug; ?>', '<?php echo urlencode($shop['shop_name']); ?>')"
                            data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
                            <i class="fas fa-plus"></i>
                            <span style="color: #000000;">WRITE A REVIEW</span>
                        </button>
                    </div>
                    <div class="view-shop-overall-rating-section-card">
                        <div class="view-shop-overall-rating-summary-and-breakdown">
                            <div class="view-shop-overall-rating-main">
                                <div class="view-shop-overall-rating-score"><?php echo $average_rating; ?></div>
                                <div class="view-shop-overall-rating-details">
                                    <div class="view-shop-star-rating">
                                        <?php include 'view-details-backend/rating-number.php'; ?>
                                    </div>
                                    <div class="view-shop-overall-rating-text">Based on <?php echo $total_reviews; ?> reviews</div>
                                </div>
                            </div>
                            <div class="view-shop-rating-breakdown">
                                <?php include 'view-details-backend/rating-bar.php'; ?>
                            </div>
                        </div>
                    </div>
                    <div id="reviewsContainer">
                        <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                            <div class="view-shop-review-list">
                                <?php
                                $reviews_result->data_seek(0);
                                while ($review = $reviews_result->fetch_assoc()):
                                    $stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM review_likes WHERE review_id = ?");
                                    $stmt->bind_param("i", $review['id']);
                                    $stmt->execute();
                                    $like_count = $stmt->get_result()->fetch_assoc()['like_count'];
                                    $user_liked = false;
                                    if (isset($_SESSION['user_id'])) {
                                        $stmt = $conn->prepare("SELECT id FROM review_likes WHERE review_id = ? AND liked_by_user_id = ?");
                                        $stmt->bind_param("ii", $review['id'], $_SESSION['user_id']);
                                        $stmt->execute();
                                        $user_liked = $stmt->get_result()->num_rows > 0;
                                    }
                                    $response_data = null;
                                    $stmt = $conn->prepare("SELECT rr.*, u.fullname as shop_owner_name FROM respond_reviews rr JOIN users u ON rr.shop_owner_id = u.id WHERE rr.review_id = ?");
                                    $stmt->bind_param("i", $review['id']);
                                    $stmt->execute();
                                    $response_result = $stmt->get_result();
                                    if ($response_result->num_rows > 0) {
                                        $response_data = $response_result->fetch_assoc();
                                    }
                                ?>
                                    <div class="view-shop-review-item" data-review-id="<?php echo $review['id']; ?>">
                                        <div class="view-shop-review-header">
                                            <img src="<?php echo BASE_URL; ?>/assets/img/profile/<?php echo !empty($review['profile_picture']) ? htmlspecialchars($review['profile_picture']) : 'profile-user.png'; ?>" alt="<?php echo htmlspecialchars($review['fullname']); ?>" class="view-shop-reviewer-profile-pic">
                                            <div class="view-shop-reviewer-text-info">
                                                <div class="view-shop-reviewer-name"><?php echo htmlspecialchars($review['fullname']); ?></div>
                                                <div class="view-shop-star-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++) {
                                                        echo ($i <= $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                                    } ?>
                                                </div>
                                                <div class="view-shop-review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="view-shop-review-comment">
                                            <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                        </div>
                                        <?php if ($response_data): ?>
                                            <div class="view-shop-shop-response">
                                                <div><i class="fas fa-reply"></i> <strong><?php echo htmlspecialchars($response_data['shop_owner_name']); ?></strong> <span><?php echo date('F j, Y', strtotime($response_data['created_at'])); ?></span></div>
                                                <p><?php echo htmlspecialchars($response_data['response']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <div class="view-shop-review-actions">
                                            <button class="view-shop-like-button"
                                                data-review-id="<?php echo $review['id']; ?>"
                                                data-review-owner-id="<?php echo $review['user_id']; ?>"
                                                onclick="handleActionClick('like', '<?php echo $shop_slug; ?>', '<?php echo $review['id']; ?>')"
                                                data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
                                                <i class="<?php echo $user_liked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                                <span class="like-count"><?php echo $like_count; ?></span> <?php echo $like_count != 1 ? 'Likes' : 'Like'; ?>
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p style="text-align: center; font-size: 14px; color: #6c757d;">No reviews yet. Be the first to leave a review!</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($total_reviews > 5): ?>
                        <div class="view-shop-pagination-controls">
                            <button id="prevBtn" class="view-shop-pagination-nav-btn" disabled><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="15,18 9,12 15,6"></polyline>
                                </svg></button>
                            <button class="view-shop-pagination-current"><span id="currentPageNum">1</span></button>
                            <span id="view-shop-remainingCount"></span>
                            <button id="nextBtn" class="view-shop-pagination-nav-btn"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9,18 15,12 9,6"></polyline>
                                </svg></button>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </section>

        <section class="view-shop-right-panel">
            <article class="view-shop-section" aria-labelledby="categoriesHeader">
                <h2 class="view-shop-section-header" id="categoriesHeader">
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="margin-right: 8px;">
                        <path d="M22.7 19.3l-4.2-4.2c.4-.9.6-1.9.6-3 0-4.4-3.6-8-8-8-1.1 0-2.1.2-3 .6l3.5 3.5-2.1 2.1-3.5-3.5c-.4.9-.6 1.9-.6 3 0 4.4 3.6 8 8 8 1.1 0 2.1-.2 3-.6l4.2 4.2c.4.4 1 .4 1.4 0l1.2-1.2c.3-.4.3-1 0-1.4z" />
                    </svg>
                    Services Offered
                </h2>
                <div class="view-shop-categories-list" role="list">
                    <?php
                    $categories = $organized_services ?? [];
                    $category_count = count($categories);
                    $limit = 3;
                    ?>
                    <?php if (!empty($categories)): ?>
                        <div class="services-list-wrapper">
                            <div class="services-list-container" id="services-list-container">
                                <?php
                                $accordion_counter = 0;
                                foreach ($categories as $category_name => $category_data):
                                    $modal_id = 'serviceModal' . $accordion_counter;
                                ?>
                                    <div
                                        class="service-name-badge"
                                        data-bs-toggle="modal"
                                        data-bs-target="#<?php echo htmlspecialchars($modal_id); ?>"
                                        role="button">

                                        <span class="badge-text"><?php echo htmlspecialchars($category_name); ?></span>
                                        <i class="fas fa-chevron-right badge-separator-icon"></i>
                                    </div>
                                <?php
                                    $accordion_counter++;
                                endforeach;
                                ?>
                            </div>

                            <?php if ($category_count > $limit): ?>
                                <?php $remaining_count = $category_count - $limit; ?>
                                <button class="toggle-services-btn" id="toggle-services-btn" data-more-text="See <?php echo $remaining_count; ?> More" data-less-text="See Less">
                                    See <?php echo $remaining_count; ?> More
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-items" role="listitem">
                            <span class="text-muted">No services listed.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('services-list-container');
                    const toggleBtn = document.getElementById('toggle-services-btn');

                    if (!toggleBtn || !container) {
                        return;
                    }

                    toggleBtn.addEventListener('click', function() {
                        container.classList.toggle('is-expanded');
                        const isExpanded = container.classList.contains('is-expanded');
                        if (isExpanded) {
                            toggleBtn.textContent = toggleBtn.dataset.lessText;
                        } else {
                            toggleBtn.textContent = toggleBtn.dataset.moreText;
                        }
                    });
                });
            </script>

            <?php if (!empty($organized_services)): ?>
                <?php
                $modal_counter = 0;
                foreach ($organized_services as $category_name => $category_data):
                    $modal_id = 'serviceModal' . $modal_counter;
                    $accordion_id = 'modalAccordion' . $modal_counter;
                ?>
                    <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" aria-labelledby="<?php echo $modal_id; ?>Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="display: flex; align-items: center; min-height: 100vh;">
                            <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                                <div class="modal-header" style="border-bottom: 1px solid #eee; padding: 1rem 1.5rem;">
                                    <h5 class="modal-title" id="<?php echo $modal_id; ?>Label" style="display: flex; align-items: center; font-weight: 600;">
                                        <i class="fas <?php echo htmlspecialchars($category_data['icon']); ?> me-2"></i>
                                        <?php echo htmlspecialchars($category_name); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="box-shadow: none; outline: none;"></button>
                                </div>
                                <div class="modal-body" style="padding: 1.5rem;">
                                    <?php if (empty($category_data['subcategories'])): ?>
                                        <div class="no-services-message">
                                            <p class="text-muted">No specific services listed for this category.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="accordion" id="<?php echo $accordion_id; ?>">
                                            <?php
                                            $subcategory_counter = 0;
                                            foreach ($category_data['subcategories'] as $subcategory_name => $services):
                                                $subcategory_counter++;
                                                $service_count = count($services);
                                                $collapse_id = $accordion_id . "_collapse" . $subcategory_counter;
                                                $is_first = $subcategory_counter === 1;
                                            ?>
                                                <div class="accordion-item" style="border: none; border-bottom: 1px solid #eee;">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button <?php echo $is_first ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapse_id; ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="<?php echo $collapse_id; ?>" style="background-color: #f8f9fa; font-weight: 500;">
                                                            <?php echo htmlspecialchars($subcategory_name); ?>
                                                            <span class="badge bg-primary ms-2"><?php echo $service_count; ?></span>
                                                        </button>
                                                    </h2>
                                                    <div id="<?php echo $collapse_id; ?>" class="accordion-collapse collapse <?php echo $is_first ? 'show' : ''; ?>" data-bs-parent="#<?php echo $accordion_id; ?>">
                                                        <div class="accordion-body" style="padding-left: 1.5rem;">
                                                            <ol class="service-list ps-4">
                                                                <?php foreach ($services as $service_name): ?>
                                                                    <li><small><?php echo htmlspecialchars($service_name); ?></small></li>
                                                                <?php endforeach; ?>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $modal_counter++;
                endforeach;
                ?>
            <?php endif; ?>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const categoryCards = document.querySelectorAll('.service-category-card');

                    categoryCards.forEach(card => {
                        card.addEventListener('mouseenter', function() {
                            const arrow = this.querySelector('.service-category-arrow');
                            if (arrow) {
                                arrow.style.transform = 'translateX(5px)';
                            }
                        });

                        card.addEventListener('mouseleave', function() {
                            const arrow = this.querySelector('.service-category-arrow');
                            if (arrow) {
                                arrow.style.transform = 'translateX(0)';
                            }
                        });
                    });

                    const accordions = document.querySelectorAll('.accordion');
                    accordions.forEach(accordion => {
                        accordion.addEventListener('shown.bs.collapse', function(e) {
                            e.target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            });
                        });
                    });
                });
            </script>


            <article class="view-shop-section brand-vehicle" aria-labelledby="brandsHeader">
                <h2 class="view-shop-section-header" id="brandsHeader">
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="margin-right: 8px;">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11C5.84 5 5.28 5.42 5.08 6.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                    </svg>
                    Vehicle Brands Serviced
                </h2>
                <div class="view-shop-categories-list" role="list">
                    <?php if (!empty($brands_serviced_array)): ?>
                        <?php foreach ($brands_serviced_array as $brand): ?>
                            <div class="view-shop-category-item" role="listitem" tabindex="0" aria-label="<?php echo htmlspecialchars($brand); ?>">
                                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                    <path d="M9 16.2l-3.5-3.5 1.41-1.41L9 13.38l7.09-7.1 1.41 1.43z"></path>
                                </svg>
                                <?php echo htmlspecialchars($brand); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="view-shop-category-item no-items" role="listitem">
                            <span class="text-muted">No specific brands listed.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
            <article class="view-shop-section location-map" aria-labelledby="locationHeader">
                <h2 class="view-shop-section-header" id="locationHeader">
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                        <path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
                    </svg>
                    Location
                </h2>
                <div class="view-shop-map-container" role="region" aria-label="Map showing business location">
                    <div id="map"></div>
                    <button onclick="openDirections()" class="map-directions-btn"><i class="fas fa-directions me-2"></i>Get Directions</button>
                </div>
            </article>

            <article class="view-shop-section photos-shop" aria-labelledby="galleryHeader">
                <h2 class="view-shop-section-header" id="galleryHeader">
                    <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                    </svg>
                    Photos
                </h2>
                <div class="view-shop-gallery-grid" id="galleryGrid"></div>
                <?php if (empty($gallery_images_php)): ?>
                    <p class="text-center text-muted">No images have been uploaded.</p>
                <?php endif; ?>
            </article>

            <div id="imageModal" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content position-relative border-0 bg-transparent shadow-none text-center">

                        <div class="position-relative d-inline-block">
                            <img class="img-fluid rounded" id="view-shop-modalImage" src="" alt="Full size image" style="border-radius: 16px;">
                            <button type="button" class="position-absolute"
                                style="
    top: -15px;
    right: -15px;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    border-radius: 50%;
    padding: 6px 12px;
    font-size: 20px;
    z-index: 10;
    line-height: 1;
"
                                data-bs-dismiss="modal" aria-label="Close">
                                &times;
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            </div>
        </section>
    </main>

    <div id="loginRequiredModal" class="accountRequired-modal" style="z-index: 1150;">
        <div class="accountRequired-modal-content">
            <span class="accountRequired-close-modal">&times;</span>
            <h3 class="accountRequired-modal-title">Account Required</h3>
            <div class="accountRequired-modal-body">
                <p>You need an account to view shop details. Please login or signup.</p>
            </div>
            <div class="accountRequired-modal-buttons">
                <button id="loginBtn" class="accountRequired-modal-btn accountRequired-btn">Login</button>
                <button id="signupBtn" class="accountRequired-modal-btn accountRequired-btn">Sign Up</button>
            </div>
        </div>
    </div>

<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header" style="border-bottom: 1px solid #dee2e6;">
                <h5 class="modal-title" id="scheduleModalLabel" style="font-weight: 600;">
                    <i class="fas fa-calendar-alt me-2" style="text-align: center;"></i> Shop Schedule
                </h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close"
                    style="background: none; border: none; font-size: 1.5rem; color: #555; cursor: pointer; line-height: 1;">
                    &times;
                </button>
            </div>

            <div class="modal-body" style="padding: 1rem 1.2rem;">
                <ul class="list-group list-group-flush">
                <?php
                if (isset($schedule) && is_array($schedule)):
                    foreach ($schedule as $day => $times):
                        $is_today_in_loop = ($day === $current_day);
                        $formatted_time = '<span class="text-danger">Closed</span>';
                        if (!empty($times['open_am'])) {
                            $formatted_open_am = date('g:i A', strtotime($times['open_am']));
                            $formatted_close_am = date('g:i A', strtotime($times['close_am']));
                            $formatted_time = htmlspecialchars($formatted_open_am) . " - " . htmlspecialchars($formatted_close_am);

                            if (!empty($times['open_pm'])) {
                                $formatted_open_pm = date('g:i A', strtotime($times['open_pm']));
                                $formatted_close_pm = date('g:i A', strtotime($times['close_pm']));
                                $formatted_time .= " / " . htmlspecialchars($formatted_open_pm) . " - " . htmlspecialchars($formatted_close_pm);
                            }
                        }
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?= $is_today_in_loop ? 'bg-light fw-bold' : '' ?>" 
                        style="padding-left: 0.5rem; padding-right: 0.5rem;">
                        <span style="min-width: 80px;"><?= htmlspecialchars($day) ?></span>
                        <span class="text-end">
                            <?= $formatted_time ?>
                            <?php if ($is_today_in_loop && $shop_status == 'open'): ?>
                                <span class="badge <?= $is_open ? 'bg-success' : 'bg-danger' ?> ms-2">
                                    <?= $is_open ? 'Open Now' : 'Closed' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php endforeach;
                else: ?>
                    <li class="list-group-item text-center text-muted">Schedule information unavailable.</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>


    <div id="messageContainer" class="message-container" style="display: none;"><span id="messageText"></span></div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/filter-modal.js"></script>

    <script>
        const BASE_URL = "<?php echo rtrim(BASE_URL, '/'); ?>";
    </script>
    <script src="<?php echo BASE_URL; ?>/js/account-required.js"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-tab-panel.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/shop-map.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-photos.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/view-details-pagination.js"></script>

    <script>
        window.currentUser = {
            id: <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>,
            fullname: <?php echo json_encode(htmlspecialchars($_SESSION['fullname'] ?? '')); ?>,
            isLoggedIn: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>
        };

        window.shopInfo = {
            name: <?php echo json_encode($shop['shop_name'] ?? ''); ?>,
            displayAddress: <?php echo json_encode($display_address ?? ''); ?>,
            combinedAddress: <?php echo json_encode($combined_address ?? ''); ?>,
            encodedCombinedAddress: <?php echo json_encode($encoded_combined_address ?? ''); ?>
        };

        window.userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        window.galleryData = {
            images: <?php echo json_encode($gallery_images_php ?? []); ?>
        };
    </script>

    <script>
        const currentUserId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let scrollPosition = 0;
            const body = document.body;

            document.addEventListener('show.bs.modal', function() {
                if (body.classList.contains('modal-open-freeze')) {
                    return;
                }
                scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

                body.style.top = `-${scrollPosition}px`;

                body.classList.add('modal-open-freeze');
            });

            document.addEventListener('hidden.bs.modal', function() {
                if (!body.classList.contains('modal-open')) {
                    body.classList.remove('modal-open-freeze');
                    body.style.top = '';
                    window.scrollTo(0, scrollPosition);
                }
            });
        });
    </script>
</body>

</html>