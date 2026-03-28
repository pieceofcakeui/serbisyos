<?php
require_once '../functions/auth.php';
include 'backend/base-path.php';
include 'backend/db_connection.php';

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
$category_sql = "SELECT id, name, icon, slug AS category_slug FROM service_categories ORDER BY display_order";
$category_result = $conn->query($category_sql);
while ($category_row = $category_result->fetch_assoc()) {
    $category_id = $category_row['id'];
    $category_name = $category_row['name'];
    $service_categories[$category_name] = [
        'icon' => $category_row['icon'],
        'slug' => $category_row['category_slug'],
        'subcategories' => []
    ];
    $subcategory_sql = "SELECT id, name, slug AS subcategory_slug FROM service_subcategories WHERE category_id = $category_id ORDER BY name";
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
    $stmt_service = $conn->prepare("SELECT id, name FROM services WHERE slug = ?");
    $stmt_service->bind_param("s", $selected_service_slug);
    $stmt_service->execute();
    $service_result = $stmt_service->get_result();

    if ($service_row = $service_result->fetch_assoc()) {
        $selected_service_id = $service_row['id'];
        $selected_service_name = $service_row['name'];
    }
    $stmt_service->close();
}

if ($selected_service_id) {
    $stmt = $conn->prepare("
        SELECT
            sa.id, sa.shop_logo, sa.shop_name, sa.shop_location, sa.shop_slug, sa.shop_status,
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
    <title><?php echo htmlspecialchars($selected_service_name ?: 'Shop Services'); ?></title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/services.css">
     <style>
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
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>
    <div id="main-content" class="main-content">
        <div class="services-section">
            <div class="container">
                <div class="services-header">
                    <h1>Services from Our Trusted Partners</h1>
                    <p>Explore a wide range of automotive services offered by our network of skilled and reliable
                        partner shops.</p>
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
                        <div style="display: flex; justify-content: center;">
                            <a href="<?php echo BASE_URL; ?>/account/service" class="back-to-services-btn">
                                <i class="fas fa-arrow-left"></i> Back to All Services
                            </a>
                        </div>
                        <h2 id="service-title"><?php echo htmlspecialchars($selected_service_name); ?></h2>
                        <div id="shops-grid" class="shops-grid">
                            <?php if (!empty($initial_shops)): ?>
                                <?php foreach ($initial_shops as $shop): ?>
                                    <div class="shop-card">
                                        <?php
                                        $default_logo_url = BASE_URL . '/account/uploads/shop_logo/logo.jpg';
                                        $final_logo_path = $default_logo_url;

                                        if (!empty($shop['shop_logo'])) {
                                            $logo_filename = $shop['shop_logo'];
                                            $logo_filesystem_path = $_SERVER['DOCUMENT_ROOT'] . parse_url(BASE_URL, PHP_URL_PATH) . '/account/uploads/shop_logo/' . $logo_filename;
                                            if (file_exists($logo_filesystem_path)) {
                                                $final_logo_path = BASE_URL . '/account/uploads/shop_logo/' . $logo_filename;
                                            }
                                        }
                                        ?>
                                        <div class="shop-logo-container">
                                            <img src="<?php echo htmlspecialchars($final_logo_path); ?>" alt="<?php echo htmlspecialchars($shop['shop_name']); ?> Logo">
                                            <div class="verified-badge-icon">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <?php
                                        $topRated = ($shop['average_rating'] !== null && $shop['average_rating'] >= 4.0);
                                        $mostBooked = ($shop['completed_bookings'] >= 10);
                                        $shop_status = $shop['shop_status'] ?? 'open';
                                        ?>
                                        <?php if ($topRated || $mostBooked): ?>
                                            <div class="badge-container">
                                                <?php if ($topRated): ?>
                                                    <div class="shop-badge top-rated"><i class="fas fa-star"></i> Top Rated</div>
                                                <?php endif; ?>
                                                <?php if ($mostBooked): ?>
                                                    <div class="shop-badge top-booking"><i class="fas fa-calendar-check"></i> Most Booked
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <h5><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                        <p><?php echo htmlspecialchars($shop['shop_location']); ?></p>

                                        <?php ?>
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
                                                <span
                                                    class="rating-number"><?php echo number_format($shop['average_rating'], 1); ?></span>
                                                <span class="stars"><?php echo generate_stars($shop['average_rating']); ?></span>
                                                <span class="rating-count">(<?php echo $shop['rating_count']; ?>)</span>
                                            <?php else: ?>
                                                <span>No ratings yet</span>
                                            <?php endif; ?>
                                        </div>
                                        <a href='<?php echo BASE_URL; ?>/account/shop/<?php echo htmlspecialchars($shop['shop_slug']); ?>'
                                            class='btn-view'>View Details</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state"><i class="fas fa-wrench"></i>
                                    <p>No shops found for this service yet. Please check back soon or try a different search.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="services-highlight-container" id="intro-placeholder">
                            <h3>Featured Partner Services</h3>
                            <?php if (!empty($highlighted_services_data)): ?>
                                <div class="service-highlight-grid">
                                    <?php foreach ($highlighted_services_data as $service): ?>
                                        <a href="<?php echo BASE_URL; ?>/account/service/<?php echo htmlspecialchars($service['slug']); ?>"
                                            class="service-highlight-card">
                                            <span class="service-name"><?php echo htmlspecialchars($service['name']); ?></span>
                                            <i class="fas fa-arrow-right service-arrow"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="text-align: center;">No featured services are available at the moment. Please use the
                                    search bar to find a service.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="cta-bottom">
                    <div class="cta-bottom-content">
                        <h3>Are You a Shop Owner?</h3>
                        <p>Join Serbisyos today and help drivers in your area find your shop online.</p>
                        <a href="<?php echo BASE_URL; ?>/account/become-a-partner">Become a Partner</a>
                    </div>
                    <div class="cta-bottom-img">
                        <img src="<?php echo BASE_URL; ?>/assets/img/partner/shop.webp"
                            alt="Shop owner working on a laptop">
                    </div>
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
                    <button id="modal-go-btn" class="btn btn-primary" disabled>View Details</button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'include/emergency-modal.php'; ?>
    <?php include 'include/help-toggle.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/navbar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceData = <?php echo json_encode($flat_service_list); ?>;
            const fullServiceTree = <?php echo json_encode($service_categories); ?>;
            const searchInput = document.getElementById('service-search-bar');
            const suggestionsContainer = document.querySelector('.autocomplete-suggestions');
            const base_url = '<?php echo BASE_URL; ?>/account/service/';

            searchInput.addEventListener('input', function () {
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
                            window.location.href = base_url + service.slug;
                        });
                        suggestionsContainer.appendChild(suggestionItem);
                    });
                    suggestionsContainer.style.display = 'block';
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            });

            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.toLowerCase().trim();
                    if (query.length === 0) return;
                    const filteredServices = serviceData.filter(service => service.label.toLowerCase().includes(query));
                    if (filteredServices.length > 0) {
                        const bestMatch = filteredServices[0];
                        window.location.href = base_url + bestMatch.slug;
                    }
                }
            });

            document.querySelectorAll('.modal-service-item').forEach(item => {
                item.addEventListener('click', () => {
                    const serviceSlug = item.dataset.serviceSlug;
                    window.location.href = base_url + serviceSlug;
                });
            });

            const goBtn = document.getElementById('modal-go-btn');
            goBtn.addEventListener('click', function () {
                const serviceSelect = document.getElementById('modal-service-select');
                const selectedServiceSlug = serviceSelect.value;
                if (selectedServiceSlug) {
                    window.location.href = base_url + selectedServiceSlug;
                }
            });

            const categorySelect = document.getElementById('modal-category-select');
            const subcategorySelect = document.getElementById('modal-subcategory-select');
            const serviceSelect = document.getElementById('modal-service-select');

            for (const categoryName in fullServiceTree) {
                const option = new Option(categoryName, categoryName);
                categorySelect.add(option);
            }

            categorySelect.addEventListener('change', function () {
                const selectedCategory = this.value;
                subcategorySelect.innerHTML = '<option selected disabled>2. Select a Subcategory</option>';
                serviceSelect.innerHTML = '<option selected disabled>3. Select a Service</option>';
                subcategorySelect.disabled = true;
                serviceSelect.disabled = true;
                goBtn.disabled = true;
                if (selectedCategory && fullServiceTree[selectedCategory]) {
                    const subcategories = fullServiceTree[selectedCategory].subcategories;
                    if (Object.keys(subcategories).length > 0) {
                        for (const subcategoryName in subcategories) {
                            const option = new Option(subcategoryName, subcategoryName);
                            subcategorySelect.add(option);
                        }
                        subcategorySelect.disabled = false;
                    }
                }
            });

            subcategorySelect.addEventListener('change', function () {
                const selectedCategory = categorySelect.value;
                const selectedSubcategory = this.value;
                serviceSelect.innerHTML = '<option selected disabled>3. Select a Service</option>';
                serviceSelect.disabled = true;
                goBtn.disabled = true;
                if (selectedCategory && selectedSubcategory && fullServiceTree[selectedCategory].subcategories[selectedSubcategory]) {
                    const services = fullServiceTree[selectedCategory].subcategories[selectedSubcategory];
                    if (Object.keys(services).length > 0) {
                        for (const serviceName in services) {
                            const serviceSlug = services[serviceName].slug;
                            const option = new Option(serviceName, serviceSlug);
                            serviceSelect.add(option);
                        }
                        serviceSelect.disabled = false;
                    }
                }
            });

            serviceSelect.addEventListener('change', function () {
                goBtn.disabled = this.value === '';
            });

            document.addEventListener('click', function (e) {
                if (!suggestionsContainer.contains(e.target) && e.target !== searchInput) {
                    suggestionsContainer.style.display = 'none';
                }
            });
            const modal = document.getElementById('servicesModal');
            const browseBtn = document.getElementById('browse-services-btn');
            const closeBtn = document.querySelector('.services-modal-close');
            browseBtn.onclick = function () { modal.style.display = 'block'; }
            closeBtn.onclick = function () { modal.style.display = 'none'; }
            window.onclick = function (event) { if (event.target == modal) { modal.style.display = "none"; } }
            const navLinks = document.querySelectorAll('.modal-nav-link');
            const categoryContents = document.querySelectorAll('.modal-category-content');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    categoryContents.forEach(content => content.style.display = 'none');
                    const targetId = link.getAttribute('data-category-target');
                    document.getElementById(targetId).style.display = 'block';
                });
            });
            if (navLinks.length > 0) {
                navLinks[0].click();
            }
        });
    </script>
</body>

</html>