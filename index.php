<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Uygulaması</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            color: whitesmoke;
            background-color: black;
        }

        .carousel-inner,
        .carousel-item,
        .carousel-item img {
            width: 100%;
            height: 400px;
            object-fit: fill;
            border-radius: 10px;
        }

        .carousel-item:hover img {
            opacity: 0.5;
        }

        .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.25rem;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.5s ease;
            max-height: 70%;
            overflow-y: auto;
            padding: 10px;
        }

        .carousel-caption::-webkit-scrollbar {
            display: none;
        }

        .carousel-item:hover .carousel-caption {
            visibility: visible;
            opacity: 1;
        }

        #movieGrid .card {
            height: 100%;
            background-color: black;
            color: whitesmoke;
            border: 2px solid gray;
        }

        .card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1rem;
            flex-grow: 1;
        }

        .card-body h5 {
            text-align: center;
            margin-bottom: 0;
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {

            .carousel-inner,
            .carousel-item,
            .carousel-item img {
                height: 250px;
            }

            .carousel-caption {
                font-size: 1rem;
                top: 50%;
                padding: 6px;
                width: 80%;
            }

            #movieGrid .card {
                font-size: 0.8rem;
            }

            #movieGrid .card img {
                height: auto;
                max-height: 250px;
                width: 100%;
                object-fit: contain;
            }
        }

        .form-select {
            background-color: black;
            color: white;
            border: 1px solid white;
        }

        .form-select option {
            background-color: black;
            color: white;
        }

        .switch-label {
            color: white;
            font-size: 0.9rem;
        }

        /* Flex düzeni ile her iki öğeyi hizala */
        .form-check.form-switch {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px; /* Dropdown ile mesafe */
        }
    </style>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $navbarTitle = "Ana Sayfa";
        $navbarItems = ['login', 'register'];
        include 'php/navbar.php';
    } else {
        $navbarTitle = "Ana Sayfa";
        $navbarItems = ['dashboard', 'add_comment', 'profile', 'edit_profile', 'logout'];
        include 'php/navbar.php';
    }
    ?>

    <!-- Carousel -->
    <div class="container my-4">
        <div id="popularCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" id="carouselContent"></div>
            <button class="carousel-control-prev" type="button" data-bs-target="#popularCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#popularCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    <!-- 🎛️ Filtre Barı -->
    <div class="container my-4">
        <div class="row g-3">

            <!-- Tür Filtre + Adult Toggle -->
            <div class="col-sm-4">
                <select class="form-select" id="genreFilter">
                    <option value="">Tür Seç</option>
                </select>
                <div class="form-check form-switch d-flex align-items-center mt-2">
                    <input class="form-check-input" type="checkbox" id="adultToggle">
                    <label class="form-check-label ms-2 switch-label" for="adultToggle">+18 içerikleri göster</label>
                </div>
            </div>

            <!-- Yıl Filtre -->
            <div class="col-sm-4">
                <select class="form-select" id="yearFilter">
                    <option value="">Yıl Seç</option>
                </select>
                <div class="d-flex justify-content-center align-items-center mt-2 gap-2">
                    <label for="yearRangeSwitch" class="mb-0">'e kadar</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="yearRangeSwitch" checked>
                    </div>
                    <label for="yearRangeSwitch" class="mb-0">sadece</label>
                </div>
            </div>

            <!-- IMDb Filtre -->
            <div class="col-sm-4">
                <select class="form-select" id="ratingFilter">
                    <option value="">IMDb Puanı</option>
                    <option value="9">9 ve üzeri</option>
                    <option value="8">8 ve üzeri</option>
                    <option value="7">7 ve üzeri</option>
                    <option value="6">6 ve üzeri</option>
                    <option value="5">5 ve üzeri</option>
                    <option value="4">4 ve üzeri</option>
                    <option value="3">3 ve üzeri</option>
                    <option value="2">2 ve üzeri</option>
                    <option value="1">1 ve üzeri</option>
                </select>
                <div class="d-flex justify-content-center align-items-center mt-2 gap-2">
                    <label for="ratingRangeSwitch" class="mb-0">'e kadar</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ratingRangeSwitch" checked>
                    </div>
                    <label for="ratingRangeSwitch" class="mb-0">sadece</label>
                </div>
            </div>

        </div>
    </div>

    <!-- Film Grid -->
    <div class="container my-4">
        <div class="row g-4" id="movieGrid"></div>
        <div class="d-flex justify-content-between my-4">
            <button id="prevBtn" class="btn btn-secondary">Previous</button>
            <button id="randomBtn" class="btn btn-secondary">Random</button>
            <button id="nextBtn" class="btn btn-primary">Next</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="index.js"></script>
</body>

</html>
