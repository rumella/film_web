<?php
session_start();
include('db.php');

// Sadece adminler işlem yapabilir
if (!isset($_SESSION['user_id']) || $_SESSION['authority_id'] != 1) {
    echo "Yetkisiz erişim.";
    exit();
}

// Yetki güncelleme işlemi
if (isset($_POST['change_authority']) && isset($_POST['user_id']) && isset($_POST['new_authority'])) {
    $userId = intval($_POST['user_id']);
    $newAuth = intval($_POST['new_authority']);

    // Admin kendi yetkisini değiştiremesin
    if ($_SESSION['user_id'] == $userId) {
        echo "Kendi yetkinizi değiştiremezsiniz.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE clients SET authority_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $newAuth, $userId);
    if ($stmt->execute()) {
        header("Location: admin_panel.php");
        exit();
    } else {
        echo "Yetki güncellenemedi.";
    }
}

// Kullanıcı silme işlemi
if (isset($_POST['delete_user']) && isset($_POST['delete_user_id'])) {
    $delete_id = intval($_POST['delete_user_id']);

    // Kendini silmeye çalışma
    if ($delete_id == $_SESSION['user_id']) {
        die("Kendinizi silemezsiniz.");
    }

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_panel.php");
    exit();
}
?>
