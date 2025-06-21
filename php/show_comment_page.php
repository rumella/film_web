<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}

$navbarTitle = "Yorumlar";
$navbarItems = ['home', 'dashboard', 'profile', 'edit_profile', 'logout'];

$apiKey = trim(file_get_contents('../api_key.txt'));

$userId = $_SESSION['user_id'] ?? 5;
$userRole = $_SESSION['authority_id'] ?? '3';

$filterMovieId = $_GET['filter_movie_id'] ?? null;
$showOnlyMine = $_GET['only_mine'] ?? null;

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

$replyCounts = [];
$replyStmt = $conn->prepare("SELECT comment_id, COUNT(*) as count FROM replies WHERE client_id = ? GROUP BY comment_id");
$replyStmt->bind_param("i", $userId);
$replyStmt->execute();
$replyResults = $replyStmt->get_result();
while ($row = $replyResults->fetch_assoc()) {
    $replyCounts[$row['comment_id']] = $row['count'];
}

$allReplies = [];
$replyFetchStmt = $conn->prepare("SELECT replies.*, clients.name FROM replies JOIN clients ON replies.client_id = clients.id ORDER BY replies.created_at ASC ");
$replyFetchStmt->execute();
$replyResult = $replyFetchStmt->get_result();
while ($replyRow = $replyResult->fetch_assoc()) {
    $allReplies[$replyRow['comment_id']][] = $replyRow;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        const apiKey = "<?php echo $apiKey; ?>";
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgb(0, 0, 0);
            color: whitesmoke;
            font-family: Arial, sans-serif;
        }

        .search-area,
        .comment-box {
            background: #1c1c1c;
            padding: 20px;
            margin: 10px auto;
            width: 90%;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        #movieSuggestions div {
            cursor: pointer;
            padding: 5px;
            background-color: #1c1c1c;
            margin: 2px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #movieSuggestions div:hover {
            background-color: #2b2b2b;
        }

        .movie {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            position: relative;
            padding-bottom: 30px;
        }

        .movie img {
            width: 100px;
            height: auto;
            border-radius: 4px;
        }

        .edit-button,
        .delete-button {
            padding: 6px 12px;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
            border-radius: 4px;
            border: none;
        }

        .edit-button {
            background-color: #007bff;
            color: white;
        }

        .delete-button {
            background-color: red;
            color: white;
        }

        .comment-date {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 0.9rem;
            background-color: #2b2b2b;
            padding: 4px 8px;
            border-radius: 4px;
            z-index: 1;
        }

        @media (max-width: 768px) {
            body {
                margin: 0;
            }

            .search-area,
            .comment-box {
                padding: 20px;
                margin: 10px auto;
                width: 100%;
                max-width: 100%;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                overflow-wrap: break-word;
            }

            .movie {
                display: flex;
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
                position: relative;
                padding-bottom: 30px;
                width: 100%;
            }

            .movie img {
                width: 100%;
                height: auto;
                border-radius: 4px;
            }

            .movie-info {
                display: flex;
                flex-direction: column;
                gap: 10px;
                word-wrap: break-word;
                width: 100%;
                margin-left: 0;
                padding-left: 0;
                box-sizing: border-box;
            }

            .edit-button,
            .delete-button,
            .reply-toggle {
                padding: 8px 16px;
                font-size: 1rem;
                margin-top: 10px;
                display: inline-block;
                border-radius: 4px;
                border: none;
                width: 100%;
                box-sizing: border-box;
                text-align: center;
            }

            .edit-button {
                background-color: #007bff;
                color: white;
            }

            .delete-button {
                background-color: red;
                color: white;
            }

            .reply-toggle {
                background-color: #28a745;
                color: white;
                margin-left: 0;
            }

            .comment-date {
                position: absolute;
                bottom: 0px;
                right: 0px;
                font-size: 0.9rem;
                background-color: #2b2b2b;
                padding: 4px 0px;
                border-radius: 4px;
                z-index: 1;
            }

            textarea {
                width: 100%;
                resize: vertical;
                box-sizing: border-box;
            }

            /* 🔙 Girinti geri getiriliyor — eski stil korunuyor */
            .movie .mt-3.ms-4 {
                margin-left: 1.5rem !important;
                padding-left: 1rem !important;
            }

            .movie .mt-3.ms-4 .border-start {
                border-left: 1px solid #ccc;
                padding-left: 1rem !important;
            }

            .movie .mt-3.ms-4 p {
                margin-left: 1rem !important;
                padding-left: 0 !important;
            }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="search-area">
        <h2>Yorum Filtreleme</h2>
        <input type="text" id="movie_name" class="form-control mb-2" placeholder="Film adı girin...">
        <div id="movieSuggestions"></div>
        <form method="GET" action="">
            <input type="hidden" name="filter_movie_id" id="selected_movie_id">
            <div class="form-check mt-2">
                <input type="checkbox" class="form-check-input" name="only_mine" value="1" id="onlyMineCheckbox" <?php if ($showOnlyMine) echo "checked"; ?>>
                <label class="form-check-label" for="onlyMineCheckbox">Sadece benim yorumlarım</label>
            </div>
            <p id="selected_movie_title" class="mt-2 fw-bold"></p>
            <button type="submit" class="btn btn-primary mt-2">Filtrele</button>
        </form>
    </div>

    <?php foreach ($comments as $comment): ?>
        <?php $movie = $movieDetails[$comment['movie_id']]; ?>
        <div class="comment-box">
            <div class="movie">
                <img src="https://image.tmdb.org/t/p/w500/<?php echo $movie['poster_path']; ?>" alt="<?php echo $movie['title']; ?>">
                <div class="movie-info">
                    <h5><?php echo $movie['title']; ?> (<?php echo $movie['release_date']; ?>)</h5>
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

                    <?php if (!empty($allReplies[$comment['id']])): ?>
                        <div class="mt-3">
                            <strong>Cevaplar:</strong>
                            <?php foreach ($allReplies[$comment['id']] as $reply): ?>
                                <div class="border-start ps-3 mb-2">
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($reply['name']); ?>:</strong> <?php echo htmlspecialchars($reply['reply']); ?></p>
                                    <small class="text-light"><?php echo date('d.m.Y H:i', strtotime($reply['created_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($userId && ($replyCounts[$comment['id']] ?? 0) < 5): ?>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary reply-toggle" data-id="<?php echo $comment['id']; ?>">Cevapla</button>
                            <form method="POST" action="save_reply.php" class="reply-form mt-2 d-none" id="reply-form-<?php echo $comment['id']; ?>">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <textarea name="reply_text" class="form-control mb-2" placeholder="Cevabınızı yazın..." required></textarea>
                                <button type="submit" class="btn btn-success btn-sm">Gönder</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="comment-date">
                    <?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let movieList = [];

        $('#movie_name').on('input', function() {
            const query = $(this).val();
            if (query.length < 2) {
                $('#movieSuggestions').html('');
                return;
            }

            $.ajax({
                url: `https://api.themoviedb.org/3/search/movie`,
                type: "GET",
                data: {
                    api_key: apiKey,
                    language: "tr-TR",
                    query: query
                },
                success: function(response) {
                    movieList = response.results;
                    let suggestions = '';
                    response.results.slice(0, 5).forEach((movie) => {
                        suggestions += `<div data-id="${movie.id}" data-title="${movie.title} (${movie.release_date})">${movie.title} (${movie.release_date})</div>`;
                    });
                    $('#movieSuggestions').html(suggestions);
                }
            });
        });

        $(document).on('click', '#movieSuggestions div', function() {
            const movieId = $(this).data('id');
            const title = $(this).data('title');
            $('#selected_movie_id').val(movieId);
            $('#selected_movie_title').text(title + " seçildi.");
            $('#movieSuggestions').html('');
        });

        $(document).on('click', '.reply-toggle', function() {
            const id = $(this).data('id');
            $(`#reply-form-${id}`).toggleClass('d-none');
        });
    </script>

</body>

</html>