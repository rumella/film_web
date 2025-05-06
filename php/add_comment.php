<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $_POST['movie_id'];
    $comment = $_POST['comment'];
    $client_id = $_POST['user_id'];

    // Aynı kullanıcı aynı filme 3 yorumdan fazla yazamasın
    $checkQuery = "SELECT COUNT(*) as count FROM comments WHERE movie_id = ? AND client_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('is', $movie_id, $client_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $row = $checkResult->fetch_assoc();

    if ($row['count'] >= 2) {
        echo json_encode(["success" => false, "message" => "Her filme en fazla 2 yorum yapabilirsiniz."]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // Karaliste dosyasını oku
    $bannedWords = file('../karaliste.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($bannedWords as $badWord) {
        $pattern = '/\b' . preg_quote($badWord, '/') . '\b/i';
        $comment = preg_replace($pattern, str_repeat('*', mb_strlen($badWord)), $comment);
    }

    // Yorum ekle
    $query = "INSERT INTO comments (movie_id, comment, client_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iss', $movie_id, $comment, $client_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Yorum eklenemedi."]);
    }

    $stmt->close();
    $conn->close();
}
?>
