<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT name, surname, birth_date, gender, email, authority_id, profile_photo FROM clients WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Sorgu hatası: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('Kullanıcı bulunamadı!');
}
$user = $result->fetch_assoc();

$authority = match ($user['authority_id']) {
    1 => 'Admin',
    2 => 'Personel',
    3 => 'Müşteri',
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
            background-color: #f4f6f8;
        }

        .profile-wrapper {
            max-width: 1000px;
            margin: 20px auto;
            padding: 40px;
            background-color: #ffffff;
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
            background-color:rgb(255, 255, 255);
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
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .name-card {
            margin-top: 20px;
            background-color: #f2f4f7;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            font-size: 18px;
            font-weight: 600;
            color: #333;
            border-left: 4px solid #007BFF; 
            padding-left: 20px;
            cursor: default;
        }

        .name-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .right-column {
            flex: 1;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background-color: #f2f4f7;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.3s;
            border-left: 4px solid #007BFF;
            padding-left: 20px;
            cursor: default;
        }

        .info-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 4px;
        }

        .info-value {
            color: #222;
            font-size: 17px;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                align-items: center;
            }

            .left-column {
                border-right: none;
                border-bottom: 2px solid #e0e0e0;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }

            .right-column {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>

<?php
$navbarTitle = "Profilim";
$navbarItems = ['home', 'dashboard', 'edit_profile', 'logout'];
include 'navbar.php';
?>

<div class="profile-wrapper">
    <div class="profile-container">
        <div class="left-column">
            <img class="profile-photo"
                 src="<?php echo !empty($user['profile_photo']) && file_exists('../uploads/' . $user['profile_photo']) ? '../uploads/' . htmlspecialchars($user['profile_photo']) : '../assets/default_profile.png'; ?>"
                 alt="Profil Fotoğrafı">

            <div class="name-card">
                <?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?>
            </div>
        </div>

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
