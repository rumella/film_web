<?php
session_start();
include('db.php');




$userId = $_SESSION['user_id'];
$movieJson = $_POST['movie_data'];
$movie = json_decode($movieJson, true);

// Mevcut kayıt var mı kontrol et
$sql = "SELECT liked_movies FROM anatable WHERE client_id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $likedMovies = json_decode($row['liked_movies'], true);

    // likedMovies boşsa dizi olarak ayarla
    if (!is_array($likedMovies)) {
        $likedMovies = [];
    }

    // Aynı film zaten eklenmiş mi kontrol et
    $exists = false;
    foreach ($likedMovies as $m) {
        if ($m['id'] == $movie['id']) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $likedMovies[] = $movie;
        $updatedJson = json_encode($likedMovies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $safeJson = mysqli_real_escape_string($conn, $updatedJson);

        $updateSql = "UPDATE anatable SET liked_movies = '$safeJson' WHERE client_id = '$userId'";
        if ($conn->query($updateSql)) {
            echo "Eklendi";
        } else {
            echo "Hata: " . $conn->error;
        }
    } else {
        echo "Zaten ekli";
    }
} else {
    $movies = [$movie];
    $jsonMovies = json_encode($movies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $safeJson = mysqli_real_escape_string($conn, $jsonMovies);

    $insertSql = "INSERT INTO anatable (client_id, liked_movies) VALUES ('$userId', '$safeJson')";
    if ($conn->query($insertSql)) {
        echo "Yeni kayıt oluşturuldu ve eklendi.";
    } else {
        echo "Hata: " . $conn->error;
    }
}
?>
