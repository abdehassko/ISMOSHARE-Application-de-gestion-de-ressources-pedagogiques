<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location: /ISMOSHARE/pages/principale/page principale.php");
    exit();
}
else {
    include("code-conexionAvecDB.php");
    extract($_SESSION);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISMOSHARE Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/non-auto_style.css" />
    <link rel="icon" href="/ISMOSHARE/assets/logoapp.png" />
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <a href="/ISMOSHARE/pages/principale/page principale.php"><img src="/ISMOSHARE/assets/logo.png" /></a>
            </div>

            <div class="profile">
                <img src="<?= $photo ?>" />
                <div class="profile-info">
                    <p class="profile-name"><?=$nom?> <?=$prenom?></p>
                    <p class="profile-role">
                        <span class="online-dot"></span><?=$role?>
                    </p>
                </div>
            </div>

            <nav class="menu">
                <a href="/ISMOSHARE/pages/forum/forum.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/icons8-forum-20.png" />
                    <p>&nbsp;&nbsp;&nbsp; Forum</p>
                </a>
                <a href="/ISMOSHARE/pages/annonce/annonce.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/icons8-annonce-20.png" />&nbsp;
                    <p>Annonces</p>
                </a>
                <a href="/ISMOSHARE/pages/ressource/ressources.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/Vector-2.png" />&nbsp;
                    <p>Ressources</p>
                </a>
                <a href="/ISMOSHARE/others/non-autorise.php" class="menu-item active">
                    <img src="/ISMOSHARE/assets/bx_message-detail.png" />&nbsp;
                    <p>Chat</p>
                    <span class="notification-badge">soon</span>
                </a>
                <?php
                if ($role=="admin") {
                    echo"<a href='/ISMOSHARE/pages/utilisateurs/utilisateurs.php' class='menu-item'>
                    <img src='/ISMOSHARE/assets/Vector11.png' />&nbsp;
                    <p>Utilisateurs</p>
                    </a>";
                }
                if ($role=="admin" || $role=="formateur") {
                    echo"<a href='/ISMOSHARE/pages/validation/validation.php' class='menu-item'>
                    <img src='/ISMOSHARE/assets/carbon_settings.png' />&nbsp;
                    <p>Validation</p>
                    </a>";
                }
                ?>
                <a href="/ISMOSHARE/others/logout.php" class="menu-item sign-out">
                    <img src="/ISMOSHARE/assets/Group 340.png" />&nbsp;
                    <p>Sign Out</p>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <div class="top-bar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-bar" placeholder="Search Class, Documents, Activities..." />
                </div>
                <div class="user-menu">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">1</span>
                    </div>
                    <a href="/ISMOSHARE/pages/profile/profile.php"><img src="<?= $photo ?>" /></a>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="errocard">
                <div class="containererr">
                    <div class="error-text">
                        <h1 class="error-title">Oops....</h1>
                        <h2 class="error-subtitle">Page not found</h2>
                        <p class="error-message">
                            You are not authorised to access to this page ! We suggest you
                            back to home.
                        </p>
                        <a href="/ISMOSHARE/pages/principale/page principale.php" class="back-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M19 12H5M12 19l-7-7 7-7" />
                            </svg>
                            Back to home
                        </a>
                    </div>

                    <div class="error-illustration">
                        <img src="/ISMOSHARE/assets/Capture.JPG" alt="" srcset="" />
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="notification-dropdown" style="display: none">
        <h4>Notifications</h4>
        <ul class="notification-list">
            <li class="notification-item">
                <p><strong>New Message:</strong> You have a new message from John.</p>
                <span class="notification-time">2 hours ago</span>
            </li>
            <li class="notification-item">
                <p>
                    <strong>File Uploaded:</strong> "Project Plan.pdf" has been
                    uploaded.
                </p>
                <span class="notification-time">1 day ago</span>
            </li>
            <li class="notification-item">
                <p>
                    <strong>Reminder:</strong> Meeting scheduled for tomorrow at 10 AM.
                </p>
                <span class="notification-time">2 days ago</span>
            </li>
        </ul>
    </div>

    <script>
    const notificationDropdown = document.getElementById(
        "notificationDropdown"
    );
    const notificationIcon = document.querySelector(".notification-icon");

    notificationIcon.addEventListener("click", () => {
        const isVisible = notificationDropdown.style.display === "block";
        notificationDropdown.style.display = isVisible ? "none" : "block";
    });

    window.addEventListener("click", (e) => {
        if (
            !notificationIcon.contains(e.target) &&
            !notificationDropdown.contains(e.target)
        ) {
            notificationDropdown.style.display = "none";
        }
    });
    </script>

    <style>
    .notification-dropdown {
        position: absolute;
        top: 50px;
        right: 20px;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 300px;
        z-index: 1000;
        padding: 15px;
        max-height: 200px;
        overflow-y: auto;
    }

    .notification-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .notification-item {
        margin-bottom: 10px;
        padding: 10px;
        border-bottom: 1px solid #eee;
        background-color: #f9f9f9;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .notification-item:hover {
        background-color: #e6f7ff;
    }

    .notification-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .notification-time {
        display: block;
        font-size: 0.85em;
        color: #888;
        margin-top: 5px;
    }
    </style>
</body>

</html>