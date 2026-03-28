<?php
include 'db_connection.php';
require '../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
include 'config.php';
?>
<style>
    .application-details {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }
    .application-details strong {
        color: #2c3e50;
        font-weight: 600;
    }
    .status-approved, .status-rejected, .status-pending {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        font-size: 0.85em;
    }
    .status-approved { color: #155724; background-color: #d4edda; }
    .status-rejected { color: #721c24; background-color: #f8d7da; }
    .status-pending { color: #856404; background-color: #fff3cd; }
    .img-preview {
        height: 150px;
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        object-fit: contain;
    }
    .img-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .section-divider {
        border-top: 1px solid #eee;
        margin: 25px 0;
    }
    .image-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 10px;
    }
    .image-item {
        flex: 1 1 30%;
        min-width: 200px;
        text-align: center;
    }
    .image-label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #2c3e50;
    }
    .no-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 162px;
        border: 2px dashed #e0e0e0;
        border-radius: 4px;
        background-color: #f9f9f9;
        color: #aaa;
        font-size: 0.9em;
    }
    .no-upload svg {
        width: 40px;
        height: 40px;
        margin-bottom: 10px;
        color: #ccc;
    }
    .hours-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-top: 15px;
    }
    .hours-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .hours-label {
        font-weight: 600;
        color: #495057;
    }
    .embed-responsive {
        position: relative;
        display: block;
        width: 100%;
        padding: 0;
        overflow: hidden;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .embed-responsive::before {
        display: block;
        content: "";
        padding-top: 56.25%;
    }
    .embed-responsive iframe {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    .clean-modal .modal-content {
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        border: none;
    }
    .clean-modal .modal-header { border-bottom: 1px solid #f0f0f0; }
    .clean-modal .modal-title { font-weight: 600; }
    .clean-modal .modal-footer { border-top: 1px solid #f0f0f0; background-color: #f7f7f7;}
    .edit-form textarea {
        min-height: 120px;
        border-radius: 8px;
    }
    .edit-form button {
        padding: 10px 20px;
        border-radius: 8px;
    }
    .edit-icon {
        cursor: pointer;
        color: #007bff;
        margin-left: 8px;
        transition: color 0.2s;
    }
    .edit-icon:hover { color: #0056b3; }
    .modal-img {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.9);
        padding-top: 60px;
    }
    .modal-img-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        max-height: 90vh;
        object-fit: contain;
    }
    .modal-img .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }
    #notificationModal .modal-content {
        border-radius: 16px;
    }
    #notificationModal .modal-body {
        position: relative;
        padding: 3rem 2rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    #notificationModal .btn-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        --bs-btn-close-focus-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
    }
    #notificationModal .modal-icon {
        font-size: 3.5rem;
        line-height: 1;
        margin-bottom: 1.5rem;
    }
    #notificationModal .modal-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    #notificationModal .modal-message {
        font-size: 1rem;
        color: #6c757d;
    }
    #notificationModal .text-success { color: #198754 !important; }
    #notificationModal .text-danger { color: #dc3545 !important; }
</style>

<?php
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM shop_applications WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $decrypted_business_permit = !empty($row['business_permit']) ? decryptData($row['business_permit']) : 'N/A';
        $decrypted_tax_id = !empty($row['tax_id']) ? decryptData($row['tax_id']) : 'N/A';
        $decrypted_dti_sec = !empty($row['dti_sec_number']) ? decryptData($row['dti_sec_number']) : 'N/A';
        $statusClass = 'status-' . strtolower($row['status']);
        $application_id = $row['id'];
        $organized_services = [];
        $sql_services = "SELECT sc.name AS category_name, sc.icon AS category_icon, sc.display_order, ssc.name AS subcategory_name, s.name AS service_name FROM shop_services ss JOIN services s ON ss.service_id = s.id JOIN service_subcategories ssc ON s.subcategory_id = ssc.id JOIN service_categories sc ON ssc.category_id = sc.id WHERE ss.application_id = ? ORDER BY sc.display_order, sc.name, ssc.name, s.name";
        $stmt_services = $conn->prepare($sql_services);
        $stmt_services->bind_param("i", $application_id);
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
        $openingTimeAM = !empty($row['opening_time_am']) ? date("g:i A", strtotime($row['opening_time_am'])) : 'N/A';
        $closingTimeAM = !empty($row['closing_time_am']) ? date("g:i A", strtotime($row['closing_time_am'])) : 'N/A';
        $morningHours = "$openingTimeAM - $closingTimeAM";
        $afternoonHours = null;
        if (!empty($row['opening_time_pm']) && !empty($row['closing_time_pm'])) {
            $openingTimePM = date("g:i A", strtotime($row['opening_time_pm']));
            $closingTimePM = date("g:i A", strtotime($row['closing_time_pm']));
            $afternoonHours = "$openingTimePM - $closingTimePM";
        }
        $daysOpen = !empty($row['days_open']) ? str_replace(',', ', ', ucwords($row['days_open'])) : 'Not specified';

        $shop_location_js = htmlspecialchars($row['shop_location'], ENT_QUOTES, 'UTF-8');

        echo "<div class='application-details'><div class='row'><div class='col-md-6'><strong>Shop Name:</strong> " . htmlspecialchars($row['shop_name']) . "<br><strong>Owner Name:</strong> " . htmlspecialchars($row['owner_name']) . "<br><strong>Years in Operation:</strong> " . htmlspecialchars($row['years_operation']) . "<br><strong>Email:</strong> " . htmlspecialchars($row['email']) . "<br><strong>Phone:</strong> " . htmlspecialchars($row['phone']) . "<br><strong>Business Permit No:</strong> " . htmlspecialchars($decrypted_business_permit) . "<br><strong>Tax ID No (TIN):</strong> " . htmlspecialchars($decrypted_tax_id) . "<br><strong>DTI/SEC No:</strong> " . htmlspecialchars($decrypted_dti_sec) . "<br></div><div class='col-md-6'><strong>City:</strong> " . htmlspecialchars($row['town_city']) . "<br><strong>Province:</strong> " . htmlspecialchars($row['province']) . "<br><strong>Postal Code:</strong> " . htmlspecialchars($row['postal_code']) . "<br><strong>Shop Location:</strong> <span id='current-address'>" . htmlspecialchars($row['shop_location']) . "</span><br><strong>Brands Serviced:</strong> " . htmlspecialchars($row['brands_serviced']) . "<br><strong>Valid ID Type:</strong> " . $row['valid_id_type'] . "<br><strong>Status:</strong> <span class='$statusClass'>" . htmlspecialchars($row['status']) . "</span><br></div></div>";
        echo "<div class='section-divider'></div><h5>Services Offered</h5>";
        if (!empty($organized_services)) {
            echo '<div class="row services-grid">';
            $accordion_counter = 0;
            foreach ($organized_services as $category_name => $category_data) {
                $accordion_id = 'serviceCat' . $accordion_counter;
                echo '<div class="col-lg-4 col-md-6 mb-4">';
                echo '  <div class="accordion" id="accordion-' . $accordion_id . '"><div class="accordion-item"><h2 class="accordion-header" id="heading-' . $accordion_id . '"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $accordion_id . '"><i class="fas ' . htmlspecialchars($category_data['icon']) . ' me-2 text-warning"></i>' . htmlspecialchars($category_name) . '</button></h2><div id="collapse-' . $accordion_id . '" class="accordion-collapse collapse" data-bs-parent="#accordion-' . $accordion_id . '"><div class="accordion-body">';
                if (empty($category_data['subcategories'])) {
                    echo '<p class="text-muted small">No specific services listed under this category.</p>';
                } else {
                    foreach ($category_data['subcategories'] as $subcategory_name => $services) {
                        $service_count = count($services);
                        echo "<div class='service-subcategory-group mt-2'>";
                        echo "  <p class='subcategory-title mb-1' style='font-weight: 500;'>" . htmlspecialchars($subcategory_name) . " <span class='text-muted small'>($service_count)</span></p>";
                        echo "  <ol class='service-list ps-4'>";
                        foreach ($services as $service_name) {
                            echo "<li><small>" . htmlspecialchars($service_name) . "</small></li>";
                        }
                        echo "  </ol>";
                        echo "</div>";
                    }
                }
                echo '</div></div></div></div></div>';
                $accordion_counter++;
            }
            echo '</div>';
        } else {
            echo "<p>No services listed for this applicant.</p>";
        }

        echo "<div class='section-divider'></div><div class='hours-container'><h5>Business Hours</h5><div class='hours-item'><span class='hours-label'>Morning Schedule:</span><span>" . $morningHours . "</span></div>";
        if ($afternoonHours) {
            echo "<div class='hours-item'><span class='hours-label'>Afternoon Schedule:</span><span>" . $afternoonHours . "</span></div>";
        }
        echo "<div class='hours-item'><span class='hours-label'>Days Open:</span><span>$daysOpen</span></div></div>";
        echo "<div class='section-divider'></div><div class='row'><div class='col-md-12'><h5>Uploaded Documents</h5><div class='image-container'>";
        function renderDocument($label, $filename, $path)
        {
            echo "<div class='image-item'><span class='image-label'>$label</span>";
            if (!empty($filename)) {
                $fullPath = $path . basename($filename);
                echo "<img src='$fullPath' class='img-preview' onclick='openImageModal(this.src)' alt='$label'>";
            } else {
                echo "<div class='no-upload'><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' viewBox='0 0 16 16'><path d='M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z'/><path d='M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h1.083A2 2 0 0 1 7 0h2a2 2 0 0 1 1.917 1H12a2 2 0 0 1 2 2v8zM1 5a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H1z'/></svg>Not Uploaded</div>";
            }
            echo "</div>";
        }
        renderDocument('Business Logo', $row['shop_logo'], '../account/uploads/shop_logo/');
        renderDocument('Business Permit', $row['business_permit_file'], '../account/uploads/business_permit/');
        renderDocument('Tax ID File', $row['tax_id_file'], '../account/uploads/tax_id/');
        renderDocument('DTI/SEC File', $row['dti_sec_file'], '../account/uploads/dti_sec/');
        renderDocument('Valid ID (Front)', $row['valid_id_front'], '../account/uploads/valid_id_front/');
        renderDocument('Valid ID (Back)', $row['valid_id_back'], '../account/uploads/valid_id_back/');
        echo "</div></div></div></div>";
    } else {
        echo "<div class='alert alert-warning'>No application found.</div>";
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
}
$conn->close();
?>

<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div id="notificationModalIcon" class="modal-icon"></div>
                <h5 id="notificationModalTitle" class="modal-title"></h5>
                <p id="notificationModalMessage" class="modal-message"></p>
            </div>
        </div>
    </div>
</div>

<div id='imageModal' class='modal-img' onclick='closeImageModal()'><span class='close'>&times;</span><img class='modal-img-content' id='modalImage'></div>

<script>
    const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));

    function openImageModal(src) {
        document.getElementById('imageModal').style.display = 'block';
        document.getElementById('modalImage').src = src;
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
    
    function showNotificationModal(message, type = 'success') {
        const modalTitle = document.getElementById('notificationModalTitle');
        const modalIcon = document.getElementById('notificationModalIcon');
        const modalMessage = document.getElementById('notificationModalMessage');

        if (type === 'success') {
            modalTitle.textContent = 'Success';
            modalTitle.className = 'modal-title text-success';
            modalIcon.className = 'modal-icon fas fa-check-circle text-success';
        } else {
            modalTitle.textContent = 'Error';
            modalTitle.className = 'modal-title text-danger';
            modalIcon.className = 'modal-icon fas fa-times-circle text-danger';
        }
        modalMessage.textContent = message;
        notificationModal.show();
    }

    window.onclick = function(event) {
        const imgModal = document.getElementById('imageModal');
        if (event.target == imgModal) {
            closeImageModal();
        }
    }
</script>