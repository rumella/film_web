<?php
session_start();
include('db.php');

// Kullanıcı giriş yaptı mı kontrol et
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');  // Eğer giriş yapmamışsa, login sayfasına yönlendir
    exit();
}

$user_id = $_SESSION['user_id'];

// Veritabanından kullanıcının profil bilgilerini çek
$query = "SELECT name, surname, birth_date, gender, email, authority_id FROM clients WHERE id = ?";
$stmt = $conn->prepare($query);

// Veritabanı hatası varsa, hata mesajını göster
if (!$stmt) {
    die('Sorgu hazırlama hatası: ' . $conn->error);
}

// Parametreyi bağla ve sorguyu çalıştır
$stmt->bind_param("i", $user_id); // "i" integer türü için
$stmt->execute();
$result = $stmt->get_result();

// Eğer kullanıcı verisi yoksa
if ($result->num_rows === 0) {
    die('Kullanıcı bulunamadı!');
}

$user = $result->fetch_assoc();

// Yetki bilgisi
$authority = '';
switch ($user['authority_id']) {
    case 1:
        $authority = 'Admin';
        break;
    case 2:
        $authority = 'Personel';
        break;
    case 3:
        $authority = 'Müşteri';
        break;
    default:
        $authority = 'Bilinmiyor';
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Profili</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body style="margin: 0; padding: 0;">
    <?php
    $navbarTitle = "Profilim";
    $navbarItems = ['home', 'dashboard', 'edit_profile', 'logout'];
    include 'navbar.php';
    ?>


    <div class="profile-container">
        <h1>Profilim</h1>
        <div class="profile-details">
            <h2><?php echo htmlspecialchars($user['name']) . ' ' . htmlspecialchars($user['surname']); ?></h2>
            <p><strong>E-posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Doğum Tarihi:</strong> <?php echo htmlspecialchars($user['birth_date']); ?></p>
            <p><strong>Cinsiyet:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
            <p><strong>Yetki:</strong> <?php echo $authority; ?></p>
        </div>
        <a href="edit_profile.php" class="btn">Profili Düzenle</a>
    </div>

</body>

</html>