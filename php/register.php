<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['first_name']);
    $surname = trim($_POST['last_name']);
    $birth_date = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ E-posta geçerli mi kontrol et
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Lütfen geçerli bir e-posta adresi girin!');
            window.location.href = '../html/register.html';
        </script>";
        exit();
    }

    // 📛 E-posta zaten kayıtlı mı?
    $check = $conn->prepare("SELECT id FROM clients WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>
            alert('Bu e-posta adresi zaten kayıtlı!');
            window.location.href = '../html/register.html';
        </script>";
        exit();
    }

    // ✅ Veritabanına ekle
    $insert = $conn->prepare("INSERT INTO clients (name, surname, birth_date, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("ssssss", $name, $surname, $birth_date, $gender, $email, $password);

    if ($insert->execute()) {
        header('Location: ../html/login.html');
        exit();
    } else {
        echo "<script>
            alert('Kayıt sırasında bir hata oluştu: " . $conn->error . "');
            window.location.href = '../html/register.html';
        </script>";
    }
}
?>
