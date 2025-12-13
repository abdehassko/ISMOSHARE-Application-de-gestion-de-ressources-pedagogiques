<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
else {
    include("../../others/code-conexionAvecDB.php");
    extract($_SESSION);
    $reqAnnonce = $db->query("SELECT a.*, u.NOM_USER, u.PRENOM_USER, u.CHEMIN_PHOTO FROM ANNONCE a
    JOIN UTILISATEUR u USING(ID_USER)
    WHERE a.ETAT_VALIDATION_A = 'valide'
    ORDER BY a.DATE_ANNONCE DESC");
    $annonces = $reqAnnonce->fetchAll(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    if (isset($forumenvoyer)) {
        $msg = [];
        if(!isset($forumcat) || empty($forumcat)) $msg['errforumcat']='vous devez choisir une categorie';
        if(!isset($forumtitle) || empty($forumtitle)) $msg['errforumtitle']='vous devez entrez un titre de votre annonce';
        if(!isset($forum) || empty($forum)) $msg['errforum']='vous devez tapez votre annonce';
        if(empty($msg)){
            $forumcat=htmlspecialchars($forumcat);
            $forum=htmlspecialchars($forum);
            $forumtitle=htmlspecialchars($forumtitle);
            try {
                if ($role=='admin' || $role === 'formateur') {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("INSERT into annonce(ID_USER,NOM_ANNONCE,TYPE_ANNONCE,CONTENU_ANNONCE,DATE_ANNONCE) values(?,?,?,?,?)");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,$date]);
                    $annonceId = $db->lastInsertId();
                    $update=$db->prepare("UPDATE annonce SET ETAT_VALIDATION_A = 'valide' where ID_USER=?");
                    $update->execute([$id_user]);
                    
                    
                    $rqs = $db->query("SELECT ID_USER FROM UTILISATEUR");
                    $users=$rqs->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($users as $user) {
                        if ($user["ID_USER"] != $id_user) {
                            $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                            $notif->execute([$user['ID_USER'], "📅​ Une Nouvelle Annonce a ete publiee par $nom $prenom"]);
                        }
                    }
                    if ($r) $msgsucces="publiee avec succes";
                    else $msgechec="Erreur dans la publication";
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("INSERT into annonce(ID_USER,NOM_ANNONCE,TYPE_ANNONCE,CONTENU_ANNONCE,DATE_ANNONCE) values(?,?,?,?,?)");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,$date]);
                    if ($r) $msgsucces="Demande de publication envoyer vers l admin";
                    else $msgechec="Erreur dans la publication";
                }
            }
            catch (PDOException $e) {
                echo "Erreur inserstion".$e->getMessage();
            }
        }
    }
}
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'opportunités';
$validCategories = ['opportunités', 'actualités', 'concours', 'autres'];
if (in_array($selectedCategory, $validCategories)) {
    $stmc = $db->prepare("SELECT a.*, u.PRENOM_USER, u.NOM_USER, u.CHEMIN_PHOTO
                      FROM ANNONCE a
                      JOIN utilisateur u ON a.ID_USER = u.ID_USER 
                      WHERE a.TYPE_ANNONCE = ? and a.ETAT_VALIDATION_A = 'valide'
                      ORDER BY a.DATE_ANNONCE DESC");
    $stmc->execute([$selectedCategory]);
    $annonces = $stmc->fetchAll(PDO::FETCH_ASSOC);
} else {
    $selectedCategory = 'opportunités';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISMOSHARE Forum</title>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" href="/ISMOSHARE/assets/logoapp.png" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/forum-style.css" />
</head>
<style>
.question-title {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 15px;
    color: #000000;
}

.question-title:hover {
    color: #000000;
    text-decoration: none;
}
</style>

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
                <a href="/ISMOSHARE/pages/annonce/annonce.php" class="menu-item active">
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
            <div class="header">
                <div class="header-title">
                    <p>ANNONCER POUR ISMO</p>
                    <?php
                    if ($_SESSION['role']=='admin' || $_SESSION['role']=='formateur') echo"<button class='new-forum-button'>ajouter une annonce</button>";
                    ?>
                </div>
            </div>
            <div class="tabs">
                <div class="tab <?php echo $selectedCategory === 'opportunités' ? 'active' : ''; ?>"
                    data-category="opportunités">opportunités</div>
                <div class="tab <?php echo $selectedCategory === 'actualités' ? 'active' : ''; ?>"
                    data-category="actualités">actualités</div>
                <div class="tab <?php echo $selectedCategory === 'concours' ? 'active' : ''; ?>"
                    data-category="concours">concours</div>
                <div class="tab <?php echo $selectedCategory === 'autres' ? 'active' : ''; ?>" data-category="autres">
                    autres</div>
            </div>

            <div class="forum-content">
                <?php if(isset($_GET["msgsucces"])){echo"<center><div style='color : green;'>$_GET[msgsucces]</div></center>";}?>
                <?php if(isset($_GET["msgechec"])){echo"<center><div style='color : green;'>$_GET[msgechec]</div></center>";}?>
                <?php if(isset($msgsucces)){echo"<center><div style='color : green;'>$msgsucces</div></center>";}?>
                <?php if(isset($msgechec)){echo"<center><div style='color : red;'>$msgechec</div></center>";}?>
                <?php 
                foreach($annonces as $a){
                    echo"<div class='question-card'>
                    <div class='question-header'>
                        <div class='question-avatar'>
                            <img src='$a[CHEMIN_PHOTO]' />
                        </div>
                        <div class='question-user'>$a[PRENOM_USER] $a[NOM_USER]</div>
                        <div class='question-time'>$a[DATE_ANNONCE]</div>";
                        if ($role === 'admin'  || $a['ID_USER'] == $id_user)
                        echo "<div class='file-actions' data-id='$a[ID_ANNONCE]'>
                            <i class='fas fa-ellipsis-v'></i>
                        </div>";
                    echo"</div>
                    <p class='question-title'>$a[NOM_ANNONCE]</p>
                    <div class='question-body'>
                        $a[CONTENU_ANNONCE]
                    </div>
                </div>";
                }
                ?>
                <!-- Modal Overlay -->
                <form id="newForumModal" class="modal-overlay" style="display: none" method="post">
                    <div class="modal-content">
                        <div class="form-group">
                            <?php if(isset($msg["errforumcat"])){echo"<div style='color : red;'>$msg[errforumcat]</div>";}?>
                            <select class="select-field" name="forumcat">
                                <option value="" disabled selected>Choose category</option>
                                <option value="opportunités">opportunités</option>
                                <option value="actualités">actualités</option>
                                <option value="concours">concours</option>
                                <option value="autres">autres</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <?php if(isset($msg["errforumtitle"])){echo"<div style='color : red;'>$msg[errforumtitle]</div>";}?>
                            <input type="text" class="input-field" placeholder="Type editing attention title"
                                name="forumtitle" />
                        </div>

                        <div class="form-group">
                            <?php if(isset($msg["errforum"])){echo"<div style='color : red;'>$msg[errforum]</div>";}?>
                            <textarea class="textarea-field" placeholder="Type your question" name="forum"></textarea>
                        </div>

                        <div class="button-container">
                            <button class="btn btn-publish" name="forumenvoyer">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                                Publish
                            </button>
                        </div>
                    </div>
                </form>
                <script>
                const modal = document.getElementById("newForumModal");
                const newForumBtn = document.querySelector(".new-forum-button");

                newForumBtn.addEventListener("click", () => {
                    modal.style.display = "flex";
                });

                window.addEventListener("click", (e) => {
                    if (e.target === modal) {
                        modal.style.display = "none";
                    }
                });
                <?php if (!empty($msg)) echo "modal.style.display = 'flex'; "?>
                </script>
            </div>
        </main>
    </div>
    <script>
    // Dropdown for file actions
    document.querySelectorAll(".file-actions").forEach(function(actionCell) {
        // Create dropdown if not exists
        let dropdown = document.createElement("div");
        dropdown.className = "file-action-dropdown";
        dropdown.style.display = "none";
        dropdown.style.position = "absolute";
        dropdown.style.background = "#fff";
        dropdown.style.boxShadow = "0 2px 8px rgba(0,0,0,0.15)";
        dropdown.style.borderRadius = "6px";
        dropdown.style.zIndex = 1000;

        document.body.appendChild(dropdown);

        actionCell.addEventListener("click", function(e) {
            e.stopPropagation();
            const annonceId = this.dataset.id;

            dropdown.innerHTML = `
        <ul style="list-style:none;margin:0;padding:8px 0;">
            <a class="delete" href="delete_annonce.php?id=${annonceId}"><li class="file-action-delete" style="padding:8px 24px;cursor:pointer;">Delete</li></a>
            <a class="modify" href="modify_annonce.php?id=${annonceId}"><li class="file-action-modify" style="padding:8px 24px;cursor:pointer;">Modify</li></a>
        </ul>
      `;
            // Hide all other dropdowns
            document
                .querySelectorAll(".file-action-dropdown")
                .forEach((d) => (d.style.display = "none"));
            // Position dropdown
            const rect = actionCell.getBoundingClientRect();
            dropdown.style.left = rect.left + window.scrollX + "px";
            dropdown.style.top = rect.bottom + window.scrollY + "px";
            dropdown.style.display = "block";
        });
        // Hide dropdown on click outside
        window.addEventListener("click", function() {
            dropdown.style.display = "none";
        });
        // Prevent closing when clicking inside dropdown
        dropdown.addEventListener("click", function(e) {
            e.stopPropagation();
        });
    });
    </script>
    <script>
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            window.location.href = `/ISMOSHARE/pages/annonce/annonce.php?category=${category}`;
        });
    });
    </script>
    <style>
    .modify,
    .delete {
        all: unset;
    }

    .file-action-dropdown ul li:hover {
        background: #f0f0f0;
    }
    </style>
</body>

</html>