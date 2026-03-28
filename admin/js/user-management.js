document.addEventListener("DOMContentLoaded", function() {
    let tabs = document.querySelectorAll(".nav-link");

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            tabs.forEach(t => t.classList.remove("active-tab"));
            this.classList.add("active-tab");
        });
    });
});

function showUsers(section) {
    document.getElementById('all-users').style.display = (section === 'all-users') ? 'block' : 'none';
    document.getElementById('ownershop').style.display = (section === 'ownershop') ? 'block' : 'none';
}