<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT name, surname, email, profile_photo, password FROM clients WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) die("Kullanıcı bulunamadı!");

$toast = [];

//fotoğraf silme
if (isset($_POST['delete_photo'])) {
    if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])) {
        unlink("../uploads/" . $user['profile_photo']);

        $update = $conn->prepare("UPDATE clients SET profile_photo = '' WHERE id = ?");
        $update->bind_param("i", $user_id);
        $update->execute();

        $_SESSION['toast'] = ["Fotoğraf silindi.", "success"];
    } else {
        $_SESSION['toast'] = ["Fotoğrafınız zaten silinmiş.", "error"];
    }

    header("Location: edit_profile.php");
    exit();
}

// FOTOĞRAF YÜKLEME (önce eskiyi sil, sonra sadece jpeg/png kabul et)
if (isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['profile_photo']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            $_SESSION['toast'] = ["Sadece JPEG ve PNG formatındaki resimler yüklenebilir!", "error"];
        } else {
            // Eskiyi sil
            if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])) {
                unlink("../uploads/" . $user['profile_photo']);
            }

            $photo_tmp = $_FILES['profile_photo']['tmp_name'];
            $photo_name = uniqid() . "_" . $_FILES['profile_photo']['name'];
            move_uploaded_file($photo_tmp, "../uploads/" . $photo_name);

            $update = $conn->prepare("UPDATE clients SET profile_photo = ? WHERE id = ?");
            $update->bind_param("si", $photo_name, $user_id);
            $update->execute();

            $_SESSION['toast'] = ["Fotoğraf başarıyla güncellendi.", "success"];
        }
    } else {
        $_SESSION['toast'] = ["Dosya Seçmediniz!", "error"];
    }

    header("Location: edit_profile.php");
    exit();
}
    //Bilgi Güncelleme
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $email = trim($_POST['email']);
    
        $fields = [];
        $params = [];
        $types = "";
    
        $updates = [];
    
        // Mevcut değerlerle karşılaştır
        if (!empty($name) && $name !== $user['name']) {
            $fields[] = "name = ?";
            $params[] = $name;
            $types .= "s";
        } elseif (!empty($name) && $name === $user['name']) {
            $_SESSION['toast'] = ["Yeni ad mevcut ad ile aynı olamaz!", "error"];
            header("Location: edit_profile.php");
            exit();
        }
    
        if (!empty($surname) && $surname !== $user['surname']) {
            $fields[] = "surname = ?";
            $params[] = $surname;
            $types .= "s";
        } elseif (!empty($surname) && $surname === $user['surname']) {
            $_SESSION['toast'] = ["Yeni soyad mevcut soyad ile aynı olamaz!", "error"];
            header("Location: edit_profile.php");
            exit();
        }
    
        if (!empty($email) && $email !== $user['email']) {
            // E-posta başka kullanıcıda var mı kontrol et
            $check = $conn->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
            $check->bind_param("si", $email, $user_id);
            $check->execute();
            $check->store_result();
    
            if ($check->num_rows > 0) {
                $_SESSION['toast'] = ["Bu e-posta adresi zaten kullanılıyor!", "error"];
                header("Location: edit_profile.php");
                exit();
            }
    
            $fields[] = "email = ?";
            $params[] = $email;
            $types .= "s";
        } elseif (!empty($email) && $email === $user['email']) {
            $_SESSION['toast'] = ["Yeni e-posta mevcut e-posta ile aynı olamaz!", "error"];
            header("Location: edit_profile.php");
            exit();
        }
    
        if (!empty($fields)) {
            $query = "UPDATE clients SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $user_id;
            $types .= "i";
    
            $update = $conn->prepare($query);
            $update->bind_param($types, ...$params);
            $update->execute();
    
            $_SESSION['toast'] = ["Profil bilgileri güncellendi.", "success"];
        } else {
            $_SESSION['toast'] = ["Güncellenecek bir bilgi girilmedi.", "error"];
        }
    
        header("Location: edit_profile.php");
        exit();
    }

// ŞİFRE GÜNCELLEME
if (isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $_SESSION['toast'] = ["Mevcut şifre yanlış!", "error"];
    } elseif ($new !== $confirm) {
        $_SESSION['toast'] = ["Yeni şifreler uyuşmuyor!", "error"];
    } elseif (password_verify($new, $user['password'])) {
        $_SESSION['toast'] = ["Yeni şifreniz ile eski şifreniz aynı olamaz!", "error"];
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE clients SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $user_id);
        $update->execute();
        $_SESSION['toast'] = ["Şifre başarıyla güncellendi.", "success"];
    }

    header("Location: edit_profile.php");
    exit();
}


// HESAP SİLME
if (isset($_POST['delete_account'])) {
    $current_password = $_POST['current_password'] ?? '';

    if (empty($current_password)) {
        $_SESSION['toast'] = ["Lütfen mevcut şifrenizi girin.", "error"];
        header("Location: edit_profile.php");
        exit();
    }

    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['toast'] = ["Mevcut şifre yanlış!", "error"];
        header("Location: edit_profile.php");
        exit();
    }

    // Profil fotoğrafını da sil (varsa)
    if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])) {
        unlink("../uploads/" . $user['profile_photo']);
    }

    // Hesabı sil
    $delete = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $delete->bind_param("i", $user_id);

    if ($delete->execute()) {
        session_destroy();
        $_SESSION['toast'] = ["Hesabınız silindi.", "success"];
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['toast'] = ["Profil silinemedi!", "error"];
        header("Location: edit_profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profili Düzenle</title>
    <link rel="stylesheet" href="../css/edit_profile.css">
    <style>
    .toast {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: bold;
    color: white;
    box-shadow: 0 0 12px rgba(0,0,0,0.2);
    animation: fadeInOut 4s ease-in-out;
    max-width: 400px;
    width: auto;
    text-align: center;
    background-color: #333;
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

    .toast.success { background-color: #28a745; }
    .toast.error { background-color: #dc3545; }

    .toast .close-btn {
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        background: transparent;
        border: none;
        color: white;
        margin-left: 10px;
    }

</style>
</head>

<body>
    <?php
    $navbarTitle = "Profili Düzenle";
    $navbarItems = ['home', 'dashboard', 'profile', 'logout'];
    include 'navbar.php';
    ?>

<?php if (isset($_SESSION['toast'])): ?>
    <?php
        list($toast, $toast_type) = $_SESSION['toast'];
        unset($_SESSION['toast']);
    ?>
    <div class="toast <?php echo $toast_type; ?>">
        <?php echo htmlspecialchars($toast); ?>
        <span class="toast-close" onclick="this.parentElement.remove()">×</span>
    </div>
<?php endif; ?>
    <div class="edit-profile-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="left-column">
                <div class="photo-frame">
                    <?php if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])): ?>
                        <img id="profilePreview" src="../uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profil Fotoğrafı">
                    <?php else: ?>
                        <div class="empty-photo">Fotoğraf Yok</div>
                    <?php endif; ?>
                </div>
                <input type="file" name="profile_photo" accept="image/*" onchange="previewImage(event)">
                <button type="submit" name="upload_photo" class="btn">Fotoğrafı Kaydet</button>
                <button type="submit" name="delete_photo" class="btn btn-danger">Fotoğrafı Sil</button>
            </div>

            <div class="middle-column">
                <label>Ad:</label>
                <input type="text" name="name" placeholder="Adınızı girin">

                <label>Soyad:</label>
                <input type="text" name="surname" placeholder="Soyadınızı girin">

                <label>E-posta:</label>
                <input type="email" name="email" placeholder="E-posta adresinizi girin">

                <button type="submit" name="update_profile" class="btn">Profili Güncelle</button>
            </div>

            <div class="right-column">
                <div class="password-field">
                    <input type="password" name="current_password" id="current_password" placeholder="Mevcut Şifre">
                    <span onclick="togglePassword('current_password', this)" class="toggle-eye">👁️</span>
                </div>
                <div class="password-field">
                    <input type="password" name="password" id="password" placeholder="Yeni Şifre">
                    <span onclick="togglePassword('password', this)" class="toggle-eye">👁️</span>
                </div>
                <div class="password-field">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Yeni Şifre (Tekrar)">
                    <span onclick="togglePassword('confirm_password', this)" class="toggle-eye">👁️</span>
                </div>
                <button type="submit" name="update_password" class="btn">Şifreyi Güncelle</button>

                <button type="submit" name="delete_account" class="btn btn-danger" onclick="return confirm('Hesabınızı silmek istediğinize emin misiniz?');">Profili Sil</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const img = document.getElementById('profilePreview');
                if (img) img.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function togglePassword(id, el) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                el.textContent = "🙈";
            } else {
                input.type = "password";
                el.textContent = "👁️";
            }
        }
    </script>
</body>
</html>