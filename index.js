let currentPage = 1; // Başlangıçta sayfa 1

$(document).ready(function () {
    // Sayfa yenilendiğinde Adult Toggle'ın durumu kaydedilsin
    if (localStorage.getItem('showAdult') === 'true') {
        $('#adultToggle').prop('checked', true); // Toggle'ı açık yap
    } else {
        $('#adultToggle').prop('checked', false); // Toggle'ı kapalı yap
    }

    loadGenres();
    loadYears();
    loadMovies(currentPage);  // İlk sayfa yükleme
    loadCarousel();

    // Previous butonu
    $('#prevBtn').on('click', function () {
        if (currentPage > 1) {
            currentPage--;
            loadMovies(currentPage); // Sayfa bir geri git
            window.scrollTo(0, 0);
        }
    });

    // Next butonu
    $('#nextBtn').on('click', function () {
        currentPage++;
        loadMovies(currentPage); // Sayfa bir ileri git
        window.scrollTo(0, 0);
    });

    // Random butonu
    $('#randomBtn').on('click', function () {
        currentPage = Math.floor(Math.random() * 20) + 1; // Random sayfa numarası oluştur
        loadMovies(currentPage); // Random filmleri yükle
        window.scrollTo(0, 0);
    });

    // Adult toggle durumunu değiştirdiğinde kaydet
    $('#adultToggle').on('change', function () {
        localStorage.setItem('showAdult', $(this).prop('checked'));
        currentPage = 1; // Toggle değiştiğinde sayfayı 1'e al
        loadMovies(currentPage);
    });

    // Filtreler değiştiğinde sayfayı 1'e sıfırla
    $('#genreFilter, #yearFilter, #ratingFilter, #yearRangeSwitch, #ratingRangeSwitch').on('change', function () {
        currentPage = 1; // Filtreler değiştiğinde sayfayı 1'e sıfırla
        loadMovies(currentPage);
    });
});

function loadGenres() {
    $.getJSON('php/get_api_key.php', function (response) {
        const apiKey = response.apiKey;
        $.getJSON(`https://api.themoviedb.org/3/genre/movie/list?api_key=${apiKey}&language=tr`, function (data) {
            data.genres.forEach(function (genre) {
                $('#genreFilter').append(`<option value="${genre.id}">${genre.name}</option>`);
            });
        });
    });
}

function loadYears() {
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= 1950; year--) {
        $('#yearFilter').append(`<option value="${year}">${year}</option>`);
    }
}

function loadMovies(page = 1) {
    const genre = $('#genreFilter').val();
    const year = $('#yearFilter').val();
    const rating = $('#ratingFilter').val();
    const yearRange = $('#yearRangeSwitch').is(':checked');
    const ratingRange = $('#ratingRangeSwitch').is(':checked');
    const showAdult = $('#adultToggle').is(':checked'); // Adult toggle durumu

    $.getJSON('php/get_api_key.php', function (response) {
        const apiKey = response.apiKey;
        let url = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=tr&page=${page}`; // page değişkenini kullanıyoruz

        if (genre) url += `&with_genres=${genre}`;
        if (year) {
            if (yearRange) {
                url += `&primary_release_year=${year}`;
            } else {
                url += `&primary_release_date.lte=${year}-12-31`;
            }
        }
        if (rating) {
            if (ratingRange) {
                url += `&vote_average.gte=${rating}`;
            } else {
                url += `&vote_average.lte=${rating}`;
            }
        }

        // Adult içeriği kontrol et ve URL'ye ekle
        url += `&include_adult=${showAdult}`;

        $.getJSON(url, function (data) {
            $('#movieGrid').empty();
            //console.log(currentPage);
            data.results.forEach(function (movie) {
                const movieRating = movie.vote_average;
                const movieYear = movie.release_date ? parseInt(movie.release_date.split('-')[0]) : null;

                // **Adult içerikleri kontrol et**:
                // Eğer toggle kapalıysa (showAdult == false), adult içerik gelmesin
                //console.log(`Movie: ${movie.title}, Adult: ${movie.adult}`); // Burada log ekliyoruz
                if (showAdult === false && movie.adult === true) return;

                if (rating) {
                    if (ratingRange && movieRating < rating) return;
                    if (!ratingRange && movieRating > rating) return;
                }

                if (year) {
                    if (yearRange && movieYear !== parseInt(year)) return;
                    if (!yearRange && movieYear > parseInt(year)) return;
                }

                const poster = movie.poster_path
                    ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
                    : 'assets/placeholder_poster.png';

                const movieCard = $(` 
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <img src="${poster}" class="card-img-top" alt="${movie.title}">
                            <div class="card-body">
                                <h5 class="card-title">${movie.title}</h5>
                            </div>
                        </div>
                    </div>
                `);

                movieCard.on('click', function () {
                    window.location.href = `php/movie_detail.php?id=${movie.id}`;
                });

                $('#movieGrid').append(movieCard);
            });
        });
    });
}

function loadCarousel() {
    $.getJSON('php/get_api_key.php', function (response) {
        const apiKey = response.apiKey;
        const url = `https://api.themoviedb.org/3/movie/popular?api_key=${apiKey}&language=tr&page=1`;

        $.getJSON(url, function (data) {
            $('#carouselContent').empty();
            data.results.slice(0, 5).forEach(function (movie, index) {
                const poster = movie.backdrop_path
                    ? `https://image.tmdb.org/t/p/original${movie.backdrop_path}`
                    : 'assets/placeholder_poster.png';

                const carouselItem = ` 
                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                        <img src="${poster}" class="d-block w-100" alt="${movie.title}">
                        <div class="carousel-caption d-block">
                            <h5>${movie.title}</h5>
                            <p>${movie.overview}</p>
                        </div>
                    </div>
                `;
                $('#carouselContent').append(carouselItem);
            });

            document.querySelectorAll('.carousel-item').forEach(item => {
                item.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        const caption = this.querySelector('.carousel-caption');
                        if (caption) {
                            const isVisible = caption.style.visibility === 'visible';
                            caption.style.visibility = isVisible ? 'hidden' : 'visible';
                            caption.style.opacity = isVisible ? '0' : '1';
                        }
                    }
                });
            });
        });
    });
}
