<?php if (isset($_SESSION['update-profile-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['update-profile-error']; ?>");
    });
</script>
<?php unset($_SESSION['update-profile-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['update-profile-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['update-profile-success']); ?>");
    });
</script>
<?php unset($_SESSION['update-profile-success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['auto-message-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "5000"
        };
        toastr.error("<?php echo addslashes($_SESSION['auto-message-error']); ?>");
    });
</script>
<?php unset($_SESSION['auto-message-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['auto-message-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "5000"
        };
        toastr.success("<?php echo addslashes($_SESSION['auto-message-success']); ?>");
    });
</script>
<?php unset($_SESSION['auto-message-success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['change-pass-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['change-pass-error']; ?>");
    });
</script>
<?php unset($_SESSION['change-pass-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['change-pass-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['change-pass-success']); ?>");
    });
</script>
<?php unset($_SESSION['change-pass-success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['application-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['application-error']; ?>");
    });
</script>
<?php unset($_SESSION['application-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['application-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['application-success']); ?>");
    });
</script>
<?php unset($_SESSION['application-success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['set-password-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['set-password-error']; ?>");
    });
</script>
<?php unset($_SESSION['set-password-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['set-password-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['set-password-success']); ?>");
    });
</script>
<?php unset($_SESSION['set-password-success']); ?>
<?php endif; ?>


<?php if (isset($_SESSION['update-profile-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo addslashes($_SESSION['update-profile-error']); ?>");
    });
</script>
<?php unset($_SESSION['update-profile-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['update-profile-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['update-profile-success']); ?>");
    });
</script>
<?php unset($_SESSION['update-profile-success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['edit_shop_error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo addslashes($_SESSION['edit_shop_error']); ?>");
    });
</script>
<?php unset($_SESSION['edit_shop_error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['edit_shop_success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['edit_shop_success']); ?>");
    });
</script>
<?php unset($_SESSION['edit_shop_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['delete-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo addslashes($_SESSION['delete-error']); ?>");
    });
</script>
<?php unset($_SESSION['delete-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['delete-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['delete-success']); ?>");
    });
</script>
<?php unset($_SESSION['delete-success']); ?>
<?php endif; ?>

    <?php if (isset($_SESSION['report_error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000",
            "iconClasses": {
                "error": "",
                "info": "",
                "success": "",
                "warning": ""
            }
        };
        toastr.error("<?php echo addslashes($_SESSION['report_error']); ?>");
    });
</script>
<?php unset($_SESSION['report_error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['report_success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000",
            "iconClasses": {
                "error": "",
                "info": "",
                "success": "",
                "warning": ""
            }
        };
        toastr.success("<?php echo addslashes($_SESSION['report_success']); ?>");
    });
</script>
<?php unset($_SESSION['report_success']); ?>
<?php endif; ?>

<style>
.toast-success {
    background-color: #388e3c !important;
    color: white !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    padding: 12px 16px;
    position: fixed !important;
    top: 20px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    z-index: 9999;
    opacity: 1 !important;
    text-align: center;
    min-width: 250px;
    max-width: 90%;
    margin: 0 auto;
}

.toast-error {
    background-color: #d32f2f !important;
    color: white !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    padding: 12px 16px;
    position: fixed !important;
    top: 20px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    z-index: 9999;
    opacity: 1 !important;
    text-align: center;
    min-width: 250px;
    max-width: 90%;
    margin: 0 auto;
}

.toast-success .toast-close-button, 
.toast-error .toast-close-button {
    color: white !important;
    text-shadow: none;
    opacity: 1;
    font-weight: bold;
    font-size: 18px;
    position: absolute;
    right: 8px;
    top: 8px;
}

.toast-success:before,
.toast-error:before,
.toast-info:before,
.toast-warning:before {
    display: none !important;
}

.toast-success .toast-message,
.toast-error .toast-message,
.toast-info .toast-message,
.toast-warning .toast-message {
    padding-left: 0 !important;
    margin-left: 0 !important;
}

#toast-container > div {
    padding-left: 15px !important;
}

#toast-container .toast-success,
#toast-container .toast-error,
#toast-container .toast-info,
#toast-container .toast-warning {
    background-image: none !important;
}
</style>