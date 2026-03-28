<?php
include './backend/db_connection.php';
$shop_logo = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'uploads/shop_logo/logo.jpg';

if (!str_starts_with($shop_logo, 'uploads/shop_logo/')) {
    $shop_logo = 'uploads/shop_logo/' . $shop_logo;
}

if (!file_exists($shop_logo)) {
    $shop_logo = 'uploads/shop_logo/logo.jpg';
}
?>