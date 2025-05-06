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

// FOTOĞRAF SİLME
if (isset($_POST['delete_photo'])) {
    if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])) {
        unlink("../uploads/" . $user['profile_photo']);
        $update = $conn->prepare("UPDATE clients SET profile_photo = '' WHERE id = ?");
        $update->bind_param("i", $user_id);
        $update->execute();
        $_SESSION['toast'] = ["Fotoğraf silindi.", "success"];
    } else {
        $_SESSION['toast'] = ["Fotoğrafınız zaten silinmiş.", "danger"];
    }
    header("Location: edit_profile.php");
    exit();
}

// FOTOĞRAF YÜKLEME
if (isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['profile_photo']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            $_SESSION['toast'] = ["Sadece JPEG ve PNG formatındaki resimler yüklenebilir!", "danger"];
        } else {
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
        $_SESSION['toast'] = ["Dosya seçmediniz!", "danger"];
    }

    header("Location: edit_profile.php");
    exit();
}

// PROFİL BİLGİ GÜNCELLEME
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);

    $fields = [];
    $params = [];
    $types = "";

    if (!empty($name) && $name !== $user['name']) {
        $fields[] = "name = ?";
        $params[] = $name;
        $types .= "s";
    } elseif (!empty($name) && $name === $user['name']) {
        $_SESSION['toast'] = ["Yeni ad mevcut ad ile aynı olamaz!", "danger"];
        header("Location: edit_profile.php");
        exit();
    }

    if (!empty($surname) && $surname !== $user['surname']) {
        $fields[] = "surname = ?";
        $params[] = $surname;
        $types .= "s";
    } elseif (!empty($surname) && $surname === $user['surname']) {
        $_SESSION['toast'] = ["Yeni soyad mevcut soyad ile aynı olamaz!", "danger"];
        header("Location: edit_profile.php");
        exit();
    }

    if (!empty($email) && $email !== $user['email']) {
        $check = $conn->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $user_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['toast'] = ["Bu e-posta adresi zaten kullanılıyor!", "danger"];
            header("Location: edit_profile.php");
            exit();
        }

        $fields[] = "email = ?";
        $params[] = $email;
        $types .= "s";
    } elseif (!empty($email) && $email === $user['email']) {
        $_SESSION['toast'] = ["Yeni e-posta mevcut e-posta ile aynı olamaz!", "danger"];
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
        $_SESSION['toast'] = ["Güncellenecek bir bilgi girilmedi.", "danger"];
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
        $_SESSION['toast'] = ["Mevcut şifre yanlış!", "danger"];
    } elseif ($new !== $confirm) {
        $_SESSION['toast'] = ["Yeni şifreler uyuşmuyor!", "danger"];
    } elseif (password_verify($new, $user['password'])) {
        $_SESSION['toast'] = ["Yeni şifreniz ile eski şifreniz aynı olamaz!", "danger"];
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
        $_SESSION['toast'] = ["Lütfen mevcut şifrenizi girin.", "danger"];
        header("Location: edit_profile.php");
        exit();
    }

    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['toast'] = ["Mevcut şifre yanlış!", "danger"];
        header("Location: edit_profile.php");
        exit();
    }

    if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])) {
        unlink("../uploads/" . $user['profile_photo']);
    }

    $delete = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $delete->bind_param("i", $user_id);

    if ($delete->execute()) {
        session_destroy();
        $_SESSION['toast'] = ["Hesabınız silindi.", "success"];
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['toast'] = ["Profil silinemedi!", "danger"];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/edit_profile.css">
</head>

<body>
    <?php
    $navbarTitle = "Profili Düzenle";
    $navbarItems = ['home', 'dashboard', 'add_comment', 'profile', 'logout'];
    include 'navbar.php';
    ?>

<?php if (isset($_SESSION['toast'])): ?>
    <?php
        list($toast_message, $toast_type) = $_SESSION['toast'];
        unset($_SESSION['toast']);

        $valid_types = ['success', 'danger', 'warning', 'info', 'primary', 'secondary', 'dark', 'light'];
        if (!in_array($toast_type, $valid_types)) {
            $toast_type = 'primary';
        }
    ?>
    <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1055; width: 30%;">
        <div id="liveToast" class="toast show w-100 text-white bg-<?php echo $toast_type; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex justify-content-between align-items-center">
                <div class="toast-body">
                    <?php echo htmlspecialchars($toast_message); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
            </div>
        </div>
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
            reader.onload = function() {
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

        document.addEventListener('DOMContentLoaded', function() {
            const toastEl = document.getElementById('liveToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 4000
                });
                toast.show();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>