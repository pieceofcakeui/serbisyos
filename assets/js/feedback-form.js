$(document).ready(function () {
    $('#contactForm').submit(function (e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        const form = this;

        submitBtn.prop('disabled', true);
        submitBtn.find('.submit-text').addClass('d-none');
        submitBtn.find('.spinner-border').removeClass('d-none');

        $('#modalTitle').text('Processing Your Feedback');
        $('#modalMessage').text('We\'re sending your message...');
        $('#modalIcon').html('<i class="fas fa-circle-notch fa-spin fa-3x text-primary"></i>');
        $('#modalOkBtn').hide();
        $('#statusModal').modal('show');

        const formData = new FormData(form);

        $.ajax({
            url: '../account/backend/submit_feedback.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    $('#modalTitle').text('Thank You!');
                    $('#modalMessage').text('Your feedback has been successfully submitted to Serbisyos.');
                    $('#modalIcon').html('<i class="fas fa-check-circle fa-3x text-success"></i>');
                    form.reset();
                } else {
                    $('#modalTitle').text('Error!');
                    $('#modalMessage').text(response.message || 'An error occurred while submitting your feedback.');
                    $('#modalIcon').html('<i class="fas fa-times-circle fa-3x text-danger"></i>');
                }
            },
            error: function (xhr, status, error) {
                $('#modalTitle').text('Error!');
                $('#modalMessage').text('An unexpected error occurred. Please try again later.');
                $('#modalIcon').html('<i class="fas fa-times-circle fa-3x text-danger"></i>');
            },
            complete: function () {
                $('#modalOkBtn').show();
                submitBtn.prop('disabled', false);
                submitBtn.find('.submit-text').removeClass('d-none');
                submitBtn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    $('#statusModal').on('hidden.bs.modal', function () {
        $('#modalIcon').html('<i class="fas fa-circle-notch fa-spin fa-3x text-primary"></i>');
        $('#modalTitle').text('Processing Your Feedback');
        $('#modalMessage').text('We\'re sending your message...');
        $('#modalOkBtn').hide();
    });
});
