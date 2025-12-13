<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
else {
    extract($_SESSION);
    include("../../others/code-conexionAvecDB.php");
    if (isset($_GET["id"])) {
        $reqm = $db->prepare("SELECT * from forum where ID_FORUM=?");
        $reqm->execute([$_GET["id"]]);
        $modforum=$reqm->fetch(PDO::FETCH_NUM);
    }
    $reqForum = $db->query("SELECT f.*, u.NOM_USER, u.PRENOM_USER, u.CHEMIN_PHOTO FROM FORUM f
    JOIN UTILISATEUR u USING(ID_USER)
    WHERE f.ETAT_VALIDATION_F = 'valide'
    ORDER BY f.DATE_PUB_FORUM DESC");
    $forums = $reqForum->fetchAll(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    $msg = [];
    if(!isset($forumcat) || empty($forumcat)) $msg['errforumcat']='vous devez choisir une categorie';
    if(!isset($forumtitle) || empty($forumtitle)) $msg['errforumtitle']='vous devez entrez un titre de votre question';
    if(!isset($forum) || empty($forum)) $msg['errforum']='vous devez tapez votre question';
    extract($_FILES);
    if (($forumphoto['error']==0)){
        if(!in_array($forumphoto['type'],["image/jpeg","image/jpg","image/png","image/tiff","image/jif","image/heif","image/heic"])) $msg['errforumphoto']='extension de votre photo invalid';
        elseif($forumphoto['size']>40*1024*1024) $msg['errforumphoto']='taille maximal est 40 MO';   
        if(empty($msg)){
            move_uploaded_file($forumphoto['tmp_name'],"..\\..\\uploaded_files\\$forumphoto[name]");
            $forumcat=htmlspecialchars($forumcat);
            $forum=htmlspecialchars($forum);
            $forumtitle=htmlspecialchars($forumtitle);
            try {
                if ($role=='admin' || $role === 'formateur') {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("UPDATE forum set ID_USER=?,TITRE_FORUM=?,THEME_FORUM=?,CONTENU_SUJET=?,CHEMIN_PHOTO_F=?,DATE_PUB_FORUM=? where ID_FORUM=?");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,"/ISMOSHARE/uploaded_files/$forumphoto[name]",$date,$_GET['id']]);
                    if ($r) {header("Location:/ISMOSHARE/pages/forum/forum.php?msgsucces=modifee avec succes");exit;}
                    else {header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Erreur dans la modification");exit;}
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("UPDATE forum set ID_USER=?,TITRE_FORUM=?,THEME_FORUM=?,CONTENU_SUJET=?,CHEMIN_PHOTO_F=?,DATE_PUB_FORUM=? where ID_FORUM=?");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,"/ISMOSHARE/uploaded_files/$forumphoto[name]",$date,$_GET['id']]);
                    $update=$db->prepare("UPDATE forum SET ETAT_VALIDATION_F = 'en attente' where ID_FORUM=?");
                    $update->execute([$_GET['id']]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin' or ROLE = 'formateur' ");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "✍🏻 $nom $prenom a demande de modifier son sujet forum"]);
                    }

                    if ($r) {header("Location:/ISMOSHARE/pages/forum/forum.php?msgsucces=Demande de modification envoyer vers l admin");exit;}
                    else {header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Erreur dans la modification");exit;}
                }
            }
            catch (PDOException $e) {
                echo "Erreur inserstion".$e->getMessage();
            }
        }     
    } 
    else{
        if(empty($msg)){
            $forumcat=htmlspecialchars($forumcat);
            $forum=htmlspecialchars($forum);
            $forumtitle=htmlspecialchars($forumtitle);
            try {
                if ($role=='admin' || $role === 'formateur') {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("UPDATE forum set ID_USER=?,TITRE_FORUM=?,THEME_FORUM=?,CONTENU_SUJET=?,DATE_PUB_FORUM=? where ID_FORUM=?");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,$date,$_GET['id']]);
                    if ($r) {header("Location:/ISMOSHARE/pages/forum/forum.php?msgsucces=modifee avec succes");exit;}
                    else {header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Erreur dans la modification");exit;}
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d H:i");
                    $reqi=$db->prepare("UPDATE forum set ID_USER=?,TITRE_FORUM=?,THEME_FORUM=?,CONTENU_SUJET=?,DATE_PUB_FORUM=? where ID_FORUM=?");
                    $r=$reqi->execute([$id_user,$forumtitle,$forumcat,$forum,$date,$_GET['id']]);
                    $update=$db->prepare("UPDATE forum SET ETAT_VALIDATION_F = 'en attente' where ID_FORUM=?");
                    $update->execute([$_GET['id']]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin' or ROLE = 'formateur' ");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "✍🏻 $nom $prenom a demande de modifier son sujet forum"]);
                    }
                    
                    if ($r) {header("Location:/ISMOSHARE/pages/forum/forum.php?msgsucces=Demande de modification envoyer vers l admin");exit;}
                    else {header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Erreur dans la modification");exit;}
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
                <?php if(isset($_GET["msgechec"])){echo"<center><div style='color : green;'>$_GET[msgechec]</div></center>";}?>
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
                        echo"<a href='#' class='question-title'>$f[TITRE_FORUM]</a>";
                        echo"<div class='question-body'>$f[CONTENU_SUJET]</div>";
                        echo"<div class='question-stats'>
                            <div class='stat'>💬 $stats</div>
                            </div>
                            </div>";
                    }
                    ?>
                <!-- Modal Overlay -->
                <form id="newForumModal" class="modal-overlay" style="display: flex;align-self:center;" method="post"
                    enctype="multipart/form-data">

                    <div class="modal-content">
                        <?php if(isset($msgsucces)){echo"<center><div style='color : green;'>$msgsucces</div></center>";}?>
                        <?php if(isset($msgechec)){echo"<center><div style='color : red;'>$msgechec</div></center>";}?>
                        <div class="form-group">
                            <?php if(isset($msg["errforumcat"])){echo"<div style='color : red;'>$msg[errforumcat]</div>";}?>
                            <select class="select-field" name="forumcat" value="<?=$modforum[3]?>">
                                <option value="" disabled>Choose category</option>
                                <?php
                                $themes=["entraide","astuces","questions","autres"];
                                foreach($themes as $theme){
                                    $s="";
                                    if($modforum[3]==$theme) $s="SELECTED";
                                    echo"<option value='$theme' $s>$theme</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <?php if(isset($msg["errforumtitle"])){echo"<div style='color : red;'>$msg[errforumtitle]</div>";}?>
                            <input type="text" class="input-field" placeholder="Type a title" name="forumtitle"
                                value="<?=$modforum[2]?>" />
                        </div>

                        <div class="form-group">
                            <?php if(isset($msg["errforum"])){echo"<div style='color : red;'>$msg[errforum]</div>";}?>
                            <textarea class="textarea-field" placeholder="Type your question"
                                name="forum"><?=$modforum[4]?></textarea>
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
                                Modifier Image
                                <input type="file" accept="image/*" style="display: none" name="forumphoto" />
                            </label>
                            <button class="btn btn-publish" type="submit" name="forumenvoyer">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                                Modifier
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
    <div id="postModal" class="modal-overlay" style="display: none">
        <div class="modal-content post-modal-content">
            <span class="close-modal" style="cursor: pointer; float: right; font-size: 1.5em">&times;</span>
            <div id="postModalQuestion"></div>
            <div class="comments-section">
                <h4>Comments</h4>
                <div id="commentsList" style="max-height: 200px; overflow-y: auto">
                    <!-- Example comments -->
                    <div class="comment" style="
                word-break: break-word;
                white-space: pre-line;
                width: 100%;
                overflow-wrap: break-word;
              ">
                        <strong>User1:</strong> This is a great question!
                    </div>
                    <div class="comment" style="
                word-break: break-word;
                white-space: pre-line;
                width: 100%;
                overflow-wrap: break-word;
              ">
                        <strong>User2:</strong> I have the same issue.
                    </div>
                </div>
                <div class="add-comment">
                    <input type="text" id="newCommentInput" placeholder="Add a comment..." style="width: 80%" />
                    <button id="addCommentBtn" class="btn btn-primary" style="margin-left: 5px">
                        Post
                    </button>
                </div>
            </div>
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
            // Find the parent question-card and clone it
            const card = this.closest(".question-card");
            if (card) {
                postModalQuestion.innerHTML = "";
                const clonedCard = card.cloneNode(true);
                clonedCard.querySelectorAll(".file-actions").forEach(el => el.remove());
                postModalQuestion.appendChild(clonedCard);
                const photo = card.querySelector(".question-user-photo");
                if (photo &&
                    photo.getAttribute("src") &&
                    photo.getAttribute("src").trim() !== "" &&
                    photo.getAttribute("src") !== "null" &&
                    photo.getAttribute("src") !== "undefined") {

                    const clonedPhoto = photo.cloneNode(true);
                    clonedPhoto.style.display = "block";
                    clonedPhoto.style.maxWidth = "100%";
                    clonedPhoto.style.height = "auto";
                    clonedPhoto.style.marginTop = "10px";
                    postModalQuestion.appendChild(clonedPhoto);
                }
                postModal.style.display = "flex";
            }
        });
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

    // Add comment
    addCommentBtn.onclick = function() {
        const val = newCommentInput.value.trim();
        if (val) {
            const div = document.createElement("div");
            div.className = "comment";
            div.style.wordBreak = "break-word";
            div.style.whiteSpace = "pre-line";
            div.style.width = "100%";
            div.style.overflowWrap = "break-word";
            div.innerHTML = `<strong>You:</strong> ${val}`;
            commentsList.appendChild(div);
            newCommentInput.value = "";
        }
    };
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