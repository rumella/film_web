let apiKey = '';
let currentPage = 1;

// API anahtarını al ve sayfa işlemlerini başlat
$.getJSON('php/get_api_key.php', function (data) {
    apiKey = data.apiKey;
    loadPopularMovies();
    loadMovies(currentPage);
});

// Popüler filmleri yükleyen fonksiyon
function loadPopularMovies() {
    fetch(`https://api.themoviedb.org/3/movie/popular?api_key=${apiKey}&language=tr-TR&page=1`)
        .then(response => response.json())
        .then(data => {
            let carouselContent = '';
            data.results.forEach((movie, index) => {
                const image = movie.backdrop_path 
                    ? `https://image.tmdb.org/t/p/w1280${movie.backdrop_path}` 
                    : 'https://via.placeholder.com/1280x400?text=No+Image';
                
                const overview = movie.overview ? movie.overview.replace(/"/g, '&quot;') : 'Açıklama bulunamadı.';

                carouselContent += `
                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                        <img src="${image}" class="d-block w-100" alt="${movie.title}">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>${movie.title}</h5>
                            <p>${overview}</p>
                        </div>
                    </div>`;
            });
            document.getElementById('carouselContent').innerHTML = carouselContent;
        });
}

// Filmleri grid şeklinde yükleyen fonksiyon
function loadMovies(page = 1) {
    fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=tr-TR&sort_by=release_date.desc&page=${page}&per_page=20`)
        .then(response => response.json())
        .then(data => {
            let movieGrid = '';
            data.results.forEach(movie => {
                const poster = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : 'https://via.placeholder.com/500x750?text=No+Image';
                if (movie.poster_path) {
                    movieGrid += `
                        <div class="col">
                            <div class="card">
                                <img src="${poster}" class="card-img-top" alt="${movie.title}">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: 1.5rem; font-weight: bold;">${movie.title}</h5>
                                </div>
                            </div>
                        </div>`;
                };                
            });
            document.getElementById('movieGrid').innerHTML = movieGrid;
        });
}

// Sayfa geçişleri
$('#nextBtn').click(function () {
    currentPage++;
    loadMovies(currentPage);
});
$('#prevBtn').click(function () {
    if (currentPage > 1) {
        currentPage--;
        loadMovies(currentPage);
    }
});
