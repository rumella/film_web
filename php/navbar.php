<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$navbarTitle = $navbarTitle ?? "Film Uygulaması";
$navbarItems = $navbarItems ?? [];

// "Yorumlar" menüsü her zaman gösterilsin
if (!in_array('add_comment', $navbarItems)) {
    $navbarItems[] = 'add_comment';
}
?>

<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #222;
        padding: 0.5rem 1rem;
        color: white;
        flex-wrap: wrap;
        position: relative;
    }

    .navbar ul {
        display: flex;
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    .navbar li {
        margin: 0 0.5rem;
    }

    .navbar a,
    .navbar button {
        color: white;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .navbar input {
        padding: 0.3rem;
    }

    .dropdown-toggle {
        display: none;
        font-size: 1.5rem;
        background: none;
        border: none;
        color: white;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 1rem;
        background-color: #333;
        border: 1px solid #444;
        padding: 0.5rem;
        z-index: 100;
        min-width: 150px;
    }

    .dropdown-menu a {
        display: block;
        color: white;
        padding: 0.3rem 0;
    }

    @media (max-width: 768px) {
        .navbar ul:not(.navbar-center) {
            display: none;
        }

        .dropdown-toggle {
            display: block;
        }
    }
</style>

<nav class="navbar">
    <ul class="navbar-right">
        <li>
            <h3 style="margin:0; cursor: default;"><?= htmlspecialchars($navbarTitle) ?></h3>
        </li>
    </ul>

    <ul class="navbar-center">
        <?php if (in_array('search', $navbarItems)): ?>
            <li>
                <input type="text" id="movie_name" placeholder="Film Ara..." />
                <button id="search_movie">Ara</button>
            </li>
        <?php endif; ?>
    </ul>

    <?php
    $isFromRoot = basename($_SERVER['PHP_SELF']) === 'index.php';
    $prefix = $isFromRoot ? 'php/' : '';
    ?>

<ul class="navbar-left">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Giriş yapmamış kullanıcılar -->
        <li><a href="<?= $isFromRoot ? 'index.php' : '../index.php' ?>">Ana Sayfa</a></li>
        <li><a href="<?= $isFromRoot ? 'html/login.html' : '../html/login.html' ?>">Giriş Yap</a></li>
        <li><a href="<?= $isFromRoot ? 'html/register.html' : '../html/register.html' ?>">Üye Ol</a></li>
    <?php else: ?>
        <!-- Giriş yapmış kullanıcılar -->
        <?php if (isset($_SESSION['authority_id']) && $_SESSION['authority_id'] == 1): ?>
            <li><a href="<?= $prefix ?>admin_panel.php">Panel</a></li>
        <?php endif; ?>
        <?php if (in_array('home', $navbarItems)): ?>
            <li><a href="<?= $isFromRoot ? 'index.php' : '../index.php' ?>">Ana Sayfa</a></li>
        <?php endif; ?>
        <?php if (in_array('dashboard', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>dashboard.php">Film Ekle</a></li>
        <?php endif; ?>
        <?php if (in_array('profile', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>profile.php">Profil</a></li>
        <?php endif; ?>
        <?php if (in_array('edit_profile', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>edit_profile.php">Profili Düzenle</a></li>
        <?php endif; ?>
        <?php if (in_array('add_comment', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>show_comment_page.php">Yorumlar</a></li>
        <?php endif; ?>
        <?php if (in_array('logout', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>logout.php">Çıkış</a></li>
        <?php endif; ?>
    <?php endif; ?>
</ul>

    <!-- Mobil Dropdown Menü -->
    <button class="dropdown-toggle" id="dropdownToggle">⋮</button>
    <div class="dropdown-menu" id="dropdownMenu">
        <?php if (in_array('home', $navbarItems)): ?>
            <a href="<?= $isFromRoot ? 'index.php' : '../index.php' ?>">Ana Sayfa</a>
        <?php endif; ?>
        <?php if (in_array('dashboard', $navbarItems)): ?>
            <a href="<?= $prefix ?>dashboard.php">Film Ekle</a>
        <?php endif; ?>
        <?php if (in_array('profile', $navbarItems)): ?>
            <a href="<?= $prefix ?>profile.php">Profil</a>
        <?php endif; ?>
        <?php if (in_array('edit_profile', $navbarItems)): ?>
            <a href="<?= $prefix ?>edit_profile.php">Profili Düzenle</a>
        <?php endif; ?>
        <?php if (in_array('add_comment', $navbarItems)): ?>
            <a href="<?= $prefix ?>show_comment_page.php">Yorumlar</a>
        <?php endif; ?>
        <?php if (in_array('logout', $navbarItems)): ?>
            <a href="<?= $prefix ?>logout.php">Çıkış</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    const toggle = document.getElementById('dropdownToggle');
    const menu = document.getElementById('dropdownMenu');

    toggle.addEventListener('click', () => {
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
</script>
