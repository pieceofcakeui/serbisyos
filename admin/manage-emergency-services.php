<?php
session_start();
include 'backend/auth.php'; 
include 'backend/db_connection.php';

$categories_result = $conn->query("SELECT * FROM emergency_categories ORDER BY display_order, name");
$subcategories_result = $conn->query("SELECT * FROM emergency_subcategories ORDER BY name");
$services_result = $conn->query("SELECT * FROM emergency_assistance_services ORDER BY name");

$data = [];

while ($cat = $categories_result->fetch_assoc()) {
    $data[$cat['id']] = $cat;
    $data[$cat['id']]['subcategories'] = [];
}

while ($sub = $subcategories_result->fetch_assoc()) {
    if (isset($data[$sub['category_id']])) {
        $data[$sub['category_id']]['subcategories'][$sub['id']] = $sub;
        $data[$sub['category_id']]['subcategories'][$sub['id']]['services'] = [];
    }
}

while ($ser = $services_result->fetch_assoc()) {
    foreach ($data as $cat_id => $category) {
        if (isset($category['subcategories'][$ser['subcategory_id']])) {
            $data[$cat_id]['subcategories'][$ser['subcategory_id']]['services'][] = $ser;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Emergency Services</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/manage-emergency-services.css">

</head>
<body>
    <?php include 'include/offline-handler.php'; ?>
    <div class="d-flex">
        
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>

       <div class="content flex-grow-1">
            <?php include 'include/navbar.php'; ?>
            <div class="container-fluid">
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <h1 style="margin-top: 30px;">Manage Emergency Services</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="prepareCategoryModal('add')">
                        <i class="fas fa-plus"></i><span class="btn-label"> Add New Category</span>
                    </button>
                </div>
                <?php
                if (isset($_SESSION['message'])) {
                    $message = $_SESSION['message'];
                    $message_text = addslashes($message['text']);
                    $message_type = $message['type'];
                    echo "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const toastElement = document.getElementById('appToast');
                            const toastBody = toastElement.querySelector('.toast-body');
                            toastBody.textContent = '{$message_text}';
                            toastElement.classList.remove('bg-success', 'bg-danger', 'text-white');
                            if ('{$message_type}' === 'success') {
                                toastElement.classList.add('bg-success', 'text-white');
                            } else {
                                toastElement.classList.add('bg-danger', 'text-white');
                            }
                            const appToast = new bootstrap.Toast(toastElement, { delay: 3000, autohide: true });
                            appToast.show();
                        });
                    </script>
                    ";
                    unset($_SESSION['message']);
                }
                ?>
                <div class="accordion" id="mainAccordion">
                    <?php foreach ($data as $category): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCat<?php echo $category['id']; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat<?php echo $category['id']; ?>">
                                    <i class="fas <?php echo htmlspecialchars($category['icon']); ?> me-2"></i> <?php echo htmlspecialchars($category['name']); ?>
                                </button>
                                <div class="btn-group gap-1">
                                    <button class="btn btn-success btn-sm" onclick="prepareSubCategoryModal('add', '<?php echo $category['id']; ?>')">
                                        <i class="fas fa-plus"></i><span class="btn-label"></span>
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="prepareCategoryModal('edit', '<?php echo $category['id']; ?>', '<?php echo htmlspecialchars(addslashes($category['name'])); ?>', '<?php echo htmlspecialchars(addslashes($category['icon'])); ?>', '<?php echo $category['display_order']; ?>')">
                                        <i class="fas fa-edit"></i><span class="btn-label"></span>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="prepareDeleteModal('category', '<?php echo $category['id']; ?>', '<?php echo htmlspecialchars(addslashes($category['name'])); ?>')">
                                        <i class="fas fa-trash"></i><span class="btn-label"></span>
                                    </button>
                                </div>
                            </h2>
                            <div id="collapseCat<?php echo $category['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#mainAccordion">
                                <div class="accordion-body">
                                    <div class="accordion" id="subAccordion<?php echo $category['id']; ?>">
                                        <?php if (!empty($category['subcategories'])): ?>
                                            <?php foreach ($category['subcategories'] as $subcategory): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingSub<?php echo $subcategory['id']; ?>">
                                                         <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSub<?php echo $subcategory['id']; ?>">
                                                             <?php echo htmlspecialchars($subcategory['name']); ?>
                                                         </button>
                                                        <div class="btn-group gap-1">
                                                             <button class="btn btn-success btn-sm" onclick="prepareServiceModal('add', '<?php echo $subcategory['id']; ?>')">
                                                                 <i class="fas fa-plus"></i><span class="btn-label"></span>
                                                             </button>
                                                            <button class="btn btn-info btn-sm" onclick="prepareSubCategoryModal('edit', '<?php echo $category['id']; ?>', '<?php echo $subcategory['id']; ?>', '<?php echo htmlspecialchars(addslashes($subcategory['name'])); ?>')">
                                                                <i class="fas fa-edit"></i><span class="btn-label"></span>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm" onclick="prepareDeleteModal('subcategory', '<?php echo $subcategory['id']; ?>', '<?php echo htmlspecialchars(addslashes($subcategory['name'])); ?>')">
                                                                <i class="fas fa-trash"></i><span class="btn-label"></span>
                                                            </button>
                                                        </div>
                                                    </h2>
                                                    <div id="collapseSub<?php echo $subcategory['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#subAccordion<?php echo $category['id']; ?>">
                                                        <div class="accordion-body">
                                                            <ul class="list-group">
                                                                <?php if (!empty($subcategory['services'])): ?>
                                                                    <?php foreach ($subcategory['services'] as $service): ?>
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                            <span><?php echo htmlspecialchars($service['name']); ?> (Value: <code><?php echo htmlspecialchars($service['value']); ?></code>)</span>
                                                                           <div class="btn-group gap-1">
                                                                                 <button class="btn btn-info btn-sm" onclick="prepareServiceModal('edit', '<?php echo $subcategory['id']; ?>', '<?php echo $service['id']; ?>', '<?php echo htmlspecialchars(addslashes($service['name'])); ?>', '<?php echo htmlspecialchars(addslashes($service['value'])); ?>')">
                                                                                     <i class="fas fa-edit"></i><span class="btn-label"></span>
                                                                                </button>
                                                                                <button class="btn btn-danger btn-sm" onclick="prepareDeleteModal('service', '<?php echo $service['id']; ?>', '<?php echo htmlspecialchars(addslashes($service['name'])); ?>')">
                                                                                    <i class="fas fa-trash"></i><span class="btn-label"></span>
                                                                                </button>
                                                                            </div>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                     <li class="list-group-item">No services listed here.</li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p>No subcategories found. Click '+' on the category header to add one.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

          <?php include 'include/footer.php'; ?>
        </div>
    </div>
    
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="backend/manage_emergency_handler.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalTitle">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="categoryAction">
                        <input type="hidden" name="category_id" id="categoryId">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryIcon" class="form-label">Font Awesome Icon Class (e.g., fas fa-truck)</label>
                            <input type="text" class="form-control" id="categoryIcon" name="category_icon" required>
                        </div>
                         <div class="mb-3" id="displayOrderWrapper">
                            <label for="displayOrder" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="displayOrder" name="display_order" value="10" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="subcategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="backend/manage_emergency_handler.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="subcategoryModalTitle">Add New Subcategory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="subcategoryAction">
                        <input type="hidden" name="parent_category_id" id="parentCategoryId">
                        <input type="hidden" name="subcategory_id" id="subcategoryId">
                        <div class="mb-3">
                            <label for="subcategoryName" class="form-label">Subcategory Name</label>
                            <input type="text" class="form-control" id="subcategoryName" name="subcategory_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="backend/manage_emergency_handler.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceModalTitle">Add New Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                         <input type="hidden" name="action" id="serviceAction">
                        <input type="hidden" name="parent_subcategory_id" id="parentSubcategoryId">
                        <input type="hidden" name="service_id" id="serviceId">
                        <div class="mb-3">
                            <label for="serviceName" class="form-label">Service Display Name</label>
                            <input type="text" class="form-control" id="serviceName" name="service_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceValue" class="form-label">Service Value</label>
                            <input type="text" class="form-control" id="serviceValue" name="service_value" placeholder="e.g., flatbed_towing (no spaces, unique)" required>
                            <div class="form-text">This is the unique identifier used by the system.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                 <form action="backend/manage_emergency_handler.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="delete_id" id="deleteId">
                        <input type="hidden" name="delete_type" id="deleteType">
                        <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
                        <p class="text-danger"><small>This action cannot be undone. Deleting a category will also delete its subcategories and services.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
        <div id="appToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body"></div>
        </div>
    </div>

    <?php include 'include/back-to-top.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const subcategoryModal = new bootstrap.Modal(document.getElementById('subcategoryModal'));
        const serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        function prepareCategoryModal(mode, id = '', name = '', icon = 'fas fa-cogs', order = '10') {
            const form = document.querySelector('#categoryModal form');
            const displayOrderWrapper = document.getElementById('displayOrderWrapper');
            form.reset();
            if (mode === 'add') {
                form.querySelector('#categoryModalTitle').innerText = 'Add New Category';
                form.querySelector('#categoryAction').value = 'add_category';
                 displayOrderWrapper.style.display = 'none';
            } else {
                form.querySelector('#categoryModalTitle').innerText = 'Edit Category';
                form.querySelector('#categoryAction').value = 'edit_category';
                form.querySelector('#categoryId').value = id;
                form.querySelector('#categoryName').value = name;
                form.querySelector('#categoryIcon').value = icon;
                form.querySelector('#displayOrder').value = order;
                displayOrderWrapper.style.display = 'block';
            }
            categoryModal.show();
        }
        
        function prepareSubCategoryModal(mode, parentCatId, subcatId = '', name = '') {
            const form = document.querySelector('#subcategoryModal form');
            form.reset();
            form.querySelector('#parentCategoryId').value = parentCatId;
            if (mode === 'add') {
                form.querySelector('#subcategoryModalTitle').innerText = 'Add New Subcategory';
                form.querySelector('#subcategoryAction').value = 'add_subcategory';
            } else {
                form.querySelector('#subcategoryModalTitle').innerText = 'Edit Subcategory';
                form.querySelector('#subcategoryAction').value = 'edit_subcategory';
                form.querySelector('#subcategoryId').value = subcatId;
                form.querySelector('#subcategoryName').value = name;
            }
            subcategoryModal.show();
        }

        function prepareServiceModal(mode, parentSubcatId, serviceId = '', name = '', serviceValue = '') {
            const form = document.querySelector('#serviceModal form');
            form.reset();
            form.querySelector('#parentSubcategoryId').value = parentSubcatId;
            if (mode === 'add') {
                form.querySelector('#serviceModalTitle').innerText = 'Add New Service';
                form.querySelector('#serviceAction').value = 'add_service';
            } else {
                form.querySelector('#serviceModalTitle').innerText = 'Edit Service';
                form.querySelector('#serviceAction').value = 'edit_service';
                form.querySelector('#serviceId').value = serviceId;
                form.querySelector('#serviceName').value = name;
                form.querySelector('#serviceValue').value = serviceValue;
            }
            serviceModal.show();
        }

        function prepareDeleteModal(type, id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteType').value = type;
            document.getElementById('deleteItemName').innerText = name;
            deleteModal.show();
        }
    </script>
</body>
</html>