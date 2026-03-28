<?php
include 'db_connection.php';

try {
    $sql = "SELECT * FROM shop_applications 
            WHERE user_id = ? AND status = 'Approved'
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header("Location: home.php");
        exit;
    }

    $shop = $result->fetch_assoc() ?? [];
    $shop_id = $shop['id'] ?? 0;

    $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];

    $shop = array_merge([
        'shop_name' => '',
        'email' => '',
        'open_24_7' => false,
        'years_operation' => '',
        'phone' => '',
        'description' => '',
        'facebook' => '',
        'instagram' => '',
        'website' => '',
        'shop_location' => '',
        'brands_serviced' => '',
        'town_city' => '',
        'province' => '',
        'country' => '',
        'postal_code' => '',
        'opening_time' => '',
        'closing_time' => '',
        'days_open' => '',
        'show_book_now' => true,
        'show_emergency' => true,
        'shop_gallery_images' => '[]'
    ], $shop);

    include 'shops-badge.php';

    $shop_name = $shop['shop_name'];
    $business_hours_display = '';

    $facebook = $shop['facebook'];
    $instagram = $shop['instagram'];
    $website = $shop['website'];
    $shop_location = $shop['shop_location'];
    $brands_serviced = $shop['brands_serviced'];

    $full_address = htmlspecialchars($shop['town_city'] . ', ' . $shop['province'] . ', ' . $shop['country'] . ', ' . $shop['postal_code']);

    $display_address = '';
    if (!empty($shop_location)) {
        $display_address = htmlspecialchars($shop_location);
    } else {
        $display_address = $full_address;
    }

    $combined_address = $shop['shop_name'] . ', ' . $display_address;
    $encoded_combined_address = urlencode($combined_address);

    if ($shop['open_24_7']) {
        $business_hours_display = "Open 24/7";
    } elseif (!empty($shop['opening_time']) && !empty($shop['closing_time'])) {
        $opening_time = date("g:i A", strtotime($shop['opening_time']));
        $closing_time = date("g:i A", strtotime($shop['closing_time']));
        $days_string = '';
        if (!empty($shop['days_open'])) {
            $days_open = explode(',', $shop['days_open']);
            $formatted_days = array_map(function ($day) {
                return ucfirst(trim($day));
            }, $days_open);
            $days_string = ' (' . implode(', ', $formatted_days) . ')';
        }
        $business_hours_display = "{$opening_time} - {$closing_time}{$days_string}";
    }

    $reviews_sql = "SELECT sr.*, u.fullname, u.profile_picture
                      FROM shop_ratings sr
                      JOIN users u ON sr.user_id = u.id
                      WHERE sr.shop_id = ?
                      ORDER BY sr.created_at DESC";
    $stmt = $conn->prepare($reviews_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $reviews_result = $stmt->get_result();
    $total_reviews = $reviews_result->num_rows;

    $avg_sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews
                FROM shop_ratings
                WHERE shop_id = ?";
    $stmt = $conn->prepare($avg_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $avg_result = $stmt->get_result();

    $average_rating = "0.0";
    if ($avg_result->num_rows > 0) {
        $rating_data = $avg_result->fetch_assoc();
        $average_rating = $rating_data['average_rating'] ? number_format($rating_data['average_rating'], 1) : "0.0";
        $total_reviews = $rating_data['total_reviews'] ? $rating_data['total_reviews'] : 0;
    }

    $rating_distribution = [];
    $rating_distribution_sql = "SELECT rating, COUNT(*) AS count
                                  FROM shop_ratings
                                  WHERE shop_id = ?
                                  GROUP BY rating
                                  ORDER BY rating DESC";
    $stmt = $conn->prepare($rating_distribution_sql);
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $rating_distribution_result = $stmt->get_result();
    while ($row = $rating_distribution_result->fetch_assoc()) {
        $rating_distribution[$row['rating']] = $row['count'];
    }

    $brands_serviced_array = !empty($brands_serviced) ? explode(',', $brands_serviced) : [];

    $gallery_images_json = $shop['shop_gallery_images'];
    $gallery_images_php = json_decode($gallery_images_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $gallery_images_php = [];
    }
} catch (Exception $e) {
    error_log("Error processing shop ID: " . $e->getMessage());
    header("Location: home.php");
    exit;
}
?>