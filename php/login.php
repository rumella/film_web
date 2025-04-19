<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = trim($_POST['input']);
    $password = $_POST['password'];

    // SQL enjeksiyon riskine karşı prepared statement kullan
    $sql = "SELECT * FROM clients WHERE email = ? OR name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Giriş başarılı, session'a her bilgiyi güvenli şekilde ata
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['name'];
            $_SESSION['last_name'] = $user['surname'];
            $_SESSION['authority_id'] = $user['authority_id'];

            header("Location: ../index.php");
            exit;
        } else {
            echo "⚠️ Yanlış şifre";
        }
    } else {
        echo "⚠️ Kullanıcı bulunamadı";
    }

    $stmt->close();
    $conn->close();
}
?>
