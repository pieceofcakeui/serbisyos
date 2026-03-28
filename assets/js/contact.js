document.addEventListener('DOMContentLoaded', function () {
   const contactForm = document.getElementById('contactForm');
   const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
   const loadingIcon = document.getElementById('loadingIcon');
   const successIcon = document.getElementById('successIcon');
   const errorIcon = document.getElementById('errorIcon');

   if (contactForm) {
      contactForm.addEventListener('submit', function (e) {
         e.preventDefault();

         const submitBtn = contactForm.querySelector('button[type="submit"]');
         const submitText = submitBtn.querySelector('.submit-text');
         const spinner = submitBtn.querySelector('.spinner-border');

         loadingIcon.classList.remove('d-none');
         successIcon.classList.add('d-none');
         errorIcon.classList.add('d-none');
         document.getElementById('modalOkBtn').style.display = 'none';

         const nameField = document.querySelector('input[name="name"]');
         const emailField = document.querySelector('input[name="email"]');
         const subjectField = document.querySelector('input[name="subject"]');
         const messageField = document.querySelector('textarea[name="message"]');

         let errorMessage = '';

         if (nameField.value.trim() === '') {
            errorMessage = 'Please enter your full name.';
         } else if (emailField.value.trim() === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value.trim())) {
            errorMessage = 'Please enter a valid email address.';
         } else if (subjectField.value.trim() === '') {
            errorMessage = 'The subject field is required.';
         } else if (messageField.value.trim() === '') {
            errorMessage = 'Please enter your message.';
         }

         if (errorMessage) {
            loadingIcon.classList.add('d-none');
            errorIcon.classList.remove('d-none');
            document.getElementById('modalTitle').textContent = 'Validation Error';
            document.getElementById('modalMessage').textContent = errorMessage;
            document.getElementById('modalOkBtn').style.display = 'block';
            statusModal.show();
            return;
         }

         submitText.textContent = 'Sending...';
         spinner.classList.remove('d-none');
         submitBtn.disabled = true;

         document.getElementById('modalTitle').textContent = 'Processing Your Message';
         document.getElementById('modalMessage').textContent = "We're sending your message...";
         statusModal.show();

         const formData = new FormData(contactForm);

         fetch('../account/backend/submit-message.php', {
            method: 'POST',
            body: formData
         })
            .then(response => {
               if (!response.ok) {
                  throw new Error('Network response was not ok');
               }
               return response.json();
            })
            .then(data => {
               loadingIcon.classList.add('d-none');

               if (data.success) {
                  successIcon.classList.remove('d-none');
                  document.getElementById('modalTitle').textContent = 'Message Sent!';
                  document.getElementById('modalMessage').textContent = data.message || 'Thank you for your message. We will get back to you soon.';
                  document.getElementById('modalOkBtn').style.display = 'block';

                  contactForm.reset();
               } else {
                  errorIcon.classList.remove('d-none');
                  document.getElementById('modalTitle').textContent = 'Error Sending Message';
                  document.getElementById('modalMessage').textContent = data.message || 'An error occurred while sending your message. Please try again later.';
                  document.getElementById('modalOkBtn').style.display = 'block';
               }
            })
            .catch(error => {
               console.error('Error:', error);
               loadingIcon.classList.add('d-none');
               errorIcon.classList.remove('d-none');
               document.getElementById('modalTitle').textContent = 'Error';
               document.getElementById('modalMessage').textContent = 'An unexpected error occurred. Please try again later.';
               document.getElementById('modalOkBtn').style.display = 'block';
            })
            .finally(() => {
               submitText.textContent = 'Submit Message';
               spinner.classList.add('d-none');
               submitBtn.disabled = false;
            });
      });
   }

   document.getElementById('modalOkBtn').addEventListener('click', function () {
      statusModal.hide();
   });
});