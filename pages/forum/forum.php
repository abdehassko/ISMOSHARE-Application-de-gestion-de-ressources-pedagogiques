<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
else {
    extract($_SESSION);
    include("../../others/code-conexionAvecDB.php");
    $reqForum = $db->query("SELECT f.*, u.NOM_USER, u.PRENOM_USER, u.CHEMIN_PHOTO FROM FORUM f
    JOIN UTILISATEUR u USING(ID_USER)
    WHERE f.ETAT_VALIDATION_F = 'valide'
    ORDER BY f.DATE_PUB_FORUM DESC");
    $forums = $reqForum->fetchAll(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    extract($_FILES);
    if (isset($forumenvoyer)) {
        $msg = [];
        if(!isset($forumcat) || empty($forumcat)) $msg['errforumcat']='vous devez choisir une categorie';
        if(!isset($forumtitle) || empty($forumtitle)) $msg['errforumtitle']='vous devez entrez un titre de votre question';
        if(!isset($forum) || empty($forum)) $msg['errforum']='vous devez tapez votre question';
        if (($forumphoto['error']!=4)){
            if (($forumphoto['error']!=0)) $msg['errforumphoto']='vous devez charger votre photo';
            elseif(!in_array($forumphoto['type'],["image/jpeg","image/jpg","image/png","image/tiff","image/jif","image/heif","image/heic"])) $msg['errforumphoto']='extension de votre photo invalid';
            elseif($forumphoto['size']>40*1024*1024) $msg['errforumphoto']='taille maximal est 40 MO';        
        }
        if(empty($msg)){
            move_uploaded_file($forumphoto['tmp_name'],"..\\..\\uploaded_files\\$forumphoto[name]");
            $forumcat=htmlspecialchars($forumcat);
            $forum=htmlspecialchars($forum);
            $forumtitle=htmlspecialchars($forumtitle);
            try {
                if ($role=='admin' || $role === 'formateur') {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("INSERT into forum(ID_USER,TITRE_FORUM,THEME_FORUM,CONTENU_SUJET,CHEMIN_PHOTO_F,DATE_PUB_FORUM) values(?,?,?,?,?,?)");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,"/ISMOSHARE/uploaded_files/$forumphoto[name]",$date]);
                    $update=$db->prepare("UPDATE forum SET ETAT_VALIDATION_F = 'valide' where ID_USER=?");
                    $update->execute([$id_user]);
                    if ($r) $msgsucces="publiee avec succes";
                    else $msgechec="Erreur dans la publication";
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("INSERT into forum(ID_USER,TITRE_FORUM,THEME_FORUM,CONTENU_SUJET,CHEMIN_PHOTO_F,DATE_PUB_FORUM) values(?,?,?,?,?,?)");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,"/ISMOSHARE/uploaded_files/$forumphoto[name]",$date]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin' or ROLE = 'formateur' ");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "🔔 Nouvelle sujet forum à valider auteur ($nom $prenom)."]);
                    }
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
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'entraide';
$validCategories = ['entraide', 'questions', 'astuces', 'autres'];
if (in_array($selectedCategory, $validCategories)) {
    $stmc = $db->prepare("SELECT f.*, u.PRENOM_USER, u.NOM_USER, u.CHEMIN_PHOTO, f.CHEMIN_PHOTO_F 
                      FROM FORUM f 
                      JOIN utilisateur u ON f.ID_USER = u.ID_USER 
                      WHERE f.THEME_FORUM = ? and f.ETAT_VALIDATION_F = 'valide'
                      ORDER BY f.DATE_PUB_FORUM DESC");
    $stmc->execute([$selectedCategory]);
    $forums = $stmc->fetchAll(PDO::FETCH_ASSOC);
} else {
    $selectedCategory = 'entraide';
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
                <a href="/ISMOSHARE/pages/forum/forum.php" class="menu-item active">
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
            <div class="header">
                <div class="header-title">
                    <p>QUESTIONNER ISMO</p>
                    <button class="new-forum-button">ajouter un sujet</button>
                </div>
            </div>
            <div class="tabs">
                <div class="tab <?php echo $selectedCategory === 'entraide' ? 'active' : ''; ?>"
                    data-category="entraide">entraide</div>
                <div class="tab <?php echo $selectedCategory === 'questions' ? 'active' : ''; ?>"
                    data-category="questions">questions</div>
                <div class="tab <?php echo $selectedCategory === 'astuces' ? 'active' : ''; ?>" data-category="astuces">
                    astuces</div>
                <div class="tab <?php echo $selectedCategory === 'autres' ? 'active' : ''; ?>" data-category="autres">
                    autres</div>
            </div>

            <div class="forum-content">
                <?php if(isset($_GET["msgsucces"])){echo"<center><div style='color : green;'>$_GET[msgsucces]</div></center>";}?>
                <?php if(isset($_GET["msgechec"])){echo"<center><div style='color : red;'>$_GET[msgechec]</div></center>";}?>
                <?php if(isset($msgsucces)){echo"<center><div style='color : green;'>$msgsucces</div></center>";}?>
                <?php if(isset($msgechec)){echo"<center><div style='color : red;'>$msgechec</div></center>";}?>
                <?php 
                    foreach($forums as $f){
                        $stmt = $db->prepare("SELECT COUNT(*) FROM REPONSEFORUM WHERE ID_FORUM = ?");
                        $stmt->execute([$f['ID_FORUM']]);
                        $stats = $stmt->fetchColumn();
                        echo '<div class="question-card">
                                <div class="question-header">
                                <div class="question-avatar">';
                        if (!empty($f['CHEMIN_PHOTO_F']) &&
                            $f['CHEMIN_PHOTO_F'] != 'photos/' && 
                            $f['CHEMIN_PHOTO_F'] !== 'null' && 
                            $f['CHEMIN_PHOTO_F'] !== '/ISMOSHARE/uploaded_files/' 
                            ) {
                            echo "<img class='question-user-photo' src='$f[CHEMIN_PHOTO_F]' width='200' style='display:none;'>";
                        }
                        echo"<img src='$f[CHEMIN_PHOTO]'/>
                            </div>";
                        echo"<div class='question-user'>$f[PRENOM_USER] $f[NOM_USER]</div>
                        <div class='question-time'>$f[DATE_PUB_FORUM]</div>";
                        if ($role=='admin' || $role === 'formateur' || $f['ID_USER'] == $id_user)
                        echo"<div class='file-actions' data-id='$f[ID_FORUM]'>
                            <i class='fas fa-ellipsis-v'></i>
                        </div>";
                        echo"</div>";
                        echo"<a href='?id_forum={$f['ID_FORUM']}' class='question-title' data-idforum='{$f['ID_FORUM']}'>$f[TITRE_FORUM]</a>";
                        echo"<div class='question-body'>$f[CONTENU_SUJET]</div>";
                        echo"<div class='question-stats'>
                            <div class='stat'>💬 $stats</div>
                            </div>
                            </div>";
                    }
                    ?>
                <!-- Modal Overlay -->
                <form id="newForumModal" class="modal-overlay" style="display: none" method="post"
                    enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="form-group">
                            <?php if(isset($msg["errforumcat"])){echo"<div style='color : red;'>$msg[errforumcat]</div>";}?>
                            <select class="select-field" name="forumcat">
                                <option value="" disabled selected>Choose category</option>
                                <option value="entraide">entraide</option>
                                <option value="astuces">astuces</option>
                                <option value="questions">questions</option>
                                <option value="autres">autres</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <?php if(isset($msg["errforumtitle"])){echo"<div style='color : red;'>$msg[errforumtitle]</div>";}?>
                            <input type="text" class="input-field" placeholder="Type a title" name="forumtitle" />
                        </div>

                        <div class="form-group">
                            <?php if(isset($msg["errforum"])){echo"<div style='color : red;'>$msg[errforum]</div>";}?>
                            <textarea class="textarea-field" placeholder="Type your question" name="forum"></textarea>
                        </div>

                        <div class="button-container">
                            <?php if(isset($msg["errforumphoto"])){echo"<div style='color : red;'>$msg[errforumphoto]</div>";}?>
                            <label class="btn btn-primary" style="cursor: pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                Add Image
                                <input type="file" accept="image/*" style="display: none" name="forumphoto" />
                            </label>
                            <button class="btn btn-publish" type="submit" name="forumenvoyer">
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
    <!-- Post Modal Overlay -->
    <?php
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    if (isset($addCommentBtn)) {
        $errcomment = [];
        if(!isset($newComment) || empty($newComment)) $errcomment['errcomment']='vous devez ecrire votre commentaire';
        if(empty($errcomment)){
            $newComment=htmlspecialchars($newComment);
            try {
                if ($role=='admin' || $role=='formateur') {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("INSERT into reponseforum(ID_USER,ID_FORUM,CONTENU_REPONSE,DATE_REPONSE) values(?,?,?,?)");
                    $r=$reqi->execute([$id_user,$id_forum,$newComment,$date]);
                    $update=$db->prepare("UPDATE reponseforum SET ETAT_VALIDATION_C = 'valide' where ID_USER=?");
                    $update->execute([$id_user]);

                    $stmt = $db->prepare("SELECT ID_USER FROM FORUM WHERE ID_FORUM = ?");
                    $stmt->execute([$id_forum]);
                    $owner_id = $stmt->fetchColumn();
                    if ($owner_id != $id_user) {
                        $notif = $db->prepare("INSERT INTO notification (ID_USER,TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?,?, NOW(),0)");
                        $notif->execute([$owner_id,"💬 Vous avez un nouveau commentaire sur votre forum auteur($nom $prenom)"]);
                    }

                    if ($r) $msgsucces="commentee avec succes"; 
                    else $msgechec="Erreur dans la publication";
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("INSERT into reponseforum(ID_USER,ID_FORUM,CONTENU_REPONSE,DATE_REPONSE) values(?,?,?,?)");
                    $r=$reqi->execute([$id_user,$id_forum,$newComment,$date]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin' or ROLE = 'formateur' ");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "💬 Nouvelle  commentaire à valider. auteur($nom $prenom)"]);
                    }

                    if ($r) $msgsucces="Demande de commentaire envoyer vers l admin";
                    else $msgechec="Erreur dans la publication";
                }
            }
            catch (PDOException $e) {
                echo "Erreur inserstion".$e->getMessage();
            }
        }
    }
    }
     ?>
    <div id="postModal" class="modal-overlay" style="display: none">
        <div class="modal-content post-modal-content">
            <span class="close-modal" style="cursor: pointer; float: right; font-size: 1.5em">&times;</span>
            <div id="postModalQuestion"></div>
            <form class="comments-section" method="post">
                <?php if(isset($msgsucces)){echo"<div style='color : green;'>$msgsucces</div>";}?>
                <?php if(isset($msgechec)){echo"<div style='color : red;'>$msgechec</div>";}?>
                <?php if(isset($errcomment["errcomment"])){echo"<div style='color : red;'>$errcomment[errcomment]</div>";}?>
                <h4>Comments</h4>
                <div id="commentsList" style="max-height: 200px; overflow-y: auto">
                    <?php
                    $id_forum = isset($_GET['id_forum']) ? $_GET['id_forum'] : null;
                    if ($id_forum){
                    $reqc = $db->prepare("SELECT rf.*, u.NOM_USER, u.PRENOM_USER, u.CHEMIN_PHOTO FROM REPONSEFORUM rf
                    JOIN UTILISATEUR u USING(ID_USER)
                    WHERE rf.ETAT_VALIDATION_C = 'valide' and ID_FORUM=?
                    ORDER BY rf.DATE_REPONSE DESC");
                    $reqc->execute([$id_forum]);
                    $comments = $reqc->fetchAll(PDO::FETCH_ASSOC);}
                    else $comments = [];
                    foreach ($comments as $comment) {
                        $check = $db->prepare("SELECT 1 FROM REACTION WHERE ID_USER = ? AND ID_REPONSE = ?");
                        $check->execute([$id_user, $comment['ID_REPONSE']]);
                        $Reacted = $check->fetchColumn();

                        $heartClass = $Reacted ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
                        $MEILLEUR_REPONSE_style = $comment['MEILLEUR_REPONSE'] ? "rgb(252, 210, 94)" : "rgb(207, 204, 194)";

                        
                    echo"<div class='comment' style='
                        display: flex;
                        align-items: center;
                        word-break: break-word;
                        white-space: pre-line;
                        width: 100%;
                        overflow-wrap: break-word;
                        justify-content: space-between;
                    '>
                        <div style='flex:1;display: flex; align-items: center; gap: 10px;'>
                            <img src='$comment[CHEMIN_PHOTO]' width=30 style='border-radius: 50%; border: 2px solid black; object-fit: cover; 
                            height: 30px;'><span style='font-size: 10px;font-weight: bolder;'>$comment[NOM_USER] $comment[PRENOM_USER]</span>&nbsp;$comment[CONTENU_REPONSE]
                        </div>
                        <div style='display: flex; align-items: center; gap: 9px; flex-direction: row;'>";
                            if ($role === 'admin' || $role === 'formateur')
                            echo"<span class='comment-action' title='Marquer comme meilleur reponse'
                                style='cursor: pointer; display: flex; align-items: center;'>
                                <a href='best_cf.php?id=$comment[ID_REPONSE]'><i class='fa-solid fa-star' style='font-size: 1em; color: $MEILLEUR_REPONSE_style;'></i></a>
                            </span>";
                            if ($role === 'stagiaire' && $comment['MEILLEUR_REPONSE']==1) {
                                    echo"<span class='comment-action' title='Meilleur reponse'
                                style='cursor: pointer; display: flex; align-items: center;'>
                                <i class='fa-solid fa-star' style='font-size: 1em; color: rgb(252, 210, 94);'></i>
                                </span>";
                            }
                            echo"<span class='comment-action' title='React'
                                style='cursor: pointer; display: flex; align-items: center;'>
                                <a href='react_rf.php?id=$comment[ID_REPONSE]'><i class='$heartClass' style='font-size: 1em; color: #e25555;'></i></a>
                                <span class='heart-count' style='margin-left: 3px;'>$comment[REACTION]</span>
                            </span>";
                            if ($role === 'admin' || $role === 'formateur' || $comment['ID_USER'] == $id_user)
                            echo"<span class='comment-action' title='Delete'
                                style='cursor: pointer; display: flex; align-items: center;'>
                                <a href='delete_cf.php?id=$comment[ID_REPONSE]'><i class='fa-regular fa-trash-can' style='font-size: 1em; color: #888;'></i></a>
                            </span>";
                        echo"</div>
                    </div>";
                    }
                    ?>
                </div>
                <div class="add-comment">
                    <input type="hidden" name="id_forum" id="id_forum_input">
                    <input type="text" id="newCommentInput" name="newComment" placeholder="Add a comment..."
                        style="width: 80%" />
                    <button id="addCommentBtn" name="addCommentBtn" class="btn btn-primary" type="submit"
                        style="margin-left: 5px">
                        Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal elements
    const postModal = document.getElementById("postModal");
    const postModalQuestion = document.getElementById("postModalQuestion");
    const closeModalBtn = postModal.querySelector(".close-modal");
    const commentsList = document.getElementById("commentsList");
    const newCommentInput = document.getElementById("newCommentInput");
    const addCommentBtn = document.getElementById("addCommentBtn");

    // Open modal on question title click

    document.querySelectorAll(".question-title").forEach((title) => {
        title.addEventListener("click", function(e) {
            e.preventDefault();
            const forumId = this.dataset.idforum;

            if (forumId) {
                // Save forum ID in localStorage before reloading
                const currentCategory = new URLSearchParams(window.location.search).get("category") ||
                    "entraide";
                window.location.href =
                    `/ISMOSHARE/pages/forum/forum.php?category=${currentCategory}&id_forum=${forumId}`;
            }
        });
    });

    // Find the parent question-card and clone it
    document.addEventListener("DOMContentLoaded", function() {
        let forumId;

        // 1. Check if stored in localStorage (from click)
        const storedForumId = localStorage.getItem("forumId");
        if (storedForumId) {
            forumId = storedForumId;
            localStorage.removeItem("forumId");
        } else {
            // 2. Or get from URL directly (refresh/bookmark/manual access)
            const urlParams = new URLSearchParams(window.location.search);
            forumId = urlParams.get("id_forum");
        }

        if (forumId) {
            const input = document.getElementById("id_forum_input");
            if (input) input.value = forumId;

            const card = document.querySelector(`.question-title[data-idforum='${forumId}']`)?.closest(
                ".question-card");

            if (card) {
                postModalQuestion.innerHTML = "";
                const clonedCard = card.cloneNode(true);
                clonedCard.querySelectorAll(".file-actions").forEach(el => el.remove());
                postModalQuestion.appendChild(clonedCard);

                const photo = card.querySelector(".question-user-photo");
                if (photo && photo.src && photo.src.trim() !== "" && photo.src !== "null" && photo.src !==
                    "undefined") {
                    const clonedPhoto = photo.cloneNode(true);
                    clonedPhoto.style.display = "block";
                    clonedPhoto.style.maxWidth = "100%";
                    clonedPhoto.style.height = "auto";
                    clonedPhoto.style.marginTop = "10px";
                    postModalQuestion.appendChild(clonedPhoto);
                }

                postModal.style.display = "flex";
            } else {
                console.warn("Card not found for forum ID:", forumId);
            }
        }
    });

    // Close modal
    closeModalBtn.onclick = function() {
        postModal.style.display = "none";
    };
    window.addEventListener("click", (e) => {
        if (e.target === postModal) {
            postModal.style.display = "none";
        }
    });
    </script>
    <script>
    // Dropdown for file actions
    document.querySelectorAll(".file-actions").forEach(function(actionCell) {
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
            const forumId = this.dataset.id;

            // Ajouter actions dans le dropdown
            dropdown.innerHTML = `
        <ul style="list-style:none;margin:0;padding:8px 0;">
            <a class="delete" href="delete_forum.php?id=${forumId}"><li class="file-action-delete" style="padding:8px 24px;cursor:pointer;">Delete</li></a>
            <a class="modify" href="modify_forum.php?id=${forumId}"><li class="file-action-modify" style="padding:8px 24px;cursor:pointer;">Modify</li></a>
        </ul>
    `;
            // Hide all other dropdowns
            document
                .querySelectorAll(".file-action-dropdown")
                .forEach((d) => (d.style.display = "none"));
            const rect = actionCell.getBoundingClientRect();
            dropdown.style.left = rect.left + window.scrollX + "px";
            dropdown.style.top = rect.bottom + window.scrollY + "px";
            dropdown.style.display = "block";

        });

        // Fermer dropdown en cliquant à l'extérieur
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
            window.location.href = `/ISMOSHARE/pages/forum/forum.php?category=${category}`;
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

    .question-user-photo {
        display: flex;
        justify-self: center;
        border-radius: 8px;
    }

    .question-user-photo[src=""],
    .question-user-photo[src="null"],
    .question-user-photo[src="undefined"],
    .question-user-photo:not([src]) {
        display: none !important;
    }
    </style>
</body>

</html>