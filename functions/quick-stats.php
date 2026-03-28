<?php
include 'db_connection.php';

$shopQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM shop_applications WHERE status = 'Approved'");
$approvedShops = mysqli_fetch_assoc($shopQuery)['total'];

$happyUsersQuery = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as total FROM shop_ratings WHERE rating >= 4");
$happyUsers = mysqli_fetch_assoc($happyUsersQuery)['total'];

$totalRatings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM shop_ratings"))['total'];
$positiveRatings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM shop_ratings WHERE rating >= 4"))['total'];
$positivePercentage = $totalRatings > 0 ? round(($positiveRatings / $totalRatings) * 100) : 0;
?>