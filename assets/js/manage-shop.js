let currentStep = 1;
const totalSteps = 2;

document.addEventListener('DOMContentLoaded', function() {
  updateHiddenFields();
  
  document.querySelectorAll('.tag-option').forEach(option => {
    option.addEventListener('click', function() {
      const container = this.closest('.tags-input-container');
      container.querySelectorAll('.tag-option').forEach(opt => opt.classList.remove('selected'));
      this.classList.add('selected');
      updateHiddenFields();
    });
  });
  
  document.getElementById('nextStepBtn').addEventListener('click', function() {
    if (currentStep < totalSteps) {
      if (validateStep(currentStep)) {
        goToStep(currentStep + 1);
      }
    } else {
      if (validateStep(currentStep)) {
        submitBookingForm();
      }
    }
  });
  
  document.getElementById('prevStepBtn').addEventListener('click', function() {
    if (currentStep > 1) {
      goToStep(currentStep - 1);
    }
  });

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});

function goToStep(step) {
  document.getElementById(`step${currentStep}`).classList.add('d-none');
  document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
  
  document.getElementById(`step${step}`).classList.remove('d-none');
  document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
  
  currentStep = step;
  
  document.getElementById('prevStepBtn').classList.toggle('d-none', currentStep === 1);
  document.getElementById('nextStepBtn').innerHTML = currentStep === totalSteps ? 
    'Submit<i class="fas fa-check ms-2"></i>' : 
    'Next<i class="fas fa-arrow-right ms-2"></i>';
}

function validateStep(step) {
  if (step === 2) {
    const date = document.getElementById('preferredDate').value;
    if (!date) {
      alert('Please select a preferred date');
      return false;
    }
  }
  return true;
}

function updateHiddenFields() {
  document.querySelectorAll('.tags-input-container').forEach(container => {
    const hiddenInput = container.querySelector('input[type="hidden"]');
    const selectedOption = container.querySelector('.tag-option.selected');
    if (selectedOption) {
      hiddenInput.value = selectedOption.dataset.value;
    }
  });
}

function addOption(type) {
  const input = document.getElementById(`${type}Input`);
  const value = input.value.trim();
  
  if (value) {
    const optionsContainer = document.getElementById(`${type}Options`);
    const newOption = document.createElement('span');
    newOption.className = 'tag-option';
    newOption.dataset.value = value;
    newOption.innerHTML = `${value} <span class="remove-option" onclick="removeOption(this)"><i class="fas fa-times-circle"></i></span>`;
    
    newOption.addEventListener('click', function() {
      optionsContainer.querySelectorAll('.tag-option').forEach(opt => opt.classList.remove('selected'));
      this.classList.add('selected');
      updateHiddenFields();
    });
    
    optionsContainer.appendChild(newOption);
    input.value = '';
    
    optionsContainer.querySelectorAll('.tag-option').forEach(opt => opt.classList.remove('selected'));
    newOption.classList.add('selected');
    updateHiddenFields();
  }
}

function removeOption(element) {
  const option = element.closest('.tag-option');
  const container = option.closest('.tags-input-container');
  const hiddenInput = container.querySelector('input[type="hidden"]');
  
  if (option.classList.contains('selected')) {
    const remainingOptions = container.querySelectorAll('.tag-option:not(.selected)');
    if (remainingOptions.length > 0) {
      remainingOptions[0].classList.add('selected');
    } else {
      hiddenInput.value = '';
    }
  }
  
  option.remove();
  updateHiddenFields();
  
  event.stopPropagation();
}

function submitBookingForm() {
  const formData = new FormData(document.getElementById('bookingForm'));
    
  fetch('../account/backend/bookings.php', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      window.location.href = '../account/shop_owner_profile.php?success=1';
    } else {
      alert(data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while submitting the booking');
  });
    
  bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
}