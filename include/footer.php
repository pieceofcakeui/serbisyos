<style>
footer {
    background: linear-gradient(135deg, #f5f7fa 70%, #e4e8f0 100%);
    border-top: 1px solid #ccc;
    box-shadow: 0 -1px 1px rgba(0, 0, 0, 0.1);
}


footer p {
    text-align: left;
}

footer .text-muted {
    color: #a8b9cc !important;
}

footer a.text-muted {
    text-decoration: none;
    transition: all 0.3s ease;
}

footer a.text-muted:hover {
    color: #ffd700 !important;
    padding-left: 5px;
}

footer ul li {
    margin-bottom: 10px;
}

footer ul li a {
    transition: color 0.3s ease, padding-left 0.3s ease;
}

footer ul li a:hover {
    color: #ffd700 !important;
    padding-left: 5px;
}

footer hr {
    border-top: 1px solid #ccc;
}

footer .social-icons a {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    margin-right: -12px;
    font-size: 25px;
    color: #343a40;
    transition: all 0.3s ease;
}

footer .social-icons a:hover {
    color: #ffc107;
    border-color: #ffc107;
    transform: scale(1.1);
}

.back-to-top-btn {
    background: none;
    color: #343a40;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
}

.back-to-top-btn i {
    font-size: 14px;
}

.back-to-top-btn:hover {
    color: #ffc107;
    border-color: #ffc107;
    background-color: rgba(255, 193, 7, 0.1);
}
</style>

<footer class="pt-5">
    <div class="container">
        <div class="row">

            <div class="col-md-4 mb-4">
                <h5 style="font-weight: 600; color: black;">About Serbisyos</h5>
                <p>
                    Serbisyos connects vehicle owners with trusted local auto repair services through a user-friendly platform featuring detailed directories, reviews, and service info—making it easy to find reliable help when it's needed most.
                </p>
            </div>

            <div class="col-md-4 mb-4">
                <h5 style="font-weight: 600; color: black;">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo BASE_URL; ?>/home" class="text-dark text-decoration-none">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/service" class="text-dark text-decoration-none">Services</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/contact" class="text-dark text-decoration-none">Contact Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/about" class="text-dark text-decoration-none">About Serbisyos</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/become-a-partner" class="text-dark text-decoration-none">Become a Partner</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5 style="font-weight: 600; color: black;">Follow Us</h5>
                <div class="social-icons mt-3">
                    <a href="https://www.facebook.com/profile.php?id=61582804589086"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@serbisyos?_t=ZS-90qgugoEX0T&_r=1"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <button id="backToTopBtn" class="back-to-top-btn">
                <i class="fas fa-arrow-up"></i> Back to Top
            </button>
        </div>

        <hr class="bg-light">

        <div class="text-center py-3">
            <ul class="list-inline mb-0">
                <li class="list-inline-item"><a href="<?php echo BASE_URL; ?>/terms-and-conditions"
                        class="text-dark text-decoration-none">Terms and Conditions</a></li>
                <li class="list-inline-item">|</li>
                <li class="list-inline-item"><a href="<?php echo BASE_URL; ?>/privacy-policy"
                        class="text-dark text-decoration-none">Privacy Policy</a></li>
            </ul>
            <p class="mb-0 text-center">&copy; 2025 Serbisyos. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script>
document.getElementById("backToTopBtn").addEventListener("click", function() {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
</script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>