
<?php
function searchServices($searchTerm)
{
  global $conn;

  $stmt = $conn->prepare("SELECT * FROM shop_applications WHERE services_offered LIKE ?");
  $searchTerm = '%' . $searchTerm . '%';
  $stmt->bind_param("s", $searchTerm);
  $stmt->execute();
  $result = $stmt->get_result();

  $shops = [];
  while ($row = $result->fetch_assoc()) {
    $shops[] = $row;
  }

  $stmt->close();

  if (empty($shops)) {
    return ['status' => 'error', 'message' => 'No shop offered this service.'];
  } else {
    return ['status' => 'success', 'data' => $shops];
  }
}
?>