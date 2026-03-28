<?php
include './backend/db_connection.php';
$full_stars = floor($average_rating);
$half_star = ($average_rating - $full_stars) >= 0.5;
for ($i = 1; $i <= 5; $i++) {
    if ($i <= $full_stars) {
        echo '<i class="fas fa-star"></i>';
    } elseif ($half_star && $i == $full_stars + 1) {
        echo '<i class="fas fa-star-half-alt"></i>';
    } else {
        echo '<i class="far fa-star"></i>';
    }
}
?>