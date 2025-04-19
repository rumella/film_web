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
$query = "SELECT email, password FROM clients WHERE id = ?";
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

// Eğer form gönderildiyse, veritabanını güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Şifreyi kontrol et
    if ($new_password !== $confirm_password) {
        echo "Şifreler uyuşmuyor!";
    } else {
        // Şifreyi hash'leyerek güncelle
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $updateQuery = "UPDATE clients SET email = ?, password = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);

        if (!$updateStmt) {
            die('Sorgu hazırlama hatası: ' . $conn->error);
        }

        $updateStmt->bind_param("ssi", $new_email, $hashed_password, $user_id);

        if ($updateStmt->execute()) {
            echo "Profil başarıyla güncellendi!";
            header("Location: logout.php");
        } else {
            echo "Hata oluştu: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili Düzenle</title>
    <link rel="stylesheet" href="../css/edit_profile.css">
</head>

<body style="margin: 0; padding: 0;">
    <?php
    $navbarTitle = "Profili Düzenle";
    $navbarItems = ['home', 'dashboard', 'profile', 'logout'];
    include 'navbar.php';
    ?>

    <div class="edit-profile-container">
        <h1>Profili Düzenle</h1>
        <form method="POST" action="edit_profile.php">
            <div class="form-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Yeni Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Şifreyi Onayla:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Profili Güncelle</button>
        </form>
    </div>

</body>

</html>