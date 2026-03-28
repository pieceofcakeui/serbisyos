$(document).ready(function() {
    $('#verify-form').on('submit', function(e) {
        const otp = $('#otp').val().trim();
        if (!/^\d{6}$/.test(otp)) {
            toastr.error('Please enter a valid 6-digit OTP');
            e.preventDefault();
        }
    });

    const countdownTimerEl = $('#countdown-timer');
    const resendLinkEl = $('#resend-link');
    const cooldownTime = 120;
    let countdownInterval;

    function startCountdown(duration) {
        let timer = duration;
        
        resendLinkEl.css({ 'pointer-events': 'none', 'color': 'gray' });
        countdownTimerEl.show();

        countdownInterval = setInterval(function() {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;

            countdownTimerEl.text(`(Resend in ${minutes}m ${seconds}s)`);

            if (--timer < 0) {
                enableResend();
            }
        }, 1000);
    }

    function enableResend() {
        clearInterval(countdownInterval);
        resendLinkEl.css({ 'pointer-events': 'auto', 'color': '' });
        countdownTimerEl.hide();
    }

    const currentTime = Math.floor(Date.now() / 1000);
    const timeSinceLastSent = currentTime - otpLastSent;
    
    if (otpLastSent > 0 && timeSinceLastSent < cooldownTime) {
        const remainingTime = cooldownTime - timeSinceLastSent;
        startCountdown(remainingTime);
    } else {
        countdownTimerEl.hide();
    }

    resendLinkEl.on('click', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'functions/resend_otp.php',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                startCountdown(cooldownTime);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    enableResend(); 
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again later.');
                enableResend();
            }
        });
    });
});