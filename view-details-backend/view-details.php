<?php
include '/functions/db_connection.php';

$shop = null;

try {
    if (!isset($_GET['shop']) || empty($_GET['shop'])) {
        header("Location: " . BASE_URL . "/home");
        exit;
    }
    $shop_slug = $_GET['shop'];

    include 'shops-badge.php';

    $sql = "SELECT *, opening_time, closing_time, open_24_7, days_open, show_book_now, show_emergency, phone,
            facebook, instagram, website, shop_location, 
            brands_serviced, shop_gallery_images
            FROM shop_applications
            WHERE shop_slug = ? AND status = 'Approved'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $shop_slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header("Location: " . BASE_URL . "/home");
        exit;
    }

    $shop = $result->fetch_assoc();
    $shop_id = $shop['id'];
    $shop_name = $shop['shop_name'];
    $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $shop['user_id'];

    $organized_services = [];
    $sql_services = "SELECT sc.name AS category_name, sc.icon AS category_icon, sc.display_order, ssc.name AS subcategory_name, s.name AS service_name FROM shop_services ss JOIN services s ON ss.service_id = s.id JOIN service_subcategories ssc ON s.subcategory_id = ssc.id JOIN service_categories sc ON ssc.category_id = sc.id WHERE ss.application_id = ? ORDER BY sc.display_order, sc.name, ssc.name, s.name";
    $stmt_services = $conn->prepare($sql_services);
    $stmt_services->bind_param("i", $shop_id);
    $stmt_services->execute();
    $result_services = $stmt_services->get_result();
    while ($service_row = $result_services->fetch_assoc()) {
        $category_name = $service_row['category_name'];
        $subcategory_name = $service_row['subcategory_name'];
        $service_name = $service_row['service_name'];
        if (!isset($organized_services[$category_name])) {
            $organized_services[$category_name] = ['icon' => $service_row['category_icon'], 'subcategories' => []];
        }
        if (!isset($organized_services[$category_name]['subcategories'][$subcategory_name])) {
            $organized_services[$category_name]['subcategories'][$subcategory_name] = [];
        }
        $organized_services[$category_name]['subcategories'][$subcategory_name][] = $service_name;
    }
    $stmt_services->close();

    $business_hours_display = '';

    $facebook = $shop['facebook'] ?? '';
    $instagram = $shop['instagram'] ?? '';
    $website = $shop['website'] ?? '';
    $google_map_link = $shop['google_map_link'] ?? '';
    $shop_location = $shop['shop_location'] ?? '';
    $brands_serviced = $shop['brands_serviced'] ?? '';
    $vehicle_types = $shop['vehicle_type'] ?? '';
    $vehicle_types_array = !empty($vehicle_types) ? explode(',', $vehicle_types) : [];

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

    $shop_logo = !empty($shop['shop_logo']) ? $shop['shop_logo'] : 'uploads/shop_logo/logo.jpg';
    if (!str_starts_with($shop_logo, 'uploads/shop_logo/')) {
        $shop_logo = 'uploads/shop_logo/' . $shop_logo;
    }
    if (!file_exists($shop_logo)) {
        $shop_logo = 'uploads/shop_logo/logo.jpg';
    }

    $services_offered = explode(',', $shop['services_offered']);
    $brands_serviced_array = !empty($brands_serviced) ? explode(',', $brands_serviced) : [];

    $gallery_images_json = $shop['shop_gallery_images'] ?? '[]';
    $image_paths_from_db = json_decode($gallery_images_json, true);

    $gallery_images_php = [];

    if (is_array($image_paths_from_db)) {
        foreach ($image_paths_from_db as $path) {
            $clean_path = trim($path);
            if (!str_starts_with($clean_path, '../account/')) {
                $gallery_images_php[] = '../account/' . $clean_path;
            } else {
                $gallery_images_php[] = $clean_path;
            }
        }
    }
} catch (Exception $e) {
    error_log("Error processing shop: " . $e->getMessage());
    header("Location: " . BASE_URL . "/home");
    exit;
}
?>