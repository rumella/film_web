$(document).ready(function () {
    let movieList = []; // API'den aranan filmleri burada saklayacağız

    // 🎬 Film Arama
    $('#search_movie').click(function () {
        const movieName = $('#movie_name').val();

        $.ajax({
            url: "https://api.themoviedb.org/3/search/movie",
            type: "GET",
            data: {
                api_key: apiKey,
                query: movieName
            },
            success: function (response) {
                const movies = response.results;
                movieList = [];
                let output = '';

                movies.forEach(function (movie, index) {
                    movieList.push(movie);

                    const posterUrl = movie.poster_path
                        ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
                        : '../assets/placeholder_poster.png';

                    output += `
                        <div class="movie-grid-item" style="margin-bottom: 20px;">
                            <img src="${posterUrl}" alt="${movie.title}" style="width:100px; height:auto; float:left; margin-right: 10px;"><br>
                            <p><strong>${movie.title}</strong><br> (${movie.release_date})</p><br>
                            <button class="add_movie" data-index="${index}">Ekle</button>
                        </div>
                    `;
                });

                $('#movie_results').html(output);

                $('#movie_results').toggleClass('show', $('#movie_results').html().trim() !== '');
            },
            error: function (xhr, status, error) {
                console.log("Hata: " + error);
            }
        });
    });

    // ➕ Film Ekleme
    $(document).on('click', '.add_movie', function () {
        const index = $(this).data('index');
        const movieData = movieList[index];

        $.ajax({
            url: 'update_movie.php',
            type: 'POST',
            data: {
                movie_data: JSON.stringify(movieData)
            },
            success: function () {
                alert("Film başarıyla eklendi!");
                location.reload();
            },
            error: function () {
                alert("Film eklenirken bir hata oluştu.");
            }
        });
    });

    // 📺 İzlenen Filmleri Listele
    function displayWatchedMovies() {
        let html = '<h2>İzlediğiniz Filmler</h2>';

        if (moviesFromPHP.length > 0) {
            moviesFromPHP.forEach(function (movie, index) {
                const posterUrl = `https://image.tmdb.org/t/p/w500${movie.poster_path}`;
                const overview = movie.overview || "Açıklama mevcut değil.";

                html += `
                    <div class="movie">
                        <img src="${posterUrl}" alt="${movie.title}">
                        <h3>${movie.title}</h3>
                        <p><strong>Yayın Tarihi:</strong> ${movie.release_date}</p>
                        <p><strong>Puan:</strong> ${movie.vote_average}</p>
                        <p><strong>Açıklama:</strong> ${overview}</p>
                        <button class="remove-movie-btn" data-index="${index}">Sil</button>
                        <button class="comment-movie-btn" data-index="${index}">Yorum Yap</button>
                    </div>
                `;
            });
        } else {
            html += '<p>Henüz izlediğiniz film yok.</p>';
        }

        $('#watched_movies').html(html);
    }

    displayWatchedMovies();

    // 💬 Yorum Sayfasına Yönlendir
    $(document).on('click', '.comment-movie-btn', function () {
        const movieIndex = $(this).data('index');
        const movieId = moviesFromPHP[movieIndex].id;

        // Yorum formu ayrı bir sayfada açılıyor
        window.location.href = `add_comment_page.php?movie_id=${movieId}`;
    });

    // ❌ Film Silme
    $(document).on('click', '.remove-movie-btn', function () {
        const movieIndex = $(this).data('index');
        const movieToRemove = moviesFromPHP[movieIndex];

        $.ajax({
            url: 'remove_movie.php',
            type: 'POST',
            data: {
                movie_id: movieToRemove.id
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success) {
                    alert('Film başarıyla silindi');
                    moviesFromPHP.splice(movieIndex, 1);
                    displayWatchedMovies();
                } else {
                    alert('Film silinemedi: ' + res.message);
                }
            },
            error: function () {
                alert('Bir hata oluştu');
            }
        });
    });
});
