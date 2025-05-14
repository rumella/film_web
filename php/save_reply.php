<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    $message = "Giriş yapmanız gerekiyor.";
    $success = false;
} else {
    $commentId = intval($_POST['comment_id'] ?? 0);
    $replyText = trim($_POST['reply_text'] ?? '');
    $clientId = $_SESSION['user_id'];

    if (!$commentId || $replyText === '') {
        $message = "Yanıt boş olamaz.";
        $success = false;
    } else {
        // Kullanıcının o yoruma daha önce kaç yanıt verdiğini kontrol et
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM replies WHERE client_id = ? AND comment_id = ?");
        $checkStmt->bind_param("ii", $clientId, $commentId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $count = $checkResult->fetch_assoc()['count'];

        if ($count >= 5) {
            $message = "Her yoruma en fazla 5 yanıt verebilirsiniz.";
            $success = false;
        } else {
            // Yanıtı kaydet
            $stmt = $conn->prepare("INSERT INTO replies (comment_id, client_id, reply) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $commentId, $clientId, $replyText);
            if ($stmt->execute()) {
                $message = "Cevap başarıyla eklendi!";
                $success = true;
            } else {
                $message = "Bir hata oluştu. Cevap eklenemedi.";
                $success = false;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Yanıt Durumu</title>
    <meta http-equiv="refresh" content="3;url=show_comment_page.php">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
            overflow: hidden;
            /* Sayfa kaydırmayı engelle */
        }

        .message-box {
            padding: 30px;
            background-color: <?php echo $success ? '#d4edda' : '#f8d7da'; ?>;
            color: <?php echo $success ? '#155724' : '#721c24'; ?>;
            border: 1px solid <?php echo $success ? '#c3e6cb' : '#f5c6cb'; ?>;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .message-box h3 {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="message-box">
        <h3><?php echo htmlspecialchars($message); ?></h3>
        <p>3 saniye içinde yorumlar sayfasına yönlendiriliyorsunuz...</p>
    </div>
</body>

</html>