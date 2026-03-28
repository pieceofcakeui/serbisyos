<?php if (isset($_SESSION['2fa_error'])): ?>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "timeOut": "2000"
            };
            toastr.error("<?php echo $_SESSION['2fa_error']; ?>");
        });
    </script>
    <?php 
    unset($_SESSION['2fa_error']); 
    endif; 
    ?>

<?php if (isset($_SESSION['login-error'])): ?>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "timeOut": "2000"
            };
            toastr.error("<?php echo $_SESSION['login-error']; ?>");
        });
    </script>
    <?php 
    unset($_SESSION['login-error']); 
    endif; 
    ?>

<?php if (isset($_SESSION['signup-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['signup-error']; ?>");
    });
</script>
<?php unset($_SESSION['signup-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['verify-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['verify-error']; ?>");
    });
</script>
<?php unset($_SESSION['verify-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['resend-pass-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['resend-pass-error']; ?>");
    });
</script>
<?php unset($_SESSION['resend-pass-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['resend-pass-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['resend-pass-success']); ?>");
    });
</script>
<?php unset($_SESSION['resend-pass-success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['resend-otp-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['resend-otp-error']; ?>");
    });
</script>
<?php unset($_SESSION['resend-otp-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['resend-otp-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['resend-otp-success']); ?>");
    });
</script>
<?php unset($_SESSION['resend-otp-success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['google-login-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['google-login-error']; ?>");
    });
</script>
<?php unset($_SESSION['google-login-error']); ?>
<?php endif; ?>


<?php if (isset($_SESSION['google-signup-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['google-signup-error']; ?>");
    });
</script>
<?php unset($_SESSION['google-signup-error']); ?>
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
        toastr.error("<?php echo $_SESSION['delete-error']; ?>");
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

<?php if (isset($_SESSION['forgot-password-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['forgot-password-error']; ?>");
    });
</script>
<?php unset($_SESSION['forgot-password-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['otp-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['otp-error']; ?>");
    });
</script>
<?php unset($_SESSION['otp-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['reset-password-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['reset-password-error']; ?>");
    });
</script>
<?php unset($_SESSION['reset-password-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['reset-password-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['reset-password-success']); ?>");
    });
</script>
<?php unset($_SESSION['reset-password-success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['reset-pass-error'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.error("<?php echo $_SESSION['reset-pass-error']; ?>");
    });
</script>
<?php unset($_SESSION['reset-pass-error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['reset-pass-success'])): ?>
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": "2000"
        };
        toastr.success("<?php echo addslashes($_SESSION['reset-pass-success']); ?>");
    });
</script>
<?php unset($_SESSION['reset-pass-success']); ?>
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
    transition: opacity 0.3s ease;
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
    transition: opacity 0.3s ease;
}

.toast-info {
    background-color: #17a2b8 !important;
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
    transition: opacity 0.3s ease;
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
    cursor: pointer;
    background: transparent;
    border: none;
    padding: 0 5px;
}
</style>