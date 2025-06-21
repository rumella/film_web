<?php
session_start();
require 'db.php';

$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['comment_id'])) {
    $comment_id = $_GET['comment_id'];

    $stmt = $conn->prepare("SELECT comment FROM comments WHERE id = ? AND client_id = ?");
    $stmt->bind_param("ii", $comment_id, $userId);
    $stmt->execute();
    $stmt->bind_result($commentText);
    if (!$stmt->fetch()) {
        echo "Bu yorumu düzenleme yetkiniz yok.";
        exit;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['comment'])) {
    $comment_id = $_POST['comment_id'];
    $new_comment = trim($_POST['comment']);

    // Yasaklı kelimeleri karaliste.txt'den al
    $yasakliKelimeler = file('../karaliste.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Yasaklı kelimeleri *** ile değiştir
    $filtered_comment = $new_comment;
    foreach ($yasakliKelimeler as $kelime) {
        $kelime = trim($kelime);
        if ($kelime !== '') {
            $filtered_comment = preg_replace("/\b" . preg_quote($kelime, '/') . "\b/i", '***', $filtered_comment);
        }
    }

    $stmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ? AND client_id = ?");
    $stmt->bind_param("sii", $filtered_comment, $comment_id, $userId);
    if ($stmt->execute()) {
        header("Location: show_comment_page.php");
        exit;
    } else {
        echo "Yorum güncellenemedi.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorumu Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1c1c1c;
            color: whitesmoke;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: #2b2b2b;
            color: whitesmoke;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
        }

        .card h2 {
            text-align: center;
        }

        .card textarea {
            width: 100%;
            resize: none;
            background-color: #333;
            color: whitesmoke;
            border: 1px solid #444;
            border-radius: 5px;
            padding: 10px;
        }

        .card button {
            width: 100%;
            background-color: #007bff;
            color: whitesmoke;
            border: none;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }

        .card button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 576px) {
            .card {
                padding: 15px;
                width: 90%;
                max-width: none;
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Yorumu Düzenle</h2>
        <form method="POST" action="edit_comment.php">
            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment_id); ?>">
            <textarea name="comment" rows="5"><?php echo htmlspecialchars($commentText); ?></textarea><br>
            <button type="submit">Kaydet</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

