<?php
session_start();
$movie_id = $_GET['movie_id'];
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yorum Yap</title>
</head>
<body>
    <h2>Film Yorum Sayfası</h2>
    <form id="commentForm">
        <textarea name="comment" placeholder="Yorumunuzu yazın..." required></textarea><br>
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <button type="submit">Yorumu Gönder</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $('#commentForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'add_comment.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                alert('Yorum başarıyla eklendi!');
                window.location.href = 'dashboard.php'; // veya başka bir sayfa
            },
            error: function () {
                alert('Yorum eklenemedi.');
            }
        });
    });
    </script>
</body>
</html>
