<?php
// db.php dosyasını dahil et
include('db.php');

// Kayıt işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verileri al
    $name = $_POST['first_name'];
    $surname = $_POST['last_name'];
    $birth_date = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // SQL sorgusuyla kullanıcıyı ekle
    $sql = "INSERT INTO clients (name, surname, birth_date, gender, email, password)
            VALUES ('$name', '$surname', '$birth_date', '$gender', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Yeni kayıt başarılı";
        header('Location: ../html/login.html');
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
}
?>