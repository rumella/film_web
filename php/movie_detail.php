<?php
if (!isset($_GET['id'])) {
    echo "Film ID belirtilmedi.";
    exit;
}
$movieId = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Detayı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: whitesmoke;
            padding: 20px;
            margin: 0;
            overflow-x: hidden;
        }

        .movie-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 20px;
            justify-content: center;
            flex-wrap: wrap;
            cursor: default;
            /* Uzun açıklamalarda kaymayı engeller */
        }

        .movie-poster {
            width: 100%;
            max-width: 320px;
            /* Daha küçük yaptım */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        .movie-details {
            flex: 1;
            max-width: 700px;
        }

        .movie-details h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .movie-info {
            text-align: left;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .movie-overview {
            text-align: left;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .text-end {
            text-align: right;
        }

        .btn-outline-light {
            border-color: white;
            color: white;
        }

        .btn-outline-light:hover {
            background-color: white;
            color: black;
        }

        @media (max-width: 768px) {
            .movie-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .movie-details {
                width: 100%;
                max-width: 100%;
            }

            .movie-details h1 {
                font-size: 1.5rem;
            }

            .movie-overview,
            .movie-info {
                text-align: left;
                font-size: 1rem;
            }

            .text-end {
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="movie-container" id="movieDetails">
            <!-- JS ile doldurulacak -->
        </div>
        <div class="text-end">
            <button onclick="window.history.back()" class="btn btn-outline-light">← Geri Dön</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const movieId = <?= json_encode($movieId) ?>;

        $.getJSON('get_api_key.php', function(data) {
            const apiKey = data.apiKey;
            const url = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=tr-TR`;

            $.getJSON(url, function(movie) {
                const posterPath = movie.poster_path ?
                    `https://image.tmdb.org/t/p/w500${movie.poster_path}` :
                    'https://via.placeholder.com/500x750?text=No+Image';

                const html = `
                    <img src="${posterPath}" class="movie-poster" alt="${movie.title}">
                    <div class="movie-details">
                        <h1>${movie.title} (${movie.release_date ? movie.release_date.substring(0, 4) : 'Yıl Yok'})</h1>
                        <div class="movie-info"><strong>IMDb:</strong> ${movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A'}</div>
                        <div class="movie-overview">${movie.overview || "Açıklama bulunamadı."}</div>
                    </div>
                `;

                $('#movieDetails').html(html);
            });
        });
    </script>
</body>

</html>