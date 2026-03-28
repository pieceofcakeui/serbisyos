<?php if (isset($_SESSION['error_message'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['error_message']; ?>");
    });
</script>
<?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['success_message'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['success_message']); ?>");
    });
</script>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>


<?php if (isset($_SESSION['delete-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['delete-error']; ?>");
    });
</script>
<?php unset($_SESSION['delete-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['delete-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['delete-success']); ?>");
    });
</script>
<?php unset($_SESSION['delete-success']); ?>
<?php endif; ?>

