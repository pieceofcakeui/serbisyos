<?php
$apiKey = 'QhfqXgeikY4Fx2efM7NFXf_YcKNZc0T-bW-ZrJvomL1Gkl6wz2XZMnkUts-nUoj1Cw7bbk9ptIkJpohhHhqhQ6iSTaRJjY1Aqkwt5X34b-pS1llO6VXDY3f7CcFSaHYx'; 
$query = $_GET['query'];
$url = 'https://api.yelp.com/v3/autocomplete?text=' . urlencode($query) . '&categories=auto';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $apiKey]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
header('Content-Type: application/json');
echo curl_exec($ch);
curl_close($ch);
?>
