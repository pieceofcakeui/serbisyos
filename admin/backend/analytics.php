<?php

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalOwners = $conn->query("SELECT COUNT(*) AS total FROM users WHERE profile_type = 'owner'")->fetch_assoc()['total'];
$totalRegularUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE profile_type = 'user'")->fetch_assoc()['total'];

$totalShops = $conn->query("SELECT COUNT(*) AS total FROM shop_applications")->fetch_assoc()['total'];
$approvedShops = $conn->query("SELECT COUNT(*) AS total FROM shop_applications WHERE status = 'Approved'")->fetch_assoc()['total'];
$pendingShops = $conn->query("SELECT COUNT(*) AS total FROM shop_applications WHERE status = 'Pending'")->fetch_assoc()['total'];
$rejectedShops = $conn->query("SELECT COUNT(*) AS total FROM shop_applications WHERE status = 'Rejected'")->fetch_assoc()['total'];

$totalShops = $totalShops ?? 0;
$approvedShops = $approvedShops ?? 0;
$pendingShops = $pendingShops ?? 0;
$rejectedShops = $rejectedShops ?? 0;
$totalOwners = $totalOwners ?? 0;
$totalRegularUsers = $totalRegularUsers ?? 0;
?>