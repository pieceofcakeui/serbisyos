<?php
include 'backend/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = $_POST['shop_name'];
    $reviewer_name = $_POST['reviewer_name'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $review_date = date('Y-m-d H:i:s');

    if (!empty($shop_name) && !empty($reviewer_name) && !empty($rating) && !empty($comment)) {
        $sql = "INSERT INTO reviews (shop_name, reviewer_name, rating, comment, review_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $shop_name, $reviewer_name, $rating, $comment, $review_date);

        if ($stmt->execute()) {
            echo "Review submitted successfully!";
        } else {
            echo "Error submitting review: " . $stmt->error;
        }
    } else {
        echo "All fields are required.";
    }
}
?>