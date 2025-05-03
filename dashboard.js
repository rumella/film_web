$(document).ready(function () {
    let movieList = []; // Aranan filmleri burada saklayacağız

    // Film arama
    $('#search_movie').click(function () {
        var movieName = $('#movie_name').val();

        $.ajax({
            url: "https://api.themoviedb.org/3/search/movie",
            type: "GET",
            data: {
                api_key: apiKey,
                query: movieName
            },
            success: function (response) {
                var movies = response.results;
                movieList = []; // Her aramada önceki listeyi temizle
                var output = '';

                movies.forEach(function (movie, index) {
                    movieList.push(movie); // Listeye ekle

                    // Poster URL oluşturuluyor
                    var posterUrl = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : '../assets/placeholder_poster.png';

                    output += `
                        <div class="movie-grid-item" style="margin-bottom: 20px;">
                            <img src="${posterUrl}" alt="${movie.title}" style="width:100px; height:auto; float:left; margin-right: 10px;"></br>
                            <p><strong>${movie.title}</strong></br> (${movie.release_date})</p></br>
                            <button class="add_movie" data-index="${index}">Ekle</button>
                        </div>
                    `;
                });

                $('#movie_results').html(output);

                // Eğer içerik varsa, görünür yap ve stil ekle
                if ($('#movie_results').html().trim() !== '') {
                    $('#movie_results').addClass('show');
                } else {
                    $('#movie_results').removeClass('show');
                }
            },
            error: function (xhr, status, error) {
                console.log("Hata: " + error);
            }
        });
    });

    // Film ekleme
    $(document).on('click', '.add_movie', function () {
        const index = $(this).data('index');
        const movieData = movieList[index];

        $.ajax({
            url: 'update_movie.php',
            type: 'POST',
            data: {
                movie_data: JSON.stringify(movieData)
            },
            success: function (response) {
                alert("Film başarıyla eklendi!");
                location.reload();
            },
            error: function (xhr, status, error) {
                alert("Film eklenirken bir hata oluştu.");
            }
        });
    });

    // İzlenen filmleri gösterme
    function displayWatchedMovies() {
        let watchedMoviesHtml = '<h2>İzlediğiniz Filmler</h2>';

        if (moviesFromPHP.length > 0) {
            moviesFromPHP.forEach(function (movie, index) {
                const posterUrl = "https://image.tmdb.org/t/p/w500" + movie.poster_path;
                const overview = movie.overview ? movie.overview : "Açıklama mevcut değil.";

                watchedMoviesHtml += `
                    <div class="movie">
                        <img src="${posterUrl}" alt="${movie.title}">
                        <h3>${movie.title}</h3>
                        <p><strong>Yayın Tarihi:</strong> ${movie.release_date}</p>
                        <p><strong>Puan:</strong> ${movie.vote_average}</p>
                        <p><strong>Açıklama:</strong> ${overview}</p>
                        <button class="remove-movie-btn" data-index="${index}">Sil</button>
                    </div>
                `;
            });
        } else {
            watchedMoviesHtml += '<p>Henüz izlediğiniz film yok.</p>';
        }

        $('#watched_movies').html(watchedMoviesHtml);
    }

    // Sayfa yüklendiğinde izlenen filmleri göster
    displayWatchedMovies();

    // Film silme
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
                console.log("Film silme cevabı:", response);  // Silme cevabını konsola yazdırıyoruz
                const res = JSON.parse(response); // JSON cevabını parse et
                if (res.success) {
                    alert('Film başarıyla silindi');
                    // Silinen filmi array'den çıkaralım
                    moviesFromPHP.splice(movieIndex, 1);
                    // Sayfayı yeniden render edelim
                    displayWatchedMovies();
                } else {
                    alert('Film silinemedi: ' + res.message);
                }
            },
            error: function (xhr, status, error) {
                console.log("Film silme hata:", error);  // Silme sırasında oluşan hatayı konsola yazdırıyoruz
                alert('Bir hata oluştu');
            }
        });
    });
});
