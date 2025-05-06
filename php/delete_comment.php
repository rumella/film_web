<?php
session_start();
require 'db.php';

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['authority_id'] ?? null;

$commentId = $_POST['comment_id'] ?? null;

if (!$userId || !$commentId) {
    die("Geçersiz istek.");
}

// Yorumu veritabanından çek
$stmt = $conn->prepare("SELECT client_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $commentId);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();
$stmt->close();

if (!$comment) {
    die("Yorum bulunamadı.");
}

// Yalnızca admin veya yorumun sahibi silebilir
if ($userRole == '1' || $comment['client_id'] == $userId) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $stmt->close();

    header("Location: show_comment_page.php");
    exit;
} else {
    die("Bu yorumu silmeye yetkiniz yok.");
}
