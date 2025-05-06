<?php
session_start();
require 'db.php';

$navbarTitle = "Yorumlar";
$navbarItems = ['home', 'dashboard', 'profile', 'edit_profile', 'logout', 'show_comment'];
include 'navbar.php';

$userId = $_SESSION['user_id'] ?? 5;
$userRole = $_SESSION['authority_id'] ?? '3';

$filterMovieId = $_GET['filter_movie_id'] ?? null;
$showOnlyMine = $_GET['only_mine'] ?? null;

$apiKey = trim(file_get_contents('../api_key.txt'));

if ($filterMovieId) {
    if ($showOnlyMine) {
        $commentQuery = $conn->prepare("
            SELECT comments.id, comments.comment, comments.created_at, comments.movie_id, clients.name, comments.client_id
            FROM comments
            JOIN clients ON comments.client_id = clients.id
            WHERE comments.movie_id = ? AND comments.client_id = ?
            ORDER BY comments.created_at DESC
        ");
        $commentQuery->bind_param("ii", $filterMovieId, $userId);
    } else {
        $commentQuery = $conn->prepare("
            SELECT comments.id, comments.comment, comments.created_at, comments.movie_id, clients.name, comments.client_id
            FROM comments
            JOIN clients ON comments.client_id = clients.id
            WHERE comments.movie_id = ?
            ORDER BY comments.created_at DESC
        ");
        $commentQuery->bind_param("i", $filterMovieId);
    }
} else {
    if ($showOnlyMine) {
        $commentQuery = $conn->prepare("
            SELECT comments.id, comments.comment, comments.created_at, comments.movie_id, clients.name, comments.client_id
            FROM comments
            JOIN clients ON comments.client_id = clients.id
            WHERE comments.client_id = ?
            ORDER BY comments.created_at DESC
        ");
        $commentQuery->bind_param("i", $userId);
    } else {
        $commentQuery = $conn->prepare("
            SELECT comments.id, comments.comment, comments.created_at, comments.movie_id, clients.name, comments.client_id
            FROM comments
            JOIN clients ON comments.client_id = clients.id
            ORDER BY comments.created_at DESC
        ");
    }
}

$commentQuery->execute();
$comments = $commentQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// TMDB API'den film bilgilerini çek
$movieDetails = [];
foreach ($comments as $comment) {
    $movieId = $comment['movie_id'];
    if (!isset($movieDetails[$movieId])) {
        $apiUrl = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}&language=tr-TR";
        $response = @file_get_contents($apiUrl);
        if ($response !== false) {
            $movieData = json_decode($response, true);
            $movieDetails[$movieId] = [
                'title' => $movieData['title'] ?? 'Başlık bulunamadı',
                'release_date' => $movieData['release_date'] ?? 'Tarih yok',
                'poster_path' => $movieData['poster_path'] ?? null,
            ];
        } else {
            $movieDetails[$movieId] = [
                'title' => 'Bilgi alınamadı',
                'release_date' => 'Tarih yok',
                'poster_path' => null,
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yorumlar</title>
    <script>
        const apiKey = "<?php echo $apiKey; ?>";
    </script>
    <style>
       body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    padding: 0px;
    margin: 0;
}

.search-area {
    margin-bottom: 30px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 100%;
    box-sizing: border-box;
}

#movieSuggestions div {
    cursor: pointer;
    padding: 5px;
    background-color: #eee;
    margin: 2px 0;
    border-radius: 4px;
}

#movieSuggestions div:hover {
    background-color: #ccc;
}

.comment-box {
    background: #fff;
    padding: 20px;
    width: 100%;
    box-sizing: border-box;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    cursor: default;
}

.edit-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px; /* sil butonundan biraz uzak olsun */
}


.movie {
    display: flex;
    align-items: flex-start;
}

.movie img {
    width: 100px;
    height: auto;
    margin-right: 20px;
}

.movie-info {
    flex-grow: 1;
    text-align: left;
}

.movie-info h3, .movie-info p {
    margin: 5px 0;
    text-align: left;
}

.delete-button {
    background-color: red;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="search-area">
    <h2>Yorum Filtreleme</h2>
    <input type="text" id="movie_name" placeholder="Film adı girin...">
    <div id="movieSuggestions"></div>
    <form method="GET" action="">
        <input type="hidden" name="filter_movie_id" id="selected_movie_id">
        <label><input type="checkbox" name="only_mine" value="1" <?php if ($showOnlyMine) echo "checked"; ?>> Sadece benim yorumlarım</label>
        <br><br>
        <p id="selected_movie_title" style="font-weight: bold;"></p>
        <button type="submit">Filtrele</button>
    </form>
</div>

<?php foreach ($comments as $comment): ?>
    <?php $movie = $movieDetails[$comment['movie_id']]; ?>
    <div class="comment-box">
        <div class="movie">
            <img src="https://image.tmdb.org/t/p/w500/<?php echo $movie['poster_path']; ?>" alt="<?php echo $movie['title']; ?>">
            <div class="movie-info">
                <h3><?php echo $movie['title']; ?> (<?php echo $movie['release_date']; ?>)</h3>
                <p><strong>Yorum Yapan:</strong> <?php echo $comment['name']; ?></p>
                <p><strong>Yorum:</strong> <?php echo $comment['comment']; ?></p>

                <?php if ($userRole == '1' || $userId == $comment['client_id']): ?>
                    <form method="POST" action="delete_comment.php" style="display:inline;" onsubmit="return confirm('Bu yorumu silmek istediğinizden emin misiniz?');">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                        <button type="submit" class="delete-button">Sil</button>
                    </form>

                    <?php if ($userId == $comment['client_id']): ?>
    <a href="edit_comment.php?comment_id=<?php echo $comment['id']; ?>" class="edit-button">Düzenle</a>
<?php endif; ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
let movieList = [];

$('#movie_name').on('input', function () {
    const query = $(this).val();
    if (query.length < 2) {
        $('#movieSuggestions').html('');
        return;
    }

    $.ajax({
        url: `https://api.themoviedb.org/3/search/movie`,
        type: "GET",
        data: { api_key: apiKey, language: "tr-TR", query: query },
        success: function (response) {
            movieList = response.results;
            let suggestions = '';
            response.results.slice(0, 5).forEach((movie) => {
                suggestions += `<div data-id="${movie.id}" data-title="${movie.title} (${movie.release_date})">${movie.title} (${movie.release_date})</div>`;
            });
            $('#movieSuggestions').html(suggestions);
        }
    });
});

$(document).on('click', '#movieSuggestions div', function () {
    const movieId = $(this).data('id');
    const title = $(this).data('title');
    $('#selected_movie_id').val(movieId);
    $('#selected_movie_title').text(title + " seçildi.");
    $('#movieSuggestions').html('');
});
</script>

</body>
</html>
