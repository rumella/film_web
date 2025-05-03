<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}

include('db.php');
$userId = $_SESSION['user_id'];

// Film ID'si AJAX ile gönderildi
if (isset($_POST['movie_id'])) {
    $movieId = $_POST['movie_id'];

    // Kullanıcıya ait film verilerini al
    $sql = "SELECT liked_movies FROM anatable WHERE client_id = '$userId'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $movies = json_decode($row['liked_movies'], true);
    
        // Eğer liked_movies geçerli bir array değilse, hata döndür
        if (!is_array($movies)) {
            echo json_encode(['success' => false, 'message' => 'Liked movies verisi hatalı.']);
            exit();
        }
    
        // Silme işlemi
        $movieFound = false;
        foreach ($movies as $key => $movie) {
            if ($movie['id'] == $movieId) {
                unset($movies[$key]);
                $movieFound = true;
                break;
            }
        }
    
        if (!$movieFound) {
            echo json_encode(['success' => false, 'message' => 'Film bulunamadı']);
            exit();
        }
    
        // Listeyi tekrar JSON formatında düzenle
        $updatedMovies = json_encode(array_values($movies));
        
        // SQL sorgusunu çalıştır
        $updateSql = "UPDATE anatable SET liked_movies = ? WHERE client_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $updatedMovies, $userId);
    
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
                error_log('Film başarıyla silindi.');
            } else {
                echo json_encode(['success' => false, 'message' => 'Veritabanında değişiklik yapılmadı.']);
                error_log('Veritabanında değişiklik yapılmadı. Belki film zaten silinmiş olabilir.');
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı güncellenemedi']);
            error_log('Veritabanı güncellenemedi: ' . $stmt->error);
        }
    
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Film verileri bulunamadı']);
    }
    
} else {
    echo json_encode(['success' => false]);
}
?>
