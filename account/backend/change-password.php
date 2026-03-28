<?php
if (isset($_SESSION['success'])) {
    echo "<script>
                alert('" . $_SESSION['success'] . "');
                window.location.href = '../change-password.php';
            </script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>
                alert('" . $_SESSION['error'] . "');
                window.location.href = '../change-password.php';
            </script>";
    unset($_SESSION['error']);
}
?>