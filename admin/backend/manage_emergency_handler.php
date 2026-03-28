<?php
session_start();
include 'db_connection.php';

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    $text = preg_replace('/-+/', '-', $text);
    if (empty($text)) {
        return 'n-a-' . time();
    }
    return $text;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {

        case 'add_category':
            $name = $_POST['category_name'];
            $icon = $_POST['category_icon'];
            $slug = generateSlug($name);
            $stmt = $conn->prepare("INSERT INTO emergency_categories (name, slug, icon) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $slug, $icon);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['text' => 'Category added successfully.', 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => 'Error adding category.', 'type' => 'error'];
            }
            $stmt->close();
            break;

        case 'edit_category':
            $id = $_POST['category_id'];
            $name = $_POST['category_name'];
            $icon = $_POST['category_icon'];
            $order = $_POST['display_order'];
            $slug = generateSlug($name);
            $stmt = $conn->prepare("UPDATE emergency_categories SET name = ?, slug = ?, icon = ?, display_order = ? WHERE id = ?");
            $stmt->bind_param("sssii", $name, $slug, $icon, $order, $id);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['text' => 'Category updated successfully.', 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => 'Error updating category.', 'type' => 'error'];
            }
            $stmt->close();
            break;

        case 'add_subcategory':
            $cat_id = $_POST['parent_category_id'];
            $name = $_POST['subcategory_name'];
            $slug = generateSlug($name);
            $stmt = $conn->prepare("INSERT INTO emergency_subcategories (category_id, name, slug) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $cat_id, $name, $slug);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['text' => 'Subcategory added successfully.', 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => 'Error adding subcategory.', 'type' => 'error'];
            }
            $stmt->close();
            break;
            
        case 'edit_subcategory':
            $id = $_POST['subcategory_id'];
            $name = $_POST['subcategory_name'];
            $slug = generateSlug($name);
            $stmt = $conn->prepare("UPDATE emergency_subcategories SET name = ?, slug = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $slug, $id);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['text' => 'Subcategory updated successfully.', 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => 'Error updating subcategory.', 'type' => 'error'];
            }
            $stmt->close();
            break;

        case 'add_service':
            $subcat_id = $_POST['parent_subcategory_id'];
            $name = $_POST['service_name'];
            $value = $_POST['service_value'];
            $slug = generateSlug($name);
            $stmt = $conn->prepare("INSERT INTO emergency_assistance_services (subcategory_id, name, slug, value) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $subcat_id, $name, $slug, $value);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['text' => 'Service added successfully.', 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => 'Error: Service value might already exist.', 'type' => 'error'];
            }
            $stmt->close();
            break;

        case 'edit_service':
            $id = $_POST['service_id'];
            $name = $_POST['service_name'];
            $value = $_POST['service_value'];
            $slug = generateSlug($name);
            $stmt = $conn->prepare("UPDATE emergency_assistance_services SET name = ?, slug = ?, value = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $slug, $value, $id);
            if ($stmt->execute()) {
                $_SESSION['message'] = ['text' => 'Service updated successfully.', 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => 'Error: Service value might already exist.', 'type' => 'error'];
            }
            $stmt->close();
            break;

        case 'delete_item':
            $id = $_POST['delete_id'];
            $type = $_POST['delete_type'];
            $table = '';
            switch ($type) {
                case 'category': $table = 'emergency_categories'; break;
                case 'subcategory': $table = 'emergency_subcategories'; break;
                case 'service': $table = 'emergency_assistance_services'; break;
            }
            if ($table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
                $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = ['text' => ucfirst($type) . ' deleted successfully.', 'type' => 'success'];
                } else {
                        $_SESSION['message'] = ['text' => 'Error deleting item.', 'type' => 'error'];
                }
                $stmt->close();
            }
            break;
    }
}

header('Location: ../manage-emergency-services.php');
exit();
?>