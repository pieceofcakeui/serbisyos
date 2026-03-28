<?php
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define('BASE_URL', '/serbisyos');
} else {
    define('BASE_URL', '');
}
?>