<?php
session_start();
include './functions/db_connection.php';

function generate_stars($rating)
{
    if ($rating === null)
        return '';
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

function create_service_id($service_name)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $service_name), '-'));
}

$service_categories = [];
$category_sql = "SELECT id, name, icon FROM service_categories ORDER BY display_order";
$category_result = $conn->query($category_sql);
while ($category_row = $category_result->fetch_assoc()) {
    $category_id = $category_row['id'];
    $category_name = $category_row['name'];
    $service_categories[$category_name] = ['icon' => $category_row['icon'], 'subcategories' => []];
    $subcategory_sql = "SELECT id, name FROM service_subcategories WHERE category_id = $category_id ORDER BY name";
    $subcategory_result = $conn->query($subcategory_sql);
    while ($subcategory_row = $subcategory_result->fetch_assoc()) {
        $subcategory_id = $subcategory_row['id'];
        $subcategory_name = $subcategory_row['name'];
        $service_categories[$category_name]['subcategories'][$subcategory_name] = [];
        $service_sql = "SELECT id, name, slug, query_term FROM services WHERE subcategory_id = $subcategory_id ORDER BY name";
        $service_result = $conn->query($service_sql);
        while ($service_row = $service_result->fetch_assoc()) {
            $service_name = $service_row['name'];
            $service_categories[$category_name]['subcategories'][$subcategory_name][$service_name] = [
                'id' => $service_row['id'],
                'slug' => $service_row['slug'],
                'query' => $service_row['query_term']
            ];
        }
    }
}

$flat_service_list = [];
foreach ($service_categories as $category_name => $category_details) {
    foreach ($category_details['subcategories'] as $subcategory_name => $services) {
        foreach ($services as $service_name => $service_details) {
            $flat_service_list[] = [
                'label' => $service_name,
                'slug' => $service_details['slug'],
                'query' => $service_details['query'],
                'category' => $category_name,
                'id' => $service_details['id']
            ];
        }
    }
}

$selected_service_slug = isset($_GET['slug']) ? $_GET['slug'] : null;
$selected_service_id = null;
$selected_service_name = '';
$initial_shops = [];

if ($selected_service_slug) {
    $stmt_name = $conn->prepare("SELECT id, name FROM services WHERE slug = ?");
    $stmt_name->bind_param("s", $selected_service_slug);
    $stmt_name->execute();
    $name_result = $stmt_name->get_result();
    if ($name_row = $name_result->fetch_assoc()) {
        $selected_service_id = $name_row['id'];
        $selected_service_name = $name_row['name'];
    }
    $stmt_name->close();
}


if ($selected_service_id) {
    $stmt = $conn->prepare("
        SELECT 
            sa.id, sa.shop_logo, sa.shop_name, sa.shop_slug, sa.shop_location, sa.shop_status,
            AVG(sr.rating) AS average_rating, 
            COUNT(DISTINCT sr.id) AS rating_count,
            (SELECT COUNT(*) FROM services_booking sb WHERE sb.shop_id = sa.id AND sb.booking_status = 'Completed') AS completed_bookings
        FROM shop_applications AS sa 
        JOIN shop_services AS ss ON sa.id = ss.application_id
        LEFT JOIN shop_ratings AS sr ON sa.id = sr.shop_id 
        WHERE ss.service_id = ? AND sa.status = 'Approved'
        GROUP BY sa.id 
        ORDER BY average_rating DESC, completed_bookings DESC
    ");
    $stmt->bind_param("i", $selected_service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $initial_shops[] = $row;
    }
    $stmt->close();
}

$highlighted_services_data = [];
$query = "SELECT s.id, s.name, s.slug, s.query_term, COUNT(ss.service_id) as shop_count 
          FROM services s
          JOIN shop_services ss ON s.id = ss.service_id
          GROUP BY s.id, s.name, s.slug, s.query_term
          ORDER BY shop_count DESC, s.name ASC
          LIMIT 12";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $highlighted_services_data[] = [
            'name' => $row['name'],
            'id' => $row['id'],
            'slug' => $row['slug'],
            'query' => $row['query_term']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($selected_service_name ?: 'Our Services'); ?></title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
      <style>
        :root {
            --border-color: #E5E7EB;
        }

        body {
            font-family: 'Montserrat', sans-serif
        }

        .services-section {
            padding: 20px 0;
            margin-bottom: 30px;
            margin-top: 40px
        }

        .services-header {
            text-align: center;
            margin-bottom: 40px
        }

        .services-header h1 {
            font-weight: 800;
            font-size: 3rem;
            color: #1a1a1a
        }

        .services-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 700px;
            margin: 1rem auto 0
        }

        .service-search-container {
            max-width: 800px;
            margin: 0 auto 40px auto;
            text-align: center
        }

        .search-bar-wrapper {
            position: relative
        }

        .search-bar-wrapper .fa-search {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.1rem
        }

        #service-search-bar {
            width: 100%;
            padding: 15px 20px 15px 50px;
            font-size: 1.1rem;
            border-radius: 50px;
            border: 1px solid #ddd;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05)
        }

        #service-search-bar:focus {
            outline: none;
            border-color: #ffc107;
            box-shadow: 0 5px 20px rgba(255, 193, 7, .2)
        }

        .autocomplete-suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 15px;
            width: 100%;
            top: 110%;
            left: 0;
            max-height: 300px;
            overflow-y: auto;
            text-align: left;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
            z-index: 1001;
            display: none
        }

        .autocomplete-suggestions div {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0
        }

        .autocomplete-suggestions div:last-child {
            border-bottom: none
        }

        .autocomplete-suggestions div:hover {
            background-color: #f8f9fa
        }

        .autocomplete-suggestions div strong {
            color: #ffc107
        }

        #browse-services-btn {
            margin-top: 15px;
            background: none;
            border: none;
            color: #0d6efd;
            font-weight: 600;
            cursor: pointer
        }

        #browse-services-btn:hover {
            text-decoration: underline
        }

        .services-note {
            font-size: .9rem;
            color: #6c757d;
            margin-top: 15px;
            font-style: italic
        }

        .service-content h2 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center
        }

        .back-to-services-btn {
            display: inline-block;
            margin-bottom: 2rem;
            font-weight: 600;
            color: #0d6efd;
            cursor: pointer;
            text-decoration: none
        }

        .back-to-services-btn:hover {
            text-decoration: underline
        }

        .shops-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem
        }

        .shop-card {
            background: white;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            text-align: center;
            padding: 1.5rem;
            transition: all .3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow: hidden
        }

        .badge-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 5px;
            margin-bottom: .75rem
        }

        .shop-badge {
            padding: 0 5px;
            border-radius: 8px;
            font-size: .7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
            white-space: nowrap
        }

        .shop-badge.top-rated i,
        .shop-badge.top-booking i {
            font-size: .7rem !important
        }

        .shop-badge.top-rated {
            background-color: #ffc107;
            color: #212529
        }

        .shop-badge.top-booking {
            background-color: #00a3bf;
            color: white
        }

        .shop-logo-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        .shop-logo-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f0f0f0;
        }
        .verified-badge-icon {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 24px;
            height: 24px;
            background-color: #1d9bf0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .verified-badge-icon .fa-check {
            color: #fff;
            font-size: 12px;
        }

        .shop-card h5 {
            font-weight: 600;
            margin-bottom: .25rem
        }

        .shop-card p {
            color: #666;
            font-size: .9rem;
            margin-bottom: .5rem
        }

        .shop-card .shop-rating {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-bottom: 1rem;
            height: 24px
        }

        .shop-card .shop-rating .stars {
            color: #ffc107
        }

        .shop-card .shop-rating .rating-number {
            font-weight: bold
        }

        .shop-card .shop-rating .rating-count {
            color: #6c757d;
            font-size: .85em
        }

        .shop-card .btn-view {
            background: #1a1a1a;
            color: white;
            border-radius: 50px;
            padding: .5rem 1.5rem;
            text-decoration: none;
            font-weight: 500;
            font-size: .9rem;
            transition: all .3s ease
        }

        .shop-card .btn-view:hover {
            background: #333
        }

        .services-highlight-container {
            margin-top: 4rem;
            padding: 2rem 0;
            border-top: 1px solid #eee
        }

        .services-highlight-container h3 {
            font-weight: 700;
            margin-bottom: 2.5rem;
            color: #343a40;
            text-align: center
        }

        .service-highlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            justify-content: center
        }

        .service-highlight-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            font-weight: 600;
            color: #212529;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
            transition: all .3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            text-decoration: none
        }

        .service-highlight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .1);
            border-color: #ffc107
        }

        .service-highlight-card .service-icon {
            background-color: #ffc107;
            color: #1a1a1a;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
            flex-shrink: 0
        }

        .service-highlight-card .service-name {
            flex-grow: 1;
            text-align: left
        }

        .service-highlight-card .service-arrow {
            font-size: 1rem;
            color: #ced4da;
            transition: color .3s ease
        }

        .service-highlight-card:hover .service-arrow {
            color: #212529
        }

        .empty-state,
        .loading-state {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem 2rem;
            background-color: #f8f9fa;
            border-radius: 15px;
            border: 1px dashed #e0e0e0;
            min-height: 250px;
            text-align: center
        }

        .empty-state i,
        .loading-state i {
            font-size: 3.5rem;
            color: #d3d3d3;
            margin-bottom: 1.5rem
        }

        .empty-state p,
        .loading-state p {
            font-size: 1.1rem;
            color: #6c757d;
            font-weight: 500;
            max-width: 400px
        }

        .services-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, .5)
        }

        .services-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            border-radius: 15px;
            width: 90%;
            max-width: 1100px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .2)
        }

        .services-modal-header {
            padding: 15px 25px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .services-modal-header h4 {
            margin: 0;
            font-weight: 700
        }

        .services-modal-close {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            border: none;
            background: none;
            cursor: pointer
        }

        .services-modal-body {
            display: flex;
            height: 70vh;
            max-height: 600px
        }

        .modal-nav {
            width: 300px;
            min-width: 300px;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            background: #f8f9fa
        }

        .modal-nav-link {
            display: block;
            padding: 15px 20px;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-weight: 600;
            color: #333
        }

        .modal-nav-link:hover {
            background-color: #e9ecef
        }

        .modal-nav-link.active {
            background-color: #ffc107;
            color: #1a1a1a
        }

        .modal-nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center
        }

        .modal-main {
            flex-grow: 1;
            padding: 25px;
            overflow-y: auto
        }

        .modal-main h5 {
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #ffc107;
            padding-bottom: 5px
        }

        .modal-main h5:first-child {
            margin-top: 0
        }

        .modal-main ul {
            list-style: none;
            padding: 0
        }

        .modal-main ul li {
            padding: 8px 0;
            cursor: pointer
        }

        .modal-main ul li:hover {
            color: #0d6efd
        }

        .modal-dropdown-view {
            display: none;
            padding: 20px
        }

        .modal-dropdown-view .form-select {
            margin-bottom: 15px
        }

        .modal-dropdown-view #modal-go-btn {
            width: 100%;
            padding: 10px;
            font-weight: 600
        }

        .cta-bottom {
            background: #fff;
            margin-top: 80px;
            border: 1px solid #e2e8f0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            overflow: hidden
        }

        .cta-bottom-content {
            padding: 2rem 3rem;
            text-align: center
        }

        .cta-bottom-img {
            height: 100%
        }

        .cta-bottom-img img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .cta-bottom h3 {
            font-weight: 700;
            font-size: 2rem;
            color: #1a1a1a;
            margin-bottom: 1rem
        }

        .cta-bottom p {
            font-size: 1.1rem;
            color: #666;
            max-width: 500px;
            margin: 0 auto 2rem auto
        }

        .cta-bottom a {
            background: #ffc107;
            color: #1a1a1a;
            padding: .8rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all .3s ease;
            display: inline-block
        }

        .cta-bottom a:hover {
            background: #ffca2c;
            transform: translateY(-2px)
        }

        .floating-contact-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #ffc107;
            color: #1a1a1a;
            padding: 1rem;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .2);
            cursor: pointer;
            transition: all .3s ease;
            z-index: 1000;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center
        }

        .floating-contact-btn i {
            margin-right: .5rem
        }

        .floating-contact-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 193, 7, .4);
            color: #1a1a1a
        }

        @media (min-width:768px) {
            .shops-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media (min-width:992px) {
            .shops-grid {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        @media (max-width:768px) {
            .cta-bottom {
                grid-template-columns: 1fr
            }

            .cta-bottom-content {
                order: 1
            }

            .cta-bottom-img {
                order: 2
            }

            .services-modal-body {
                flex-direction: column;
                height: auto;
                max-height: 80vh
            }

            .modal-nav,
            .modal-main {
                display: none
            }

            .modal-dropdown-view {
                display: block
            }

            .services-section {
                margin-top: 100px !important
            }
        }

        @media (max-width:575.98px) {
            .shops-grid {
                grid-template-columns: 1fr
            }

            .service-highlight-grid {
                grid-template-columns: 1fr
            }

            .services-header h1 {
                font-size: 2.2rem
            }

            .service-content h2 {
                font-size: 1.8rem
            }
        }
    </style>
    <style>
        .shop-status-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 5px;
            margin-bottom: 8px;
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
            margin-right: 4px;
        }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

    <div class="services-section">
        <div class="container">
            <div class="services-header">
                <h1>Services from Our Trusted Partners</h1>
                <p>Explore a wide range of automotive services offered by our network of skilled and reliable partner
                    shops.</p>
            </div>

            <div class="service-search-container">
                <div class="search-bar-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="service-search-bar" placeholder="e.g., Oil Change, Brake Repair...">
                    <div class="autocomplete-suggestions"></div>
                </div>
                <button id="browse-services-btn">Or Browse All Services</button>
                <div class="services-note">Note: The full list includes all potential services. Some may not have an
                    available partner shop yet.</div>
            </div>

            <div id="service-content-pane" class="service-content">
                <?php if ($selected_service_id): ?>
                    <div class="text-center"><a href="<?php echo BASE_URL; ?>/service" class="back-to-services-btn"><i
                                class="fas fa-arrow-left"></i> Back to All Services</a></div>
                    <h2 id="service-title"><?php echo htmlspecialchars($selected_service_name); ?></h2>
                    <div id="shops-grid" class="shops-grid">
                        <?php if (!empty($initial_shops)): ?>
                            <?php foreach ($initial_shops as $shop): ?>
                                <div class="shop-card">
                                    <?php
                                    $topRated = ($shop['average_rating'] !== null && $shop['average_rating'] >= 4.0);
                                    $mostBooked = ($shop['completed_bookings'] >= 10);
                                    $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                                    $final_logo_path = $default_logo_url;
                                    if (!empty($shop['shop_logo'])) {
                                        $base_directory = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
                                        $logo_file_path = $_SERVER['DOCUMENT_ROOT'] . $base_directory . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                        if (file_exists($logo_file_path)) {
                                            $final_logo_path = BASE_URL . '/account/uploads/shop_logo/' . $shop['shop_logo'];
                                        }
                                    }
                                    $shop_status = $shop['shop_status'] ?? 'open';
                                    ?>
                                    <div class="shop-logo-container">
                                        <img src="<?php echo htmlspecialchars($final_logo_path); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">
                                        <div class="verified-badge-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <?php if ($topRated || $mostBooked): ?>
                                        <div class="badge-container">
                                            <?php if ($topRated): ?>
                                                <div class="shop-badge top-rated"><i class="fas fa-star"></i> Top Rated</div>
                                            <?php endif; ?>
                                            <?php if ($mostBooked): ?>
                                                <div class="shop-badge top-booking"><i class="fas fa-calendar-check"></i> Most Booked</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <h5><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                    <p><?php echo htmlspecialchars($shop['shop_location']); ?></p>

                                    <?php if ($shop_status == 'temporarily_closed') : ?>
                                        <div class="shop-status-badge temporarily-closed">
                                            <i class="fas fa-exclamation-triangle"></i> Temporarily Closed
                                        </div>
                                    <?php elseif ($shop_status == 'permanently_closed') : ?>
                                        <div class="shop-status-badge permanently-closed">
                                            <i class="fas fa-store-slash"></i> Permanently Closed
                                        </div>
                                    <?php endif; ?>

                                    <div class="shop-rating">
                                        <?php if ($shop['rating_count'] > 0): ?>
                                            <span class="rating-number"><?php echo number_format($shop['average_rating'], 1); ?></span>
                                            <span class="stars"><?php echo generate_stars($shop['average_rating']); ?></span>
                                            <span class="rating-count">(<?php echo $shop['rating_count']; ?>)</span>
                                        <?php else: ?>
                                            <span>No ratings yet</span>
                                        <?php endif; ?>
                                    </div>
                                    <a href='<?php echo BASE_URL . '/shop/' . htmlspecialchars($shop['shop_slug']); ?>' 
                                       class='btn-view' 
                                       title="View Details">
                                        View Details
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state"><i class="fas fa-wrench"></i>
                                <p>No shops found for this service yet. Please check back soon or try a different search.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="services-highlight-container" id="intro-placeholder">
                        <h3>Featured Partner Services</h3>
                        <?php if (!empty($highlighted_services_data)): ?>
                            <div class="service-highlight-grid">
                                <?php foreach ($highlighted_services_data as $service): ?>
                                    <a href="service/<?php echo htmlspecialchars($service['slug']); ?>"
                                        class="service-highlight-card">
                                        <span class="service-name"><?php echo htmlspecialchars($service['name']); ?></span>
                                        <i class="fas fa-arrow-right service-arrow"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No featured services are available at the moment. Please use the search bar to find a service.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="cta-bottom">
                <div class="cta-bottom-content">
                    <h3>Are You a Shop Owner?</h3>
                    <p>Join Serbisyos today and help drivers in your area find your shop online.</p>
                    <a href="<?php echo BASE_URL; ?>/become-a-partner">Become a Partner</a>
                </div>
                <div class="cta-bottom-img">
                    <img src="<?php echo BASE_URL; ?>/assets/img/partner/shop.webp" alt="Shop owner working on a laptop">
                </div>
            </div>
        </div>
    </div>

    <div id="servicesModal" class="services-modal">
        <div class="services-modal-content">
            <div class="services-modal-header">
                <h4>Browse Services</h4><button type="button" class="services-modal-close">&times;</button>
            </div>
            <div class="services-modal-body">
                <div class="modal-nav">
                    <?php foreach ($service_categories as $category_name => $details): ?>
                        <button class="modal-nav-link"
                            data-category-target="<?php echo create_service_id($category_name); ?>">
                            <i class="fas <?php echo $details['icon']; ?>"></i> <?php echo $category_name; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="modal-main">
                    <?php foreach ($service_categories as $category_name => $details): ?>
                        <div class="modal-category-content" id="<?php echo create_service_id($category_name); ?>"
                            style="display: none;">
                            <?php if (isset($details['subcategories'])): ?>
                                <?php foreach ($details['subcategories'] as $subcategory_name => $services): ?>
                                    <h5><?php echo $subcategory_name; ?></h5>
                                    <ul>
                                        <?php foreach ($services as $service_name => $service_info): ?>
                                            <li class="modal-service-item"
                                                data-service-slug="<?php echo htmlspecialchars($service_info['slug']); ?>">
                                                <?php echo $service_name; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-dropdown-view">
                    <select id="modal-category-select" class="form-select">
                        <option selected disabled>1. Select a Category</option>
                    </select>
                    <select id="modal-subcategory-select" class="form-select" disabled>
                        <option selected disabled>2. Select a Subcategory</option>
                    </select>
                    <select id="modal-service-select" class="form-select" disabled>
                        <option selected disabled>3. Select a Service</option>
                    </select>
                    <button id="modal-go-btn" class="btn btn-primary" disabled>View Shops</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="js/script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceData = <?php echo json_encode($flat_service_list); ?>;
            const fullServiceTree = <?php echo json_encode($service_categories); ?>;
            const searchInput = document.getElementById('service-search-bar');
            const suggestionsContainer = document.querySelector('.autocomplete-suggestions');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                suggestionsContainer.innerHTML = '';
                if (query.length < 2) {
                    suggestionsContainer.style.display = 'none';
                    return;
                }
                const filteredServices = serviceData.filter(service => service.label.toLowerCase().includes(query));
                if (filteredServices.length > 0) {
                    filteredServices.slice(0, 10).forEach(service => {
                        const suggestionItem = document.createElement('div');
                        const regex = new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
                        suggestionItem.innerHTML = service.label.replace(regex, '<strong>$1</strong>');
                        suggestionItem.addEventListener('click', () => {
                            window.location.href = `service/${service.slug}`;
                        });
                        suggestionsContainer.appendChild(suggestionItem);
                    });
                    suggestionsContainer.style.display = 'block';
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.toLowerCase().trim();
                    if (query.length === 0) return;
                    const filteredServices = serviceData.filter(service => service.label.toLowerCase().includes(query));
                    if (filteredServices.length > 0) {
                        const bestMatch = filteredServices[0];
                        window.location.href = `service/${bestMatch.slug}`;
                    }
                }
            });

            document.addEventListener('click', function(e) {
                if (!suggestionsContainer.contains(e.target) && e.target !== searchInput) {
                    suggestionsContainer.style.display = 'none';
                }
            });

            const modal = document.getElementById('servicesModal');
            const browseBtn = document.getElementById('browse-services-btn');
            const closeBtn = document.querySelector('.services-modal-close');
            browseBtn.onclick = () => {
                modal.style.display = 'block';
            };
            closeBtn.onclick = () => {
                modal.style.display = 'none';
            };
            window.onclick = (event) => {
                if (event.target == modal) modal.style.display = "none";
            };

            const navLinks = document.querySelectorAll('.modal-nav-link');
            const categoryContents = document.querySelectorAll('.modal-category-content');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    categoryContents.forEach(content => content.style.display = 'none');
                    document.getElementById(link.dataset.categoryTarget).style.display = 'block';
                });
            });
            if (navLinks.length > 0) navLinks[0].click();

            document.querySelectorAll('.modal-service-item').forEach(item => {
                item.addEventListener('click', () => {
                    window.location.href = `service/${item.dataset.serviceSlug}`;
                });
            });

            const categorySelect = document.getElementById('modal-category-select');
            const subcategorySelect = document.getElementById('modal-subcategory-select');
            const serviceSelect = document.getElementById('modal-service-select');
            const goBtn = document.getElementById('modal-go-btn');

            for (const categoryName in fullServiceTree) {
                categorySelect.add(new Option(categoryName, categoryName));
            }

            categorySelect.addEventListener('change', function() {
                subcategorySelect.innerHTML = '<option selected disabled>2. Select a Subcategory</option>';
                serviceSelect.innerHTML = '<option selected disabled>3. Select a Service</option>';
                subcategorySelect.disabled = true;
                serviceSelect.disabled = true;
                goBtn.disabled = true;
                const subcategories = fullServiceTree[this.value]?.subcategories;
                if (subcategories && Object.keys(subcategories).length > 0) {
                    for (const subcategoryName in subcategories) {
                        subcategorySelect.add(new Option(subcategoryName, subcategoryName));
                    }
                    subcategorySelect.disabled = false;
                }
            });

            subcategorySelect.addEventListener('change', function() {
                serviceSelect.innerHTML = '<option selected disabled>3. Select a Service</option>';
                serviceSelect.disabled = true;
                goBtn.disabled = true;
                const services = fullServiceTree[categorySelect.value]?.subcategories[this.value];
                if (services && Object.keys(services).length > 0) {
                    for (const serviceName in services) {
                        serviceSelect.add(new Option(serviceName, services[serviceName].slug));
                    }
                    serviceSelect.disabled = false;
                }
            });

            serviceSelect.addEventListener('change', () => {
                goBtn.disabled = this.value === '';
            });

            goBtn.addEventListener('click', () => {
                if (serviceSelect.value) {
                    window.location.href = `service/${serviceSelect.value}`;
                }
            });
        });
    </script>
</body>

</html>