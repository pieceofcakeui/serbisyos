<?php
include 'db_connection.php';
include 'security_helper.php';

function generate_stars($rating) {
    if ($rating === null) {
        return '';
    }
    $stars_html = '';
    $rating = round($rating * 2) / 2;
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    for ($i = 0; $i < $full_stars; $i++) {
        $stars_html .= '<i class="fas fa-star"></i>';
    }
    if ($half_star) {
        $stars_html .= '<i class="fas fa-star-half-alt"></i>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars_html .= '<i class="far fa-star"></i>';
    }
    return $stars_html;
}

$shops_html = '';

if (isset($_GET['service']) && !empty(trim($_GET['service']))) {
    $query_term = trim($_GET['service']);
    
    $serviceSynonymMap = [
        'Oil Change' => ['Oil Change', 'Change Oil', 'PMS'],
        'Engine Overhaul' => ['Engine Overhaul', 'Engine Rebuild'],
        'Brake Pad Replacement' => ['Brake Pad Replacement', 'Brake Pads'],
        'AC Gas Recharge' => ['AC Gas Recharge', 'Freon Recharge', 'Aircon Charge'],
        'Wheel Alignment' => ['Wheel Alignment', 'Alignment'],
        'Flat Tire Repair' => ['Flat Tire Repair'],
    ];
    
    $search_parts = explode(' & ', $query_term);
    $regex_parts = [];

    foreach ($search_parts as $part) {
        $search_terms = [$part];
        if (array_key_exists($part, $serviceSynonymMap)) {
            $search_terms = $serviceSynonymMap[$part];
        }
        $sub_regex = implode('|', array_map(function($term) { return preg_quote($term, '/'); }, $search_terms));
        $regex_parts[] = '(?=.*' . $sub_regex . ')';
    }
    $final_regex = '^' . implode('', $regex_parts) . '.*$';

    $stmt = $conn->prepare("
        SELECT 
            sa.id, sa.shop_logo, sa.shop_name, sa.shop_location, 
            AVG(sr.rating) AS average_rating, 
            COUNT(DISTINCT sr.id) AS rating_count,
            (SELECT COUNT(*) FROM services_booking sb WHERE sb.shop_id = sa.id AND sb.booking_status = 'Completed') AS completed_bookings
        FROM shop_applications AS sa
        LEFT JOIN shop_ratings AS sr ON sa.id = sr.shop_id
        WHERE sa.services_offered REGEXP ?
        GROUP BY sa.id
        ORDER BY average_rating DESC, completed_bookings DESC
    ");

    $stmt->bind_param("s", $final_regex);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($shop = $result->fetch_assoc()) {
            $default_logo = 'uploads/shop_logo/logo.jpg';
            $final_logo_path = $default_logo;

            if (!empty($shop['shop_logo'])) {
                $potential_path = $shop['shop_logo'];
                if (!str_starts_with($potential_path, 'uploads/shop_logo/')) {
                    $potential_path = 'uploads/shop_logo/' . $potential_path;
                }
                if (file_exists('../' . $potential_path)) {
                    $final_logo_path = $potential_path;
                }
            }

            $avg_rating = $shop['average_rating'];
            $booking_count = $shop['completed_bookings'];
            $topRated = ($avg_rating !== null && $avg_rating >= 4.0);
            $mostBooked = ($booking_count >= 10);
            
            $badge_html = '';
            if ($topRated || $mostBooked) {
                $badge_html .= '<div class="badge-container">';
                if ($topRated) {
                    $badge_html .= '<div class="shop-badge top-rated"><i class="fas fa-star"></i> Top Rated</div>';
                }
                if ($mostBooked) {
                    $badge_html .= '<div class="shop-badge top-booking"><i class="fas fa-calendar-check"></i> Most Booked</div>';
                }
                $badge_html .= '</div>';
            }

            $rating_display = '<span>No ratings yet</span>';
            if ($shop['rating_count'] > 0) {
                $stars = generate_stars($shop['average_rating']);
                $rating_number = number_format($shop['average_rating'], 1);
                $rating_count = $shop['rating_count'];
                $rating_display = "
                    <span class='rating-number'>{$rating_number}</span>
                    <span class='stars'>{$stars}</span>
                    <span class='rating-count'>({$rating_count})</span>
                ";
            }
            
            $encrypted_id = URLSecurity::encryptId($shop['id']);
            $encoded_name = urlencode($shop['shop_name']);
            $view_shop_link = "view_details.php?shop_id={$encrypted_id}&shop={$encoded_name}";

            $shops_html .= "
            <div class='shop-card'>
                {$badge_html}
                <img src='" . htmlspecialchars($final_logo_path) . "' alt='" . htmlspecialchars($shop['shop_name']) . " Logo'>
                <h5>" . htmlspecialchars($shop['shop_name']) . "</h5>
                <p>" . htmlspecialchars($shop['shop_location']) . "</p>
                <div class='shop-rating'>{$rating_display}</div>
                <a href='{$view_shop_link}' class='btn-view'>View Shop</a>
            </div>";
        }
    } else {
        $shops_html = '
        <div class="empty-state" style="grid-column: 1 / -1;">
            <i class="fas fa-wrench"></i>
            <p>No shops found for this service yet. Please try a different search.</p>
        </div>';
    }
    $stmt->close();
} else {
     $shops_html = '
     <div class="empty-state" style="grid-column: 1 / -1;">
         <i class="fas fa-exclamation-triangle"></i>
         <p>Invalid service selected.</p>
     </div>';
}

$conn->close();
echo $shops_html;