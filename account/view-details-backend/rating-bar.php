<?php
include './backend/db_connection.php';
for ($i = 5; $i >= 1; $i--) {
    $count = $rating_distribution[$i] ?? 0;
    $percentage = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
    echo '
<div class="rating-bar">
    <span>' . $i . ' stars</span>
<div class="progress">
<div class="progress-bar" style="width: ' . $percentage . '%"></div>
</div>
<span>' . $count . ' reviews</span>
</div>';
}
?>