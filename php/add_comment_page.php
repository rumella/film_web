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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-light text-dark border-bottom">
            <h4 class="mb-0">Film Yorum Sayfası</h4>
        </div>
        <div class="card-body">
            <form id="commentForm">
                <div class="mb-3">
                    <label for="comment" class="form-label">Yorumunuz (10-500 karakter):</label>
                    <textarea class="form-control" id="comment" name="comment" rows="5" minlength="10" maxlength="500" required></textarea>
                    <div class="form-text"><span id="charCount">0</span>/500 karakter</div>
                </div>
                <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie_id); ?>">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <button type="submit" class="btn btn-primary">Yorumu Gönder</button>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const commentBox = $('#comment');
    const charCount = $('#charCount');

    commentBox.on('input', function () {
        const length = $(this).val().length;
        charCount.text(length);
    });

    $('#commentForm').on('submit', function (e) {
        e.preventDefault();

        const commentLength = commentBox.val().length;
        if (commentLength < 10 || commentLength > 500) {
            alert("Yorumunuz 10 ile 500 karakter arasında olmalıdır.");
            return;
        }

        $.ajax({
            url: 'add_comment.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Yorum başarıyla eklendi!');
                    window.location.href = 'show_comment_page.php';
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Sunucu hatası oluştu.');
            }
        });
    });
</script>

</body>
</html>
