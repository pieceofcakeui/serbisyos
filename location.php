<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locations</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
    margin: 0;
}

        .location-section {
            padding: 0;
            background-color: transparent;
        }
        .location-hero {
            background: transparent;
            padding: 30px 0;
            text-align: center;
            margin-bottom: -60px;
        }
        .location-hero h1 {
            font-weight: 800;
            font-size: 3rem;
            color: #1a1a1a;
        }
        .location-hero p {
            font-size: 1.2rem;
            color: #666;
            max-width: 700px;
            margin: 1rem auto 0;
        }
        .locations-content {
            padding: 80px 20px;
        }
        .district-title {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2.5rem;
            text-align: center;
            position: relative;
        }
        .location-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        .location-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: block;
            overflow: hidden;
            position: relative;
            height: 150px;
        }
        .location-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        .location-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .location-card:hover img {
            transform: scale(1.05);
        }
        .location-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.1));
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
        }
        .location-card-body span {
            font-weight: 600;
            font-size: 1.3rem;
        }
        .floating-contact-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #ffc107;
            color: #1a1a1a;
            padding: 1rem;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .floating-contact-btn i {
            margin-right: 0.5rem;
        }
        .floating-contact-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 193, 7, 0.4);
            color: #1a1a1a;
        }
    </style>
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'offline-handler.php'; ?>

   <div class="location-section">
        <div class="location-hero">
            <div class="container">
                <h1>Our Partner Service Area</h1>
                <p>We are proud to serve the entire province of Iloilo. Find trusted auto repair shops in your city or municipality below. We are expanding to more locations soon!</p>
            </div>
        </div>
        <div class="locations-content">
            <div class="container">
                <div class="mb-5">
                    <h2 class="district-title">Iloilo City</h2>
                    <div class="location-grid">
                        <a href="services.php?location=Arevalo" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Arevalo" alt="Arevalo"><div class="location-card-overlay"><div class="location-card-body"><span>Arevalo</span></div></div></a>
                        <a href="services.php?location=City Proper" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=City+Proper" alt="City Proper"><div class="location-card-overlay"><div class="location-card-body"><span>City Proper</span></div></div></a>
                        <a href="services.php?location=Jaro" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Jaro" alt="Jaro"><div class="location-card-overlay"><div class="location-card-body"><span>Jaro</span></div></div></a>
                        <a href="services.php?location=La Paz" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=La+Paz" alt="La Paz"><div class="location-card-overlay"><div class="location-card-body"><span>La Paz</span></div></div></a>
                        <a href="services.php?location=Lapuz" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Lapuz" alt="Lapuz"><div class="location-card-overlay"><div class="location-card-body"><span>Lapuz</span></div></div></a>
                        <a href="services.php?location=Mandurriao" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Mandurriao" alt="Mandurriao"><div class="location-card-overlay"><div class="location-card-body"><span>Mandurriao</span></div></div></a>
                        <a href="services.php?location=Molo" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Molo" alt="Molo"><div class="location-card-overlay"><div class="location-card-body"><span>Molo</span></div></div></a>
                    </div>
                </div>
                
                <div class="mb-5">
                    <h2 class="district-title">1st District</h2>
                    <div class="location-grid">
                        <a href="services.php?location=Guimbal" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Guimbal" alt="Guimbal"><div class="location-card-overlay"><div class="location-card-body"><span>Guimbal</span></div></div></a>
                        <a href="services.php?location=Igbaras" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Igbaras" alt="Igbaras"><div class="location-card-overlay"><div class="location-card-body"><span>Igbaras</span></div></div></a>
                        <a href="services.php?location=Miagao" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Miagao" alt="Miagao"><div class="location-card-overlay"><div class="location-card-body"><span>Miagao</span></div></div></a>
                        <a href="services.php?location=Oton" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Oton" alt="Oton"><div class="location-card-overlay"><div class="location-card-body"><span>Oton</span></div></div></a>
                        <a href="services.php?location=San Joaquin" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=San+Joaquin" alt="San Joaquin"><div class="location-card-overlay"><div class="location-card-body"><span>San Joaquin</span></div></div></a>
                        <a href="services.php?location=Tigbauan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Tigbauan" alt="Tigbauan"><div class="location-card-overlay"><div class="location-card-body"><span>Tigbauan</span></div></div></a>
                        <a href="services.php?location=Tubungan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Tubungan" alt="Tubungan"><div class="location-card-overlay"><div class="location-card-body"><span>Tubungan</span></div></div></a>
                    </div>
                </div>

                <div class="mb-5">
                    <h2 class="district-title">2nd District</h2>
                    <div class="location-grid">
                        <a href="services.php?location=Alimodian" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Alimodian" alt="Alimodian"><div class="location-card-overlay"><div class="location-card-body"><span>Alimodian</span></div></div></a>
                        <a href="services.php?location=Leganes" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Leganes" alt="Leganes"><div class="location-card-overlay"><div class="location-card-body"><span>Leganes</span></div></div></a>
                        <a href="services.php?location=Leon" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Leon" alt="Leon"><div class="location-card-overlay"><div class="location-card-body"><span>Leon</span></div></div></a>
                        <a href="services.php?location=New Lucena" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=New+Lucena" alt="New Lucena"><div class="location-card-overlay"><div class="location-card-body"><span>New Lucena</span></div></div></a>
                        <a href="services.php?location=Pavia" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Pavia" alt="Pavia"><div class="location-card-overlay"><div class="location-card-body"><span>Pavia</span></div></div></a>
                        <a href="services.php?location=San Miguel" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=San+Miguel" alt="San Miguel"><div class="location-card-overlay"><div class="location-card-body"><span>San Miguel</span></div></div></a>
                        <a href="services.php?location=Santa Barbara" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Santa+Barbara" alt="Santa Barbara"><div class="location-card-overlay"><div class="location-card-body"><span>Santa Barbara</span></div></div></a>
                        <a href="services.php?location=Zarraga" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Zarraga" alt="Zarraga"><div class="location-card-overlay"><div class="location-card-body"><span>Zarraga</span></div></div></a>
                    </div>
                </div>

                <div class="mb-5">
                    <h2 class="district-title">3rd District</h2>
                    <div class="location-grid">
                        <a href="services.php?location=Badiangan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Badiangan" alt="Badiangan"><div class="location-card-overlay"><div class="location-card-body"><span>Badiangan</span></div></div></a>
                        <a href="services.php?location=Bingawan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Bingawan" alt="Bingawan"><div class="location-card-overlay"><div class="location-card-body"><span>Bingawan</span></div></div></a>
                        <a href="services.php?location=Cabatuan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Cabatuan" alt="Cabatuan"><div class="location-card-overlay"><div class="location-card-body"><span>Cabatuan</span></div></div></a>
                        <a href="services.php?location=Calinog" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Calinog" alt="Calinog"><div class="location-card-overlay"><div class="location-card-body"><span>Calinog</span></div></div></a>
                        <a href="services.php?location=Janiuay" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Janiuay" alt="Janiuay"><div class="location-card-overlay"><div class="location-card-body"><span>Janiuay</span></div></div></a>
                        <a href="services.php?location=Lambunao" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Lambunao" alt="Lambunao"><div class="location-card-overlay"><div class="location-card-body"><span>Lambunao</span></div></div></a>
                        <a href="services.php?location=Maasin" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Maasin" alt="Maasin"><div class="location-card-overlay"><div class="location-card-body"><span>Maasin</span></div></div></a>
                        <a href="services.php?location=Mina" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Mina" alt="Mina"><div class="location-card-overlay"><div class="location-card-body"><span>Mina</span></div></div></a>
                        <a href="services.php?location=Pototan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Pototan" alt="Pototan"><div class="location-card-overlay"><div class="location-card-body"><span>Pototan</span></div></div></a>
                    </div>
                </div>

                <div class="mb-5">
                    <h2 class="district-title">4th District</h2>
                    <div class="location-grid">
                        <a href="services.php?location=Anilao" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Anilao" alt="Anilao"><div class="location-card-overlay"><div class="location-card-body"><span>Anilao</span></div></div></a>
                        <a href="services.php?location=Banate" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Banate" alt="Banate"><div class="location-card-overlay"><div class="location-card-body"><span>Banate</span></div></div></a>
                        <a href="services.php?location=Barotac Nuevo" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Barotac+Nuevo" alt="Barotac Nuevo"><div class="location-card-overlay"><div class="location-card-body"><span>Barotac Nuevo</span></div></div></a>
                        <a href="services.php?location=Dingle" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Dingle" alt="Dingle"><div class="location-card-overlay"><div class="location-card-body"><span>Dingle</span></div></div></a>
                        <a href="services.php?location=Dueñas" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Dueñas" alt="Dueñas"><div class="location-card-overlay"><div class="location-card-body"><span>Dueñas</span></div></div></a>
                        <a href="services.php?location=Dumangas" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Dumangas" alt="Dumangas"><div class="location-card-overlay"><div class="location-card-body"><span>Dumangas</span></div></div></a>
                        <a href="services.php?location=San Enrique" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=San+Enrique" alt="San Enrique"><div class="location-card-overlay"><div class="location-card-body"><span>San Enrique</span></div></div></a>
                        <a href="services.php?location=Passi City" class="location-card"><img src="https://placehold.co/400x300/333/ffc107?text=Passi+City" alt="Passi City"><div class="location-card-overlay"><div class="location-card-body"><span>Passi City</span></div></div></a>
                    </div>
                </div>

                <div class="mb-5">
                    <h2 class="district-title">5th District</h2>
                    <div class="location-grid">
                        <a href="services.php?location=Ajuy" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Ajuy" alt="Ajuy"><div class="location-card-overlay"><div class="location-card-body"><span>Ajuy</span></div></div></a>
                        <a href="services.php?location=Balasan" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Balasan" alt="Balasan"><div class="location-card-overlay"><div class="location-card-body"><span>Balasan</span></div></div></a>
                        <a href="services.php?location=Barotac Viejo" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Barotac+Viejo" alt="Barotac Viejo"><div class="location-card-overlay"><div class="location-card-body"><span>Barotac Viejo</span></div></div></a>
                        <a href="services.php?location=Batad" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Batad" alt="Batad"><div class="location-card-overlay"><div class="location-card-body"><span>Batad</span></div></div></a>
                        <a href="services.php?location=Carles" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Carles" alt="Carles"><div class="location-card-overlay"><div class="location-card-body"><span>Carles</span></div></div></a>
                        <a href="services.php?location=Concepcion" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Concepcion" alt="Concepcion"><div class="location-card-overlay"><div class="location-card-body"><span>Concepcion</span></div></div></a>
                        <a href="services.php?location=Estancia" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Estancia" alt="Estancia"><div class="location-card-overlay"><div class="location-card-body"><span>Estancia</span></div></div></a>
                        <a href="services.php?location=Lemery" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Lemery" alt="Lemery"><div class="location-card-overlay"><div class="location-card-body"><span>Lemery</span></div></div></a>
                        <a href="services.php?location=San Dionisio" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=San+Dionisio" alt="San Dionisio"><div class="location-card-overlay"><div class="location-card-body"><span>San Dionisio</span></div></div></a>
                        <a href="services.php?location=San Rafael" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=San+Rafael" alt="San Rafael"><div class="location-card-overlay"><div class="location-card-body"><span>San Rafael</span></div></div></a>
                        <a href="services.php?location=Sara" class="location-card"><img src="https://placehold.co/400x300/555/ffc107?text=Sara" alt="Sara"><div class="location-card-overlay"><div class="location-card-body"><span>Sara</span></div></div></a>
                    </div>
                </div>
            </div>
        </div>
   </div>

    <?php include 'include/emergency-floating.php'; ?>
    <?php include 'include/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<script src="assets/js/progress-bar.js"></script>
    <script src="js/script.js"></script>
    <script src="js/filter-modal.js"></script>

</body>

</html>
