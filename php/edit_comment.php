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
    $yasakliKelimeler = file('karaliste.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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
    <title>Yorumu Düzenle</title>
</head>
<body>
    <h2>Yorumu Düzenle</h2>
    <form method="POST" action="edit_comment.php">
        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment_id); ?>">
        <textarea name="comment" rows="5" cols="50"><?php echo htmlspecialchars($commentText); ?></textarea><br>
        <button type="submit">Kaydet</button>
    </form>
</body>
</html>
