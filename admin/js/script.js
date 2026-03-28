document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarClose = document.getElementById('sidebarClose');

  sidebarToggle.addEventListener('click', function () {
    if (window.innerWidth <= 768) {
      sidebar.classList.toggle('active');
    }
  });

  if (sidebarClose) {
    sidebarClose.addEventListener('click', function () {
      sidebar.classList.remove('active');
    });
  }

  window.addEventListener('resize', function () {
    if (window.innerWidth > 768) {
      sidebar.classList.remove('active');
    }
  });
});


const togglePassword = document.getElementById("toggle-password");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", function () {
  const type =
    passwordInput.getAttribute("type") === "password" ? "text" : "password";
  passwordInput.setAttribute("type", type);
  this.innerHTML =
    type === "password"
      ? '<i class="fa-regular fa-eye"></i>'
      : '<i class="fa-regular fa-eye-slash"></i>';
});


document.getElementById("modal-body-content").innerHTML = content;

const modalFooter = document.querySelector(".modal-footer");
if (data.status === "Pending") {
  modalFooter.innerHTML = `
            <button type="button" class="btn btn-success" onclick="updateStatus(${data.id}, 'Approved')">
                <i class="fas fa-check"></i> Approve
            </button>
            <button type="button" class="btn btn-danger" onclick="updateStatus(${data.id}, 'Rejected')">
                <i class="fas fa-times"></i> Reject
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        `;
} else {
  modalFooter.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        `;
}

function updateStatus(id, status) {
  if (
    !confirm(
      `Are you sure you want to ${status.toLowerCase()} this application?`
    )
  ) {
    return;
  }

  fetch("update_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}&status=${status}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast("Status updated successfully!", "success");
        setTimeout(() => {
          const row = document.querySelector(`tr[data-id='${id}']`);
          if (row) {
            const statusCell = row.querySelector("td:nth-child(5)");
            statusCell.innerHTML = `<span class='badge bg-${data.status === "Approved" ? "success" : "danger"
              }'>${data.status}</span>`;

            const approveButton = row.querySelector(".btn-success");
            const rejectButton = row.querySelector(".btn-danger");
            if (approveButton) approveButton.style.display = "none";
            if (rejectButton) rejectButton.style.display = "none";
          }
        }, 1000);
      } else {
        showToast(data.message || "Failed to update status", "danger");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast("Error updating status", "danger");
    });
}

function showToast(message, type = "info") {
  let toastContainer = document.querySelector(".toast-container");
  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.className = "toast-container position-fixed bottom-0 end-0 p-3";
    document.body.appendChild(toastContainer);
  }

  const toastId = "toast-" + Date.now();

  const toastHtml = `
    <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;

  toastContainer.innerHTML += toastHtml;

  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
  toast.show();

  toastElement.addEventListener('hidden.bs.toast', function () {
    toastElement.remove();
  });
}

function loadApplications() {
  fetch("../backend/get_applications.php")
    .then(response => response.json())
    .then(data => {
      const tableBody = document.getElementById("applicationsTableBody");
      if (tableBody) {
        tableBody.innerHTML = "";

        if (data.length === 0) {
          tableBody.innerHTML = "<tr><td colspan='6' class='text-center'>No applications found</td></tr>";
          return;
        }

        data.forEach(app => {

          let statusClass = 'warning';
          if (app.status === 'Approved') statusClass = 'success';
          if (app.status === 'Rejected') statusClass = 'danger';

          const row = document.createElement('tr');
          row.dataset.id = app.id;
          row.innerHTML = `
            <td>${app.id}</td>
            <td>${app.shop_name}</td>
            <td>${app.owner_name}</td>
            <td>${app.email}</td>
            <td><span class='badge bg-${statusClass}'>${app.status || 'Pending'}</span></td>
            <td>
              <button class='btn btn-primary btn-sm' 
                      data-bs-toggle='modal'
                      data-bs-target='#viewModal'
                      onclick='viewApplication(${JSON.stringify(app).replace(/'/g, "\\'")})'> 
                <i class='fas fa-eye'></i>
              </button>
              ${app.status === 'Pending' ? `
                <button class='btn btn-success btn-sm ms-1' onclick='updateStatus(${app.id}, "Approved")'><i class='fas fa-check'></i></button>
                <button class='btn btn-danger btn-sm ms-1' onclick='updateStatus(${app.id}, "Rejected")'><i class='fas fa-times'></i></button>
              ` : ''}
            </td>
          `;
          tableBody.appendChild(row);
        });
      }
    })
    .catch(error => {
      console.error("Error loading applications:", error);
      showToast("Failed to load applications", "danger");
    });
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("applicationsTableBody")) {
    loadApplications();
  }

  const searchInput = document.getElementById("searchApplications");
  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase();
      const tableRows = document.querySelectorAll("#applicationsTableBody tr");

      tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
      });
    });
  }
});

function showToast(message, type = "info") {
  let toastContainer = document.getElementById("toastContainer");
  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.id = "toastContainer";
    toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-white bg-${type} border-0`;
  toast.setAttribute("role", "alert");
  toast.setAttribute("aria-live", "assertive");
  toast.setAttribute("aria-atomic", "true");

  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

  toastContainer.appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  toast.addEventListener("hidden.bs.toast", () => {
    toast.remove();
  });
}

function handleResize() {
  const width = window.innerWidth;
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");

  if (width <= 768) {
    sidebar?.classList.remove("active");
    content?.classList.remove("content-active");
  } else {
    sidebar?.classList.add("active");
    content?.classList.add("content-active");
  }
}

window.addEventListener("load", function () {
  handleResize();
  window.addEventListener("resize", handleResize);
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});

document.getElementById("sidebarToggle").addEventListener("click", function () {
  document.getElementById("sidebar").classList.toggle("active");
  document.querySelector(".content").classList.toggle("content-active");
});

var tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});
