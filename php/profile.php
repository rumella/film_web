<?php
session_start();
include('db.php');

// Kullanıcı oturumunun kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini veritabanından al
$query = "SELECT name, surname, birth_date, gender, email, authority_id, profile_photo FROM clients WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die('Sorgu hatası: ' . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Kullanıcı bilgileri bulunamazsa hata mesajı göster
if ($result->num_rows === 0) {
    die('Kullanıcı bulunamadı!');
}

$user = $result->fetch_assoc();

// Yetki bilgisi için açıklama
$authority = match ($user['authority_id']) {
    1 => 'Admin',
    2 => 'Personel',
    3 => 'Kullanıcı',
    default => 'Bilinmiyor'
};
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: black;
            color: whitesmoke;
        }

        .profile-wrapper {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #1c1c1c;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .profile-container {
            display: flex;
            gap: 60px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .left-column {
            flex: 0 0 260px;
            text-align: center;
            padding: 30px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0);
        }

        .profile-photo {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ccc;
        }

        .profile-photo:hover {
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.5);
        }

        .name-card {
            margin-top: 20px;
            background-color: #2b2b2b;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            font-size: 18px;
            font-weight: 600;
            border-left: 4px solid #007BFF; 
            padding-left: 20px;
            cursor: default;
        }

        .name-card:hover {
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.5);
        }

        .right-column {
            flex: 1;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background-color: #2b2b2b;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.3s;
            border-left: 4px solid #007BFF;
            padding-left: 20px;
            cursor: default;
        }

        .info-card:hover {
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.5);
        }

        .info-label {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 17px;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                align-items: center;
                gap: 30px; /* Aralıklar arttırılabilir */
            }

            .left-column,
            .right-column {
                flex: none;
                width: 100%;
                text-align: center;
            }

            .left-column {
                margin-bottom: 20px;
            }

            .right-column {
                padding: 0 10px;
            }

            .profile-photo {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>

<?php
// Navbar'ı dahil et
$navbarTitle = "Profilim";
$navbarItems = ['home', 'dashboard', 'add_comment', 'edit_profile', 'logout'];
include 'navbar.php';
?>

<div class="profile-wrapper">
    <div class="profile-container">
        <!-- Sol Kol: Profil Fotoğrafı ve İsim -->
        <div class="left-column">
            <img class="profile-photo"
                 src="<?php echo !empty($user['profile_photo']) && file_exists('../uploads/' . $user['profile_photo']) ? '../uploads/' . htmlspecialchars($user['profile_photo']) : '../assets/default_profile.png'; ?>"
                 alt="Profil Fotoğrafı">

            <div class="name-card">
                <?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?>
            </div>
        </div>

        <!-- Sağ Kol: Kullanıcı Bilgileri -->
        <div class="right-column">
            <div class="info-card">
                <div class="info-label">E-posta</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">Doğum Tarihi</div>
                <div class="info-value"><?php echo htmlspecialchars($user['birth_date']); ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">Cinsiyet</div>
                <div class="info-value"><?php echo htmlspecialchars($user['gender']); ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">Yetki</div>
                <div class="info-value"><?php echo $authority; ?></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
