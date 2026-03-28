<?php
$services = explode(",", $row['services_offered']);
shuffle($services);
$services = array_slice($services, 0, 5);
?>

<span>
  <i class="fas fa-wrench" style=" margin-right: 6px;"></i>
  <?php
  foreach ($services as $index => $service) {
      echo htmlspecialchars(trim($service));
      if ($index !== count($services) - 1) {
          echo ", ";
      }
  }
  ?>, etc.
</span>