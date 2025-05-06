<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['authority_id'] != '1') {
    die("Yetkiniz yok.");
}

$commentId = $_POST['comment_id'] ?? null;

if ($commentId) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $stmt->close();
}

header("Location: show_comment_page.php");
exit;
