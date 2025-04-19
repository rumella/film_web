<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}

$api_key = file_get_contents('../api_key.txt');
if ($api_key === false) {
    die("API anahtarı okunamadı.");
}

include('db.php');
$userId = $_SESSION['user_id'];

// Kullanıcıya ait film verilerini al
$sql = "SELECT liked_movies FROM anatable WHERE client_id = '$userId'";
$result = $conn->query($sql);

$movies = [];
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $movies = json_decode($row['liked_movies'], true);
    
    // Şimdi JSON'daki filmleri alfabetik olarak sıralayalım (title'ı baz alarak)
    usort($movies, function($a, $b) {
        return strcmp($a['title'], $b['title']); // 'title' alanına göre alfabetik sıralama
    });
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- PHP'den JS'ye veri geçirme -->
    <script>
        const apiKey = "<?php echo htmlspecialchars($api_key); ?>";
        const moviesFromPHP = <?php echo json_encode($movies); ?>;
    </script>

    <script src="../dashboard.js"></script>
</head>

<body>
    <!-- Navbar -->
    <?php
    $navbarTitle = "Film Ekle";
    $navbarItems = ['search', 'home', 'profile', 'edit_profile', 'logout'];
    include 'navbar.php';
    ?>

    <!-- Film Sonuçları -->
    <div id="movie_results" class="movie-grid"></div>
    <div id="watched_movies"></div>
</body>

</html>
