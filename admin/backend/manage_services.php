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


function redirect_with_message($message, $type = 'success')
{
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
    header('Location: ../manage-services.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add_category') {
        $name = $_POST['category_name'];
        $icon = $_POST['category_icon'];
        $slug = generateSlug($name);

        $result = $conn->query("SELECT MAX(display_order) as max_order FROM service_categories");
        $row = $result->fetch_assoc();
        $max_order = $row['max_order'] ?? 0;
        $new_display_order = $max_order + 10;

        $stmt = $conn->prepare("INSERT INTO service_categories (name, slug, icon, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $slug, $icon, $new_display_order);

        if ($stmt->execute()) {
            redirect_with_message('Category added successfully.');
        } else {
            redirect_with_message('Error adding category: ' . $stmt->error, 'danger');
        }
    }

    if ($action == 'edit_category') {
        $id = $_POST['category_id'];
        $name = $_POST['category_name'];
        $icon = $_POST['category_icon'];
        $display_order = $_POST['display_order'];
        $slug = generateSlug($name);

        $stmt = $conn->prepare("UPDATE service_categories SET name = ?, slug = ?, icon = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $slug, $icon, $display_order, $id);
        if ($stmt->execute()) {
            redirect_with_message('Category updated successfully.');
        } else {
            redirect_with_message('Error updating category: ' . $stmt->error, 'danger');
        }
    }

    if ($action == 'add_subcategory') {
        $category_id = $_POST['parent_category_id'];
        $name = $_POST['subcategory_name'];
        $slug = generateSlug($name);

        $stmt = $conn->prepare("INSERT INTO service_subcategories (category_id, name, slug) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $category_id, $name, $slug);
        if ($stmt->execute()) {
            redirect_with_message('Subcategory added successfully.');
        } else {
            redirect_with_message('Error adding subcategory: ' . $stmt->error, 'danger');
        }
    }

    if ($action == 'edit_subcategory') {
        $id = $_POST['subcategory_id'];
        $name = $_POST['subcategory_name'];
        $slug = generateSlug($name);

        $stmt = $conn->prepare("UPDATE service_subcategories SET name = ?, slug = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $slug, $id);
        if ($stmt->execute()) {
            redirect_with_message('Subcategory updated successfully.');
        } else {
            redirect_with_message('Error updating subcategory: ' . $stmt->error, 'danger');
        }
    }

    if ($action == 'add_service') {
        $subcategory_id = $_POST['parent_subcategory_id'];
        $name = $_POST['service_name'];
        $query_term = $_POST['query_term'];
        $slug = generateSlug($name);

        $stmt = $conn->prepare("INSERT INTO services (subcategory_id, name, slug, query_term) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $subcategory_id, $name, $slug, $query_term);
        if ($stmt->execute()) {
            redirect_with_message('Service added successfully.');
        } else {
            redirect_with_message('Error adding service: ' . $stmt->error, 'danger');
        }
    }

    if ($action == 'edit_service') {
        $id = $_POST['service_id'];
        $name = $_POST['service_name'];
        $query_term = $_POST['query_term'];
        $slug = generateSlug($name);

        $stmt = $conn->prepare("UPDATE services SET name = ?, slug = ?, query_term = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $slug, $query_term, $id);
        if ($stmt->execute()) {
            redirect_with_message('Service updated successfully.');
        } else {
            redirect_with_message('Error updating service: ' . $stmt->error, 'danger');
        }
    }

    if ($action == 'delete_item') {
        $id = $_POST['delete_id'];
        $type = $_POST['delete_type'];
        $table = '';

        switch ($type) {
            case 'category':
                $table = 'service_categories';
                break;
            case 'subcategory':
                $table = 'service_subcategories';
                break;
            case 'service':
                $table = 'services';
                break;
        }

        if ($table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                redirect_with_message(ucfirst($type) . ' deleted successfully.');
            } else {
                redirect_with_message('Error deleting ' . $type . ': ' . $stmt->error, 'danger');
            }
        }
    }
}

$conn->close();
?>