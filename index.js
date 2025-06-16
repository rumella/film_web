let currentPage = 1;
let apiKeyGlobal = null;
let movieList = [];

$(document).ready(function () {
    $.getJSON('php/get_api_key.php', function (response) {
        apiKeyGlobal = response.apiKey;

        // Türler, yıllar yüklendikten sonra filtreleri ata
        loadGenres(function () {
            loadYears(function () {
                loadRatingFilter(); // sabit bir liste olduğu için fonksiyonlaştırdık
                applyStoredFilters(); // tüm filtrelere localStorage'tan değerleri uygula
                loadMovies(currentPage); // filtreler hazır, filmleri yükle
            });
        });

        loadCarousel();
        initSearchFeature();

        // Sayfalama işlemleri
        $('#prevBtn').on('click', function () {
            if (currentPage > 1) {
                currentPage--;
                loadMovies(currentPage);
                window.scrollTo(0, 0);
            }
        });

        $('#nextBtn').on('click', function () {
            currentPage++;
            loadMovies(currentPage);
            window.scrollTo(0, 0);
        });

        $('#randomBtn').on('click', function () {
            currentPage = Math.floor(Math.random() * 20) + 1;
            loadMovies(currentPage);
            window.scrollTo(0, 0);
        });

        // Filtreler değişince
        $('#genreFilter, #yearFilter, #ratingFilter').on('change', function () {
            localStorage.setItem('selectedGenre', $('#genreFilter').val());
            localStorage.setItem('selectedYear', $('#yearFilter').val());
            localStorage.setItem('selectedRating', $('#ratingFilter').val());
            currentPage = 1;
            loadMovies(currentPage);
        });

        $('#adultToggle').on('change', function () {
            localStorage.setItem('showAdult', $(this).prop('checked'));
            currentPage = 1;
            loadMovies(currentPage);
        });

        // Scroll-to-top görünürlüğü
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 300) {
                $('#scrollTopBtn').fadeIn();
            } else {
                $('#scrollTopBtn').fadeOut();
            }
        });

        $('#scrollTopBtn').on('click', function () {
            const btn = $(this);
            btn.addClass('clicked');
            setTimeout(() => btn.removeClass('clicked'), 500);
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
    });
});

// Türleri yükle
function loadGenres(callback) {
    $.getJSON(`https://api.themoviedb.org/3/genre/movie/list?api_key=${apiKeyGlobal}&language=tr`, function (data) {
        $('#genreFilter').empty().append(`<option value="">Tüm Türler</option>`);
        data.genres.forEach(function (genre) {
            $('#genreFilter').append(`<option value="${genre.id}">${genre.name}</option>`);
        });
        if (callback) callback();
    });
}

// Yılları yükle
function loadYears(callback) {
    const currentYear = new Date().getFullYear();
    $('#yearFilter').empty().append(`<option value="">Tüm Yıllar</option>`);
    for (let year = currentYear; year >= 1950; year--) {
        $('#yearFilter').append(`<option value="${year}">${year}</option>`);
    }
    if (callback) callback();
}

// Sabit rating seçeneklerini yükle
function loadRatingFilter() {
    if ($('#ratingFilter option').length === 0) {
        $('#ratingFilter').append(`<option value="">Tüm Puanlar</option>`);
        for (let i = 10; i >= 1; i--) {
            $('#ratingFilter').append(`<option value="${i}">${i}+</option>`);
        }
    }
}

// localStorage'dan filtreleri uygula
function applyStoredFilters() {
    const savedGenre = localStorage.getItem('selectedGenre');
    const savedYear = localStorage.getItem('selectedYear');
    const savedRating = localStorage.getItem('selectedRating');
    const showAdult = localStorage.getItem('showAdult');

    if (savedGenre) $('#genreFilter').val(savedGenre);
    if (savedYear) $('#yearFilter').val(savedYear);
    if (savedRating) $('#ratingFilter').val(savedRating);
    $('#adultToggle').prop('checked', showAdult === 'true');
}

// Film listesini yükle
function loadMovies(page = 1) {
    const genre = $('#genreFilter').val();
    const year = $('#yearFilter').val();
    const rating = $('#ratingFilter').val();
    const yearRange = $('#yearRangeSwitch').is(':checked');
    const ratingRange = $('#ratingRangeSwitch').is(':checked');
    const showAdult = $('#adultToggle').is(':checked');

    let url = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKeyGlobal}&language=tr&page=${page}&sort_by=popularity.desc&include_adult=${showAdult}`;

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

    $.getJSON(url, function (data) {
        $('#movieGrid').empty();

        data.results.forEach(function (movie) {
            const movieRating = movie.vote_average;
            const movieYear = movie.release_date ? parseInt(movie.release_date.split('-')[0]) : null;

            if (!showAdult && movie.adult) return;

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
}

// Carousel'i yükle
function loadCarousel() {
    const url = `https://api.themoviedb.org/3/movie/popular?api_key=${apiKeyGlobal}&language=tr&page=1`;

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
    });
}

// Arama özelliği
function initSearchFeature() {
    $('#movie_name').on('input', function () {
        const query = $(this).val().trim();
        if (query.length > 0) {
            searchMovies(query);
        } else {
            $('#movie_results').empty().hide();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#movie_name, #searchSuggestions').length) {
            $('#searchSuggestions').hide();
        }
    });
}

function searchMovies(query) {
    $.ajax({
        url: "https://api.themoviedb.org/3/search/movie",
        type: "GET",
        data: {
            api_key: apiKeyGlobal,
            query: query,
            language: "tr"
        },
        success: function (response) {
            const movies = response.results;
            movieList = [];
            let output = '';

            movies.forEach(function (movie) {
                movieList.push(movie);

                const posterUrl = movie.poster_path
                    ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
                    : 'assets/placeholder_poster.png';

                output += `
                    <div class="col-12">
                        <div class="search-result-card add_movie" data-id="${movie.id}">
                            <img src="${posterUrl}" alt="${movie.title}">
                            <div class="card-body">
                                <h6 class="card-title">${movie.title}</h6>
                                <p class="mb-1"><strong>Orijinal:</strong> ${movie.original_title}</p>
                                <p><strong>Yıl:</strong> ${movie.release_date?.split('-')[0] || 'Bilinmiyor'}</p>
                            </div>
                        </div>
                    </div>`;
            });

            $('#movie_results').html(output).show();
        }
    });
}
