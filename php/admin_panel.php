<?php
session_start();
include('db.php');


// Kullanıcı giriş yapmış mı?
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit();
}

// Sadece adminler erişebilir
if (!isset($_SESSION['authority_id']) || $_SESSION['authority_id'] != 1) {
    echo "Bu sayfaya yalnızca adminler erişebilir.";
    exit();
}

// Arama filtresi
$searchName = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT id, name, surname, birth_date, email, authority_id FROM clients";
if (!empty($searchName)) {
    $sql .= " WHERE name LIKE '%$searchName%'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<?php
    $navbarTitle = "Panel";
    $navbarItems = ['home', 'dashboard', 'profile', 'edit_profile', 'logout'];
    include 'navbar.php';
    ?>
<div class="container mt-5">
    <h1 class="mb-4">Admin Paneli</h1>

    <!-- Arama Formu -->
    <form method="GET" action="admin_panel.php" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Ada göre ara..." value="<?= htmlspecialchars($searchName) ?>">
        <button type="submit" class="btn btn-primary">Ara</button>
        <a href="admin_panel.php" class="btn btn-secondary">Temizle</a>
    </form>

    <div class="table-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>Doğum Tarihi</th>
                    <th>Email</th>
                    <th>Yetki</th>
                    <th>Yetki Güncelle</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['surname']) ?></td>
                    <td><?= htmlspecialchars($row['birth_date']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <?php
                        switch ($row['authority_id']) {
                            case 1: echo "Admin"; break;
                            case 2: echo "Mod"; break;
                            case 3: echo "Client"; break;
                            default: echo "Bilinmiyor"; break;
                        }
                        ?>
                    </td>
                    <td>
                        <form action="admin_actions.php" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <select name="new_authority" class="form-select form-select-sm w-auto">
                                <option value="1" <?= $row['authority_id'] == 1 ? 'selected' : '' ?>>Admin</option>
                                <option value="2" <?= $row['authority_id'] == 2 ? 'selected' : '' ?>>Mod</option>
                                <option value="3" <?= $row['authority_id'] == 3 ? 'selected' : '' ?>>Client</option>
                            </select>
                            <button type="submit" name="change_authority" class="btn btn-sm btn-primary">Kaydet</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
