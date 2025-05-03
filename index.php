<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Film Uygulaması</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            color: whitesmoke;
            background-color: black;
        }

        .carousel-item {
            position: relative;
        }

        /* Poster üzerine gelindiğinde karartma efekti */
        .carousel-item:hover img {
            opacity: 0.5;
        }

        .carousel-item img {
            width: 100%;
            height: 400px;
            object-fit: contain;
            transition: opacity 0.5s ease;
        }

        /* Laptop ve daha büyük ekranlar için */
        @media (min-width: 992px) {
            .carousel-item img {
                object-fit: fill;
            }
        }

        /* Film açıklaması */
        .carousel-caption {
            position: absolute;
            top: 35%;
            /* Yeri biraz daha yukarıya aldık */
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.25rem;
            visibility: hidden;
            /* Başlangıçta gizle */
            opacity: 0;
            transition: opacity 0.5s ease;
            max-height: 40%;
            /* Açıklama kısmının yüksekliğini sınırlıyoruz */
            overflow-y: auto;
            /* Fazla metin için kaydırma ekledik */
            padding: 10px;
            /*background-color: rgba(0, 0, 0, 0.6); /* Yarı şeffaf arka plan */
        }

        /* Scrollbar'ı gizleme */
        .carousel-caption::-webkit-scrollbar {
            display: none;
            /* Webkit tabanlı tarayıcılarda scrollbar'ı gizler */
        }

        /* Hover durumunda açıklama görünür hale gelir */
        .carousel-item:hover .carousel-caption {
            visibility: visible;
            opacity: 1;
        }


        /*
        #movieGrid img {
            height: 35vh;
            object-fit: cover;
        }
*/
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
            /* Başlığı en alta yerleştirir */
            padding: 1rem;
            /* Kart içi boşluk */
            flex-grow: 1;
            /* Kartın geri kalan kısmını doldurur */
        }

        .card-body h5 {
            text-align: center;
            margin-bottom: 0;
            font-size: 1.25rem;
            /* İstenilen font büyüklüğü */
        }
    </style>
</head>

<body>
    <?php
    $navbarTitle = "Ana Sayfa";
    $navbarItems = ['dashboard', 'profile', 'edit_profile', 'logout'];
    include 'php/navbar.php';
    ?>


    <div id="popularCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner" id="carouselContent"></div>
        <button class="carousel-control-prev" type="button" data-bs-target="#popularCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#popularCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>



    <!-- Film Grid -->
    <div class="container my-4">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4" id="movieGrid"></div>
        <div class="d-flex justify-content-between my-4">
            <button id="prevBtn" class="btn btn-secondary">Previous</button>
            <button id="nextBtn" class="btn btn-primary">Next</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="index.js"></script> <!-- JavaScript dosyamız burada -->
</body>

</html>