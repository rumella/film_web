<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $_POST['movie_id'];
    $comment = $_POST['comment'];
    $client_id = $_POST['user_id'];

    // Karaliste dosyasını oku
    $bannedWords = file('../karaliste.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Her yasaklı kelimeyi yorumdan sansürle
    foreach ($bannedWords as $badWord) {
        $pattern = '/\b' . preg_quote($badWord, '/') . '\b/i'; // kelime sınırları ve büyük-küçük harf duyarsız
        $comment = preg_replace($pattern, str_repeat('*', mb_strlen($badWord)), $comment);
    }

    // Yorumun veri tabanına eklenmesi
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
