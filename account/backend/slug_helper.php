<?php
function createSlug($conn, $text) {
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');

    $baseSlug = $slug;
    $counter = 1;
    while (true) {
        $query = "SELECT id FROM shop_applications WHERE shop_slug = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt->close();
            return $slug;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $counter++;
        $stmt->close();
    }
}
?>