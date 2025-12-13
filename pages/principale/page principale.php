<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/principale/page principale.php");
    exit();
}
else {
    extract($_SESSION);
    include("../../others/code-conexionAvecDB.php");

    $reqRessources = $db->query("SELECT * FROM RESSOURCE WHERE ETAT_VALIDATION_R = 'valide' ORDER BY DATE_UPLOAD DESC LIMIT 5");
    $ressources = $reqRessources->fetchAll(PDO::FETCH_ASSOC);

    $reqForums = $db->query("SELECT f.*, u.NOM_USER, u.PRENOM_USER, u.CHEMIN_PHOTO FROM FORUM f
    JOIN UTILISATEUR u USING (ID_USER)
    WHERE f.ETAT_VALIDATION_F = 'valide'
    ORDER BY f.DATE_PUB_FORUM DESC
    LIMIT 5");
    $forums = $reqForums->fetchAll(PDO::FETCH_ASSOC);

    $reqAnnonces = $db->query("SELECT * FROM ANNONCE WHERE ETAT_VALIDATION_A = 'valide' ORDER BY DATE_ANNONCE DESC LIMIT 5");
    $annonces = $reqAnnonces->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISMOSHARE Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/style.css" />
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
                <img src="<?= $photo ?>" width="60" />
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
                <a href="/ISMOSHARE/others/non-autorise.php" class="menu-item">
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
                        <?php
                        $nbr_notf=$db->prepare("SELECT count(*) from notification where ID_USER=? and EST_LUE_=0");
                        $nbr_notf->execute([$id_user]);
                        $nott=$nbr_notf->fetch(PDO::FETCH_COLUMN);
                        echo "<span class='notification-count'>$nott</span>"
                        ?>
                    </div>
                    <a href="/ISMOSHARE/pages/profile/profile.php"><img src="<?= $photo ?>" /></a>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="welcome-card">
                <h2>Welcome back, <?=$nom?> <?=$prenom?></h2>
                <?php
                        $nbr_notf=$db->prepare("SELECT count(*) from notification where ID_USER=? and EST_LUE_=0");
                        $nbr_notf->execute([$id_user]);
                        $nott=$nbr_notf->fetch(PDO::FETCH_COLUMN);
                echo "<p>
                    Here’s what’s new today. Stay organized and make the most of your
                    day! <br />
                    You have 3 new messages and $nott unseen notification.
                </p>"
                ?>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Announcements Column -->
                <div class="content-column">
                    <div class="section-header">
                        <h3 class="section-title">LATEST ANNONCES</h3>
                        <a href="/ISMOSHARE/pages/annonce/annonce.php" class="view-all">See all</a>
                    </div>

                    <div class="scrollable-content">
                        <!-- Announcement Cards -->
                        <?php
                        foreach ($annonces as $a){
                            $day = date_format(date_create($a['DATE_ANNONCE']), 'M');
                            $recap=substr($a['CONTENU_ANNONCE'],0,50).'...';
                            echo '<div class="announcement-card">
                                    <div class="announcement-date">';
                            echo "<span class='date-number'>$day</span>
                                    </div>";
                            echo "<div class='announcement-content'>
                                        <div class='announcement-title'>$a[NOM_ANNONCE]</div>
                                        <div class='announcement-subtitle'>$recap</div>
                                    </div>";
                            echo"</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Latest Subject Column -->
                <div class="content-column">
                    <div class="section-header">
                        <h3 class="section-title">LATEST SUJET</h3>
                        <a href="/ISMOSHARE/pages/forum/forum.php" class="view-all">See all</a>
                    </div>

                    <div class="scrollable-content">
                        <?php
                        foreach ($forums as $f){
                            $frecap=substr($f["CONTENU_SUJET"], 0, 50). '...';
                            $d=date_format(date_create($f["DATE_PUB_FORUM"]), 'd-M');
                            echo '<div class="content-card">';
                            echo "<div class='content-icon icon-blue'><img src='$f[CHEMIN_PHOTO]' width='40' height='42' style='border-radius:50%; object-fit:cover;'></div>";
                            echo "<div class='content-info'>
                                    <div class='announcement-title'>$f[PRENOM_USER] $f[NOM_USER]</div>
                                    <div class='announcement-subtitle'>$frecap</div>
                                </div>";
                            echo"<span class='content-time'>$d</span>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Latest Files Column -->
                <div class="content-column">
                    <div class="section-header">
                        <h3 class="section-title">LATEST FILES</h3>
                        <a href="/ISMOSHARE/pages/ressource/ressources.php" class="view-all">See all</a>
                    </div>

                    <div class="scrollable-content">
                        <?php
                        foreach ($ressources as $r){
                            $n=basename($r["CHEMIN_FICHIER"]);
                            $dd=date_format(date_create($r["DATE_UPLOAD"]), 'M d, Y');
                            echo"<div class='content-card'>
                                 <div class='content-info'>";
                            echo"<div class='announcement-title'>$n</div>";
                            echo"<div class='announcement-subtitle'>$dd</div>
                            </div>
                            <i class='fas fa-file-alt file-icon'></i>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="notification-dropdown" style="display: none">
        <h4 style="display: inline;">Notifications : </h4><a style="display: inline;"
            href='/ISMOSHARE/notifications/mark-all-read.php'>tous est
            lue?</a>
        <ul class="notification-list">
            <?php
            $notif = $db->prepare("SELECT * FROM NOTIFICATION WHERE ID_USER = ? and EST_LUE_=0 ORDER BY DATE_NOTIFICATION DESC");
            $notif->execute([$id_user]);
            $notifications = $notif->fetchAll(PDO::FETCH_ASSOC);
            foreach ($notifications as $n){
                echo"<li class='notification-item'>";
                echo"<p>$n[TEXTE_NOTIFICATION].</p>";
                echo"<span class='notification-time'>$n[DATE_NOTIFICATION]</span>
                <a href='/ISMOSHARE/notifications/mark-read.php?id_nf=$n[ID_NOTIFICATION]'>Marquer comme lue</a>";
                echo"</li>";
            }
            ?>
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