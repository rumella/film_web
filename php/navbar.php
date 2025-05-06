<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$navbarTitle = $navbarTitle ?? "Film Uygulaması";
$navbarItems = $navbarItems ?? [];
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

    .navbar-title h3 {
        cursor: default;
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

        .navbar .navbar-center {
            flex-grow: 1;
            text-align: center;
        }

        .dropdown-toggle {
            display: block;
        }
    }
</style>

<nav class="navbar">
    <div class="navbar-title">
        <h3 style="margin:0;"><?= htmlspecialchars($navbarTitle) ?></h3>
    </div>


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
    $currentPage = basename($_SERVER['PHP_SELF']); // Şu anki sayfa
    $prefix = $isFromRoot ? 'php/' : '';
    $showPanel = isset($_SESSION['authority_id']) && $_SESSION['authority_id'] == 1 && $currentPage !== 'admin_panel.php';
    ?>


    <ul class="navbar-left">
        <?php if ($showPanel): ?>
            <li><a href="<?= $prefix ?>admin_panel.php">Panel</a></li>
        <?php endif; ?>
        <?php if (in_array('home', $navbarItems)): ?>
            <li><a href="<?= $isFromRoot ? 'index.php' : '../index.php' ?>">Ana Sayfa</a></li>
        <?php endif; ?>
        <?php if (in_array('dashboard', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>dashboard.php">Dashboard</a></li>
        <?php endif; ?>
        <?php if (in_array('add_comment', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>show_comment_page.php">Yorumlar</a></li>
        <?php endif; ?>
        <?php if (in_array('profile', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>profile.php">Profil</a></li>
        <?php endif; ?>
        <?php if (in_array('edit_profile', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>edit_profile.php">Profili Düzenle</a></li>
        <?php endif; ?>
        <?php if (in_array('logout', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>logout.php">Çıkış</a></li>
        <?php endif; ?>
        <?php if (in_array('login', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>../html/login.html">Giriş Yap</a></li>
        <?php endif; ?>
        <?php if (in_array('register', $navbarItems)): ?>
            <li><a href="<?= $prefix ?>../html/register.html">Üye Ol</a></li>
        <?php endif; ?>
    </ul>

    <!-- Mobil Dropdown Menü -->
    <button class="dropdown-toggle" id="dropdownToggle">⋮</button>
    <div class="dropdown-menu" id="dropdownMenu">
        <?php if ($showPanel): ?>
            <a href="<?= $prefix ?>admin_panel.php">Panel</a>
        <?php endif; ?>
        <?php if (in_array('home', $navbarItems)): ?>
            <a href="<?= $isFromRoot ? 'index.php' : '../index.php' ?>">Ana Sayfa</a>
        <?php endif; ?>
        <?php if (in_array('dashboard', $navbarItems)): ?>
            <a href="<?= $prefix ?>dashboard.php">Dashboard</a>
        <?php endif; ?>
        <?php if (in_array('add_comment', $navbarItems)): ?>
            <a href="<?= $prefix ?>show_comment_page.php">Yorumlar</a>
        <?php endif; ?>
        <?php if (in_array('profile', $navbarItems)): ?>
            <a href="<?= $prefix ?>profile.php">Profil</a>
        <?php endif; ?>
        <?php if (in_array('edit_profile', $navbarItems)): ?>
            <a href="<?= $prefix ?>edit_profile.php">Profili Düzenle</a>
        <?php endif; ?>
        <?php if (in_array('logout', $navbarItems)): ?>
            <a href="<?= $prefix ?>logout.php">Çıkış</a>
        <?php endif; ?>
        <?php if (in_array('login', $navbarItems)): ?>
            <a href="<?= $prefix ?>../html/login.html">Giriş Yap</a>
        <?php endif; ?>
        <?php if (in_array('register', $navbarItems)): ?>
            <a href="<?= $prefix ?>../html/register.html">Üye Ol</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById("dropdownToggle");
        const dropdownMenu = document.getElementById("dropdownMenu");

        toggleBtn.addEventListener("click", function() {
            dropdownMenu.style.display = (dropdownMenu.style.display === "block") ? "none" : "block";
        });

        document.addEventListener("click", function(event) {
            if (!toggleBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = "none";
            }
        });
    });
</script>