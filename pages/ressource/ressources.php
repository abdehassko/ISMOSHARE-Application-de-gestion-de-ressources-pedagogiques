<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
else {     
    include("../../others/code-conexionAvecDB.php");
    extract($_SESSION);
    $reqRessource = "SELECT r.*, u.NOM_USER, u.PRENOM_USER, f.NOM_FILIERE, m.NOM_MODULE
        FROM RESSOURCE r
        JOIN UTILISATEUR u USING(ID_USER)
        JOIN FILIERE f USING(ID_FILIERE)
        JOIN MODULE m USING(ID_MODULE)
        WHERE r.ETAT_VALIDATION_R = 'valide'";
    $params = [];
    if (!empty($_GET['filterFiliere'])) {
        $reqRessource .= " AND r.ID_FILIERE = ?";
        $params[] = $_GET['filterFiliere'];
    }
    if (!empty($_GET['filterModule'])) {     
        $reqRessource .= " AND r.ID_MODULE = ?";
        $params[] = $_GET['filterModule'];
    }
    if (!empty($_GET['filterDate'])) { 
        $now = date('Y-m-d');
        if ($_GET['filterDate'] === 'Semaine') {
            $monday = date('Y-m-d', strtotime('monday this week'));
            $reqRessource .= " AND r.DATE_UPLOAD >= ?";
            $params[] = $monday;
        }
        elseif ($_GET['filterDate'] === 'Mois') {
            $reqRessource .= " AND MONTH(r.DATE_UPLOAD) = MONTH(CURDATE()) AND YEAR(r.DATE_UPLOAD) = YEAR(CURDATE())";
        }

        elseif ($_GET['filterDate'] === 'Annee') {
            $reqRessource .= " AND YEAR(r.DATE_UPLOAD) = YEAR(CURDATE())";
        } 
    }
    $reqRessource .= " ORDER BY r.DATE_UPLOAD DESC";
    $reqRessource=$db->prepare($reqRessource);
    $reqRessource->execute($params);
    $ressources = $reqRessource->fetchAll(PDO::FETCH_ASSOC);
    }
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    extract($_FILES);
    if (isset($importer)) {
        $msg = [];
        if(!isset($ressourcetitle) || empty($ressourcetitle)) $msg['errressourcetitle']='vous devez choisir un titre';
        if(!isset($filiere) || empty($filiere)) $msg['errfiliere']='vous devez choisir un filiere';
        if(!isset($module) || empty($module)) $msg['errmodule']='vous devez choisir une module';
        if (($fichier['error']!=0)) $msg['errfichier']='vous devez charger votre fichier';
        elseif(!in_array($fichier['type'],["image/jpeg","image/jpg","image/png","image/tiff","image/jif","image/heif","image/heic","application/pdf","application/msword"
        ,"application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-powerpoint"
        ,"application/vnd.openxmlformats-officedocument.presentationml.presentation","text/plain","text/html","application/javascript"
        ,"text/x-python","application/x-python-code"])) $msg['errfichier']='extension de votre fichier invalid';
        elseif($fichier['size']>40*1024*1024) $msg['errfichier']='taille maximal est 40 MO';        
        if(empty($msg)){
            move_uploaded_file($fichier['tmp_name'],"..\\..\\uploaded_files\\$fichier[name]");
            $ressourcetitle=htmlspecialchars($ressourcetitle);
            try {
                if ($role=='admin' || $role=='formateur') {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("INSERT into ressource(ID_USER,TITRE_RESSOURCE,ID_MODULE,ID_FILIERE,CHEMIN_FICHIER,DATE_UPLOAD) values(?,?,?,?,?,?)");
                    $r=$reqi->execute([$id_user,$ressourcetitle,$module,$filiere,"/ISMOSHARE/uploaded_files/$fichier[name]",$date]);
                    $update=$db->prepare("UPDATE ressource SET ETAT_VALIDATION_R = 'valide' where ID_USER=?");
                    $update->execute([$id_user]);
                    if ($r) $msgsucces="importee avec succes";
                    else $msgechec="Erreur dans l importation";
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("INSERT into ressource(ID_USER,TITRE_RESSOURCE,ID_MODULE,ID_FILIERE,CHEMIN_FICHIER,DATE_UPLOAD) values(?,?,?,?,?,?)");
                    $r=$reqi->execute([$id_user,$ressourcetitle,$module,$filiere,"/ISMOSHARE/uploaded_files/$fichier[name]",$date]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin'");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "🔔 Nouvelle  Ressource à valider. auteur($nom $prenom)"]);
                    }
                    
                    if ($r) $msgsucces="Demande d importation envoyer vers l admin";
                    else $msgechec="Erreur dans l importation";
                }
            }
            catch (PDOException $e) {
                echo "Erreur inserstion".$e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISMOSHARE Resources</title>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/ressource-style.css" />
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
                <a href="/ISMOSHARE/pages/ressource/ressources.php" class="menu-item active">
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
            <div class="top-bar">
                <div class="search-container1">
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
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    RESSOURCES <i class="fas fa-chevron-right"></i>
                </h1>
            </div>

            <!-- Search Bar -->
            <div class="search-container" style="margin-bottom: 20px">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search filiere publicateur..." />
            </div>
            <?php if(isset($_GET["msgsucces"])){echo"<center><div style='color : green;'>$_GET[msgsucces]</div></center>";}?>
            <?php if(isset($_GET["msgechec"])){echo"<center><div style='color : green;'>$_GET[msgechec]</div></center>";}?>
            <?php if(isset($msgsucces)){echo"<div style='color : green;'>$msgsucces</div>";}?>
            <?php if(isset($msgechec)){echo"<div style='color : red;'>$msgechec</div>";}?>
            <!-- Files Section -->
            <div class="files-section">
                <div class="section-header">
                    <h2 class="section-title">
                        Public Files <span class="file-count">
                            <?php
                            $count=$db->query("SELECT count(*) from ressource where ETAT_VALIDATION_R='valide'");
                            $c=$count->fetch(PDO::FETCH_COLUMN);
                            echo "(Total $c)";
                            ?>
                        </span>
                    </h2>
                    <div style="display: flex; gap: 15px; align-items: center">
                        <button class="add-button">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="filter-button">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="files-table">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 40%">
                                    File Name <i class="fas fa-sort"></i>
                                </th>
                                <th style="width: 15%">
                                    FILIERE <i class="fas fa-sort"></i>
                                </th>
                                <th style="width: 15%">MODULE <i class="fas fa-sort"></i></th>
                                <th style="width: 15%">PUBLICATEUR <i class="fas fa-sort"></i></th>
                                <th style="width: 20%">
                                    date publication <i class="fas fa-sort"></i>
                                </th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($ressources as $r){
                                $fileSize = file_exists($r['CHEMIN_FICHIER']) ? filesize($r['CHEMIN_FICHIER']) : 0;
                                $fileSize_KB=round($fileSize / 1024/1024, 2);
                                $name = basename($r['CHEMIN_FICHIER']);
                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                switch ($ext) {
                                    case 'pdf':
                                        $iconClass = 'fas fa-file-pdf icon-pdf';
                                        break;
                                    case 'doc':
                                    case 'docx':
                                        $iconClass = 'fas fa-file-word icon-word';
                                        break;
                                    case 'xls':
                                    case 'xlsx':
                                        $iconClass = 'fas fa-file-excel icon-excel';
                                        break;
                                    case 'ppt':
                                    case 'pptx':
                                        $iconClass = 'fas fa-file-powerpoint icon-ppt';
                                        break;
                                    case 'zip':
                                    case 'rar':
                                        $iconClass = 'fas fa-file-archive icon-zip';
                                        break;
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'png':
                                    case 'gif':
                                        $iconClass = 'fas fa-file-image icon-img';
                                        break;
                                    case 'txt':
                                        $iconClass = 'fas fa-file-alt icon-txt';
                                        break;
                                    default:
                                        $iconClass = 'fas fa-file icon-default';
                                        break;
                                }
                                echo"
                                    <a href='?id_ressource={$r['ID_RESSOURCE']}'><tr class='file-row' data-id_ressource='{$r['ID_RESSOURCE']}'>
                                    <td>
                                    <div class='file-icon'>
                                    <i class='$iconClass'></i>
                                    <div>
                                    <span class='file-name'>$r[TITRE_RESSOURCE]</span>
                                    <span class='file-size'>$fileSize_KB.mb</span>
                                    </div>
                                    </div>
                                    </td>
                                    <td>$r[NOM_MODULE]</td>
                                    <td>$r[NOM_FILIERE]</td>
                                    <td>$r[NOM_USER] $r[PRENOM_USER]</td>
                                    <td>$r[DATE_UPLOAD]</td>";
                                    if ($role=='admin' || $role=='formateur' || $r['ID_USER'] == $id_user){
                                    echo"<td class='file-actions' data-id='$r[ID_RESSOURCE]'>
                                    <i class='fas fa-ellipsis-v'></i>
                                    </td>";};
                                    echo"<td>
                                    <a href='telecharger.php?chemin=$r[CHEMIN_FICHIER]&id_r=$r[ID_RESSOURCE] '>
                                    <i class='fas fa-download'></i>
                                    </a>";
                                    if ($role=='admin' || ($role=='formateur' && $r['NOM_FILIERE'] == $filiere)){
                                        $stmt = $db->prepare("SELECT COUNT(*) FROM TELECHARGEMENT WHERE ID_RESSOURCE = ?");
                                        $stmt->execute([$r['ID_RESSOURCE']]);
                                        $nb = $stmt->fetchColumn();
                                    echo"<span class='stat'>($nb)</span>";}
                                    echo"</td>
                                    </tr></a>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--ajouter ressource pop-up -->
            <form id="newForumModal" class="modal-overlay" style="display: none" method="post"
                enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="form-group">
                        <?php if(isset($msg["errressourcetitle"])){echo"<div style='color : red;'>$msg[errressourcetitle]</div>";}?>
                        <input type="text" class="input-field" placeholder="Type a title" name="ressourcetitle" />
                    </div>
                    <div class="form-group">
                        <?php if(isset($msg["errfiliere"])){echo"<div style='color : red;'>$msg[errfiliere]</div>";}?>
                        <select id="filterFiliere1" class="select-field" name="filiere">
                            <option value="" disabled selected>filière</option>
                            <?php 
                                include("../../others/code-conexionAvecDB.php");
                                $reqfl =$db->query("SELECT * FROM filiere");
                                $fili = $reqfl->fetchAll(PDO::FETCH_NUM);
                                #fetching dans les options 
                                foreach($fili as $f){
                                echo"<option value='$f[0]'> $f[1]</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?php if(isset($msg["errmodule"])){echo"<div style='color : red;'>$msg[errmodule]</div>";}?>
                        <select id="filterModule1" class="select-field" name="module">
                            <option value="" disabled selected>Select Module</option>
                        </select>
                    </div>
                    <?php if(isset($msg["errfichier"])){echo"<div style='color : red;'>$msg[errfichier]</div>";}?>


                    <div class="button-container">
                        <label class="btn btn-primary" style="cursor: pointer">
                            Importer fichier
                            <input type="file" style="display: none" name="fichier" />
                        </label>
                        <button class="btn btn-publish" type="submit" name="importer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                            Publish
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
    <!-- Filter Modal -->
    <form id="filterModal" class="modal-overlay" style="display: none" method="GET">
        <div class="modal-content">
            <h3>Filter Files</h3>
            <div class="form-group">
                <label for="filterFiliere">Filière</label>
                <select id="filterFiliere" class="select-field" name="filterFiliere">
                    <option value="" disabled selected>Select Filière</option>
                    <?php 
                                include("../../others/code-conexionAvecDB.php");
                                $reqfl =$db->query("SELECT * FROM filiere");
                                $fili = $reqfl->fetchAll(PDO::FETCH_NUM);
                                #fetching dans les options 
                                foreach($fili as $f){
                                echo"<option value='$f[0]'> $f[1]</option>";
                                }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filterModule">Module</label>
                <select id="filterModule" class="select-field" name="filterModule">
                    <option value="">Tous</option>
                </select>
            </div>
            <div class="form-group">
                <label for="filterDate">Date</label>
                <select id="filterDate" class="select-field" name="filterDate">
                    <option value="">Tous</option>
                    <option value="Semaine">Cette Semaine</option>
                    <option value="Mois">Ce Mois</option>
                    <option value="Annee">Cette Annee</option>
                </select>
            </div>
            <div class="button-container">
                <button class="btn btn-primary" id="applyFilter" type="submit">Apply Filter</button>
                <button class="btn btn-secondary" id="closeFilterModal">
                    Cancel
                </button>
            </div>
        </div>
    </form>
    <!--  Scrpt pour ajouter un ressource -->
    <script>
    const modal = document.getElementById("newForumModal");
    const newForumBtn = document.querySelector(".add-button");

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
    <!--  Scrpt pour ajouter un filtre -->
    <script>
    const filterModal = document.getElementById("filterModal");
    const filterButton = document.querySelector(".filter-button");
    const closeFilterModal = document.getElementById("closeFilterModal");

    filterButton.addEventListener("click", () => {
        filterModal.style.display = "flex";
    });

    closeFilterModal.addEventListener("click", () => {
        filterModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === filterModal) {
            filterModal.style.display = "none";
        }
    });

    document.getElementById("applyFilter").addEventListener("click", () => {
        const filiere = document.getElementById("filterFiliere").value;
        const module = document.getElementById("filterModule").value;
        const date = document.getElementById("filterDate").value;

        console.log("Filter applied:", {
            filiere,
            module,
            date
        });
        filterModal.style.display = "none";
    });
    </script>
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
    <!-- Notification scrpt -->

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
                    $reqi=$db->prepare("INSERT into commentaireressource(ID_USER,ID_RESSOURCE,CONTENU_COMMENT,DATE_COMMENTAIRE) values(?,?,?,?)");
                    $r=$reqi->execute([$id_user,$id_ressource,$newComment,$date]);
                    $update=$db->prepare("UPDATE commentaireressource SET ETAT_VALIDATION_C = 'valide' where ID_USER=?");
                    $update->execute([$id_user]);

                    $stmt = $db->prepare("SELECT ID_USER FROM RESSOURCE WHERE ID_RESSOURCE = ?");
                    $stmt->execute([$id_ressource]);
                    $owner_id = $stmt->fetchColumn();
                    if ($owner_id != $id_user) {
                        $notif = $db->prepare("INSERT INTO notification (ID_USER,TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?,?, NOW(),0)");
                        $notif->execute([$owner_id,"💬 $nom $prenom a ajouter un nouveau commentaire sur votre ressource"]);
                    }
                    
                    if ($r) $msgsucces="commentee avec succes";
                    else $msgechec="Erreur dans la publication";
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("INSERT into commentaireressource(ID_USER,ID_RESSOURCE,CONTENU_COMMENT,DATE_COMMENTAIRE) values(?,?,?,?)");
                    $r=$reqi->execute([$id_user,$id_ressource,$newComment,$date]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin'");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "💬 Nouvelle  commentaire à valider auteur($nom $prenom)."]);
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
                    $id_ressource= isset($_GET['id_ressource']) ? $_GET['id_ressource'] : null;
                    if ($id_ressource){
                    $reqc = $db->prepare("SELECT rr.*, u.NOM_USER, u.PRENOM_USER, u.CHEMIN_PHOTO FROM commentaireressource rr
                    JOIN UTILISATEUR u USING(ID_USER)
                    WHERE rr.ETAT_VALIDATION_C = 'valide' and ID_RESSOURCE=?
                    ORDER BY rr.DATE_COMMENTAIRE DESC");
                    $reqc->execute([$id_ressource]);
                    $comments = $reqc->fetchAll(PDO::FETCH_ASSOC);}
                    else $comments = [];
                    foreach ($comments as $comment) {            
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
                            height: 30px;'><strong>$comment[NOM_USER] $comment[PRENOM_USER]</strong>&nbsp;$comment[CONTENU_COMMENT]
                        </div>
                        <div style='display: flex; align-items: center; gap: 9px; flex-direction: row;'>";
                            if ($role === 'admin' || $role === 'formateur' || $comment['ID_USER'] == $id_user)
                            echo"<span class='comment-action' title='Delete'
                                style='cursor: pointer; display: flex; align-items: center;'>
                                <a href='delete_cr.php?id=$comment[ID_COMMENT]'><i class='fa-regular fa-trash-can' style='font-size: 1em; color: #888;'></i></a>
                            </span>";
                        echo"</div>
                    </div>";
                    }
                    ?>
                </div>
                <div class="add-comment">
                    <input type="hidden" name="id_ressource" id="id_forum_input">
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
    document.getElementById('filterFiliere').addEventListener('change', function() {
        const filiereId = this.value;
        const moduleSelect = document.getElementById('filterModule');

        if (!filiereId) {
            // Reset module select if no filiere selected
            moduleSelect.innerHTML = '<option value="Tous">Tous</option>';
            return;
        }

        fetch('getModulesByFiliere.php?filiere_id=' + filiereId)
            .then(response => response.json())
            .then(data => {
                // Clear current options
                moduleSelect.innerHTML = '';

                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Tous';
                moduleSelect.appendChild(defaultOption);

                // Add new options from response
                data.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.ID_MODULE;
                    option.textContent = module.NOM_MODULE;
                    moduleSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching modules:', error);
            });
    });
    </script>

    <script>
    // Modal elements
    const postModal = document.getElementById("postModal");
    const postModalQuestion = document.getElementById("postModalQuestion");
    const closeModalBtn = postModal.querySelector(".close-modal");
    const commentsList = document.getElementById("commentsList");
    const newCommentInput = document.getElementById("newCommentInput");
    const addCommentBtn = document.getElementById("addCommentBtn");

    // Open modal on question title click
    document.querySelectorAll(".file-row").forEach((row) => {
        row.addEventListener("click", function(e) {
            const tds = Array.from(this.querySelectorAll("td"));
            const lastTd = tds[tds.length - 1];
            const secondLastTd = tds[tds.length - 2];
            if (e.target.closest("td") === lastTd || e.target.closest("td") === secondLastTd) {
                return;
            }
            const ressourceId = this.dataset.id_ressource;
            localStorage.setItem("ressourceId", ressourceId);
            window.location.href = `?id_ressource=${ressourceId}`;
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
    document.addEventListener("DOMContentLoaded", function() {
        let ressourceId;

        // 1. Try from localStorage (if clicking from page)
        const storedId = localStorage.getItem("ressourceId");
        if (storedId) {
            ressourceId = storedId;
            localStorage.removeItem("ressourceId");
        } else {
            // 2. Or get from URL
            const urlParams = new URLSearchParams(window.location.search);
            ressourceId = urlParams.get("id_ressource");
        }

        if (ressourceId) {
            const input = document.getElementById("id_forum_input");
            if (input) input.value = ressourceId;

            const row = document.querySelector(`.file-row[data-id_ressource='${ressourceId}']`);

            if (row) {
                postModalQuestion.innerHTML = "";
                const clonedRow = row.cloneNode(true);
                postModalQuestion.appendChild(clonedRow);
                postModal.style.display = "flex";
            } else {
                console.warn("No matching resource found for ID:", ressourceId);
            }
        }
    });
    </script>
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
            const ressourceId = this.dataset.id;
            dropdown.innerHTML = `
        <ul style="list-style:none;margin:0;padding:8px 0;">
            <a class="delete" href="delete-ressource.php?id=${ressourceId}"><li class="file-action-delete" style="padding:8px 24px;cursor:pointer;">Delete</li></a>
            <a class="modify" href="modify-ressource.php?id=${ressourceId}"><li class="file-action-modify" style="padding:8px 24px;cursor:pointer;">Modify</li></a>
        </ul>
      `;
            // Delete action
            dropdown.querySelector(".file-action-delete").onclick = function() {
                if (dropdown.currentRow) dropdown.currentRow.remove();
                dropdown.style.display = "none";
            };
            // Modify action: open #newForumModal
            dropdown.querySelector(".file-action-modify").onclick = function() {
                document.getElementById("newForumModal").style.display = "flex";
                dropdown.style.display = "none";
            };
            // Hide all other dropdowns
            document
                .querySelectorAll(".file-action-dropdown")
                .forEach((d) => (d.style.display = "none"));
            // Position dropdown
            const rect = actionCell.getBoundingClientRect();
            dropdown.style.left = rect.left + window.scrollX + "px";
            dropdown.style.top = rect.bottom + window.scrollY + "px";
            dropdown.style.display = "block";
            // Store current row for actions
            dropdown.currentRow = actionCell.closest("tr");
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
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const rows = document.querySelectorAll(".files-table tbody tr");

        function filterRessources() {
            const value = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const fileName = row.querySelector(".file-name")?.innerText.toLowerCase() || "";
                const filiere = row.children[1]?.innerText.toLowerCase() || "";
                const module = row.children[2]?.innerText.toLowerCase() || "";
                const publicateur = row.children[3]?.innerText.toLowerCase() || "";

                const match = fileName.includes(value) || module.includes(value) || filiere.includes(
                    value) || publicateur.includes(value);

                row.style.display = match ? "table-row" : "none";
            });
        }

        searchInput.addEventListener("input", filterRessources);
    });
    </script>
    <script>
    document.getElementById('filterFiliere1').addEventListener('change', function() {
        const filiereId1 = this.value;
        const moduleSelect1 = document.getElementById('filterModule1');

        if (!filiereId1) {
            // Reset module select if no filiere selected
            moduleSelect1.innerHTML = '<option value="" disabled selected>Select Module</option>';
            return;
        }

        fetch('getModulesByFiliere.php?filiere_id=' + filiereId1)
            .then(response => response.json())
            .then(data => {
                // Clear current options
                moduleSelect1.innerHTML = '';

                // Add new options from response
                data.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.ID_MODULE;
                    option.textContent = module.NOM_MODULE;
                    moduleSelect1.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching modules:', error);
            });
    });
    </script>

    <style>
    .delete,
    .modify {
        all: unset;
    }

    .file-action-dropdown ul li:hover {
        background: #f0f0f0;
    }
    </style>
</body>

</html>