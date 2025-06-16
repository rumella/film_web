<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Uygulaması</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            color: whitesmoke;
            background-color: black;
        }

        #searchForm input,
        #searchForm button {
            height: 48px;
            font-size: 1rem;
            padding: 0 16px;
            border-radius: 12px;
            border: none;
        }

        #searchForm input {
            flex: 1;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            background-color: #1e1e1e;
            color: #fff;
        }

        #movie_name {
            background-color: #000;
            /* siyah arka plan */
            color: #fff;
            /* beyaz yazı */
        }

        #movie_name::placeholder {
            color: #aaa;
            /* soluk beyaz */
            font-weight: 300;
        }

        #searchForm input:focus {
            box-shadow: none;
            outline: none;
        }

        #searchForm input::placeholder {
            color: #aaa;
            font-weight: 300;
        }

        #searchSuggestions {
            background-color: white;
            color: black;
            border-radius: 0 0 6px 6px;
            position: absolute;
            z-index: 999;
            width: 100%;
            max-height: 500px;
            overflow-y: auto;
            display: none;
        }

        #searchSuggestions li {
            display: flex;
            align-items: center;
            padding: 8px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
        }

        #searchSuggestions li:hover {
            background-color: #f2f2f2;
        }

        #searchSuggestions img {
            width: 50px;
            height: 75px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }

        .suggestion-info {
            display: flex;
            flex-direction: column;
        }

        .suggestion-info strong {
            font-size: 1rem;
            font-weight: 600;
        }

        .suggestion-info small {
            font-size: 0.85rem;
            color: #666;
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
            background-color: #2b2b2b;
            color: whitesmoke;
            transition: box-shadow 0.3s ease;
            /* Geçiş efekti ekleyerek daha yumuşak bir animasyon sağla */
        }

        #movieGrid .card:hover {
            box-shadow: 0 0 50px rgba(153, 23, 23, 0.7);
            /* Gölgeyi tüm yönlere eşit şekilde dağıt */
            transform: scale(1.05);
            /* Hover sırasında kartın biraz büyümesini sağlar */
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
            margin-top: 10px;
            /* Dropdown ile mesafe */
        }

        .search-result-card {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
            background: linear-gradient(to right, #1a1a1a, #111);
            border: 1px solid #333;
            padding: 12px 16px;
            margin-bottom: 12px;
            color: #f5f5f5;
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .search-result-card:hover {
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }

        .search-result-card img {
            width: 90px;
            height: 130px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #555;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
        }

        .search-result-card .card-body {
            padding: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .search-result-card h6 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 6px;
            color: #fff;
        }

        .search-result-card p {
            font-size: 0.85rem;
            margin: 2px 0;
            color: #bbb;
        }

        .footer a:hover {
            color: #0dcaf0 !important;
        }

        /* Yukarı Çık Butonu */
        #scrollTopBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            font-size: 1.2rem;
            padding: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        /* Tıklayınca döndür */
        #scrollTopBtn.clicked {
            transform: rotate(360deg);
        }
    </style>
</head>

<body>
    <?php
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

    <div class="container my-4 position-relative">
        <div class="mb-3 text-start text-white">
            <h1 class="display-3 fw-bold">Hoş Geldiniz!</h1>
            <p class="fs-5 fw-light">Keşfedilecek milyonlarca film, TV şovu ve kişi. Şimdi keşfedin.</p>
        </div>
        <form id="searchForm" class="d-flex w-110 shadow-sm border rounded-3 overflow-hidden">

            <input type="text" id="movie_name" class="form-control border-0 px-3 py-1 small" placeholder="Film adı yazın...">
        </form>
        <ul id="searchSuggestions" class="list-group mt-1"></ul>
        <div id="movie_results" class="row mt-4"></div>
    </div>
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

    <!-- Footer -->
    <footer class="footer mt-5 py-4 bg-dark text-light border-top">
        <div class="container text-center">
            <div class="mb-2">
                <a href="https://twitter.com/kullaniciadi" target="_blank" class="text-light me-3" title="Twitter">
                    <i class="bi bi-twitter fs-5"></i>
                </a>
                <a href="https://instagram.com/kullaniciadi" target="_blank" class="text-light me-3" title="Instagram">
                    <i class="bi bi-instagram fs-5"></i>
                </a>
                <a href="https://github.com/kullaniciadi" target="_blank" class="text-light me-3" title="GitHub">
                    <i class="bi bi-github fs-5"></i>
                </a>
                <a href="https://linkedin.com/in/kullaniciadi" target="_blank" class="text-light" title="LinkedIn">
                    <i class="bi bi-linkedin fs-5"></i>
                </a>
            </div>
            <div>
                <small>&copy; <?php echo date('Y'); ?> Film Uygulaması • Tüm Hakları Saklıdır</small>
            </div>
        </div>
    </footer>

    <!-- Yukarı Çık Butonu -->
    <button id="scrollTopBtn" type="button" title="Yukarı çık"
        class="btn btn-secondary d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="index.js"></script>
</body>

</html>