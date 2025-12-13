<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
else {
    include("../../others/code-conexionAvecDB.php");
    extract($_SESSION); 
    if (isset($_GET["id"])) {
        $reqm = $db->prepare("SELECT r.*,f.NOM_FILIERE,m.NOM_MODULE from ressource r JOIN filiere f USING(ID_FILIERE)
        JOIN module m USING(ID_MODULE) where ID_RESSOURCE=?");
        $reqm->execute([$_GET["id"]]);
        $modress=$reqm->fetch(PDO::FETCH_NUM);
    }
    $reqRessource = $db->query("SELECT r.*, u.NOM_USER, u.PRENOM_USER,f.NOM_FILIERE,m.NOM_MODULE FROM RESSOURCE r
    JOIN UTILISATEUR u USING(ID_USER)
    JOIN filiere f USING(ID_FILIERE)
    JOIN module m USING(ID_MODULE)
    WHERE r.ETAT_VALIDATION_R = 'valide'
    ORDER BY r.DATE_UPLOAD DESC");
    $ressources = $reqRessource->fetchAll(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    $msg = [];
    if(!isset($ressourcetitle) || empty($ressourcetitle)) $msg['errressourcetitle']='vous devez choisir un titre';
    if(!isset($filiere) || empty($filiere)) $msg['errfiliere']='vous devez choisir un filiere';
    if(!isset($module) || empty($module)) $msg['errmodule']='vous devez choisir une module';  
    extract($_FILES);
    if (($fichier['error']==0)){
        if(!in_array($fichier['type'],["image/jpeg","image/jpg","image/png","image/tiff","image/jif","image/heif","image/heic","application/pdf","application/msword"
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
                    $reqi=$db->prepare("UPDATE ressource set ID_USER=?,TITRE_RESSOURCE=?,ID_MODULE=?,ID_FILIERE=?,CHEMIN_FICHIER=?,DATE_UPLOAD=? where ID_RESSOURCE=?");
                    $r=$reqi->execute([$id_user,$ressourcetitle,$module,$filiere,"/ISMOSHARE/uploaded_files/$fichier[name]",$date,$_GET["id"]]);
                    if ($r) {header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgsucces=modification avec succes");exit;}
                    else header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=modification avec succes");exit;
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("UPDATE ressource set ID_USER=?,TITRE_RESSOURCE=?,ID_MODULE=?,ID_FILIERE=?,CHEMIN_FICHIER=?,DATE_UPLOAD=? where ID_RESSOURCE=?");
                    $r=$reqi->execute([$id_user,$ressourcetitle,$module,$filiere,"/ISMOSHARE/uploaded_files/$fichier[name]",$date,$_GET["id"]]);
                    $update=$db->prepare("UPDATE ressource SET ETAT_VALIDATION_R = 'en attente' where ID_RESSOURCE=?");
                    $update->execute([$_GET['id']]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin' or ROLE = 'formateur' ");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "✍🏻 $nom $prenom a demande de modifier son ressource"]);
                    }

                    if ($r) {header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgsucces=Demande modification evoyee vers l admin");exit;}
                    else header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=modification avec succes");exit;
                }
            }
            catch (PDOException $e) {
                echo "Erreur inserstion".$e->getMessage();
            }
        }
    }
        else{
            if(empty($msg)){
                $ressourcetitle=htmlspecialchars($ressourcetitle);
                try {
                    if ($role=='admin' || $role=='formateur') {
                        date_default_timezone_set('Africa/Casablanca');
                        $date = date("Y-m-d");
                        $reqi=$db->prepare("UPDATE ressource set ID_USER=?,TITRE_RESSOURCE=?,ID_MODULE=?,ID_FILIERE=?,DATE_UPLOAD=? where ID_RESSOURCE=?");
                        $r=$reqi->execute([$id_user,$ressourcetitle,$module,$filiere,$date,$_GET["id"]]);
                        if ($r) {header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgsucces=modification avec succes");exit;}
                        else header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=modification avec succes");exit;
                    }
                    else {
                        date_default_timezone_set('Africa/Casablanca');
                        $date = date("Y-m-d");
                        $reqi=$db->prepare("UPDATE ressource set ID_USER=?,TITRE_RESSOURCE=?,ID_MODULE=?,ID_FILIERE=?,DATE_UPLOAD=? where ID_RESSOURCE=?");
                        $r=$reqi->execute([$id_user,$ressourcetitle,$module,$filiere,$date,$_GET["id"]]);
                        $update=$db->prepare("UPDATE ressource SET ETAT_VALIDATION_R = 'en attente' where ID_RESSOURCE=?");
                        $update->execute([$_GET['id']]);

                        $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin' or ROLE = 'formateur' ");
                        foreach ($admins as $admin) {
                            $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                            $notif->execute([$admin['ID_USER'], "✍🏻 $nom $prenom a demande de modifier son ressource"]);
                        }

                        if ($r) {header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgsucces=Demande modification evoyee vers l admin");exit;}
                        else header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=modification avec succes");exit;
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
                    <input type="text" id="searchInput" placeholder="Search filiere publicateur..." />
                </div>
                <div class="user-menu">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">1</span>
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
                <input type="text" placeholder="Search files and folders..." />
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
                            $count=$db->query("SELECT count(*) from ressource");
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
            <form id="newForumModal" class="modal-overlay" style="display: flex;align-self:center;" method="post"
                enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="form-group">
                        <?php if(isset($msg["errressourcetitle"])){echo"<div style='color : red;'>$msg[errressourcetitle]</div>";}?>
                        <input type="text" class="input-field" placeholder="Type a title" name="ressourcetitle"
                            value="<?=$modress[4]?>" />
                    </div>
                    <div class=" form-group">
                        <?php if(isset($msg["errfiliere"])){echo"<div style='color : red;'>$msg[errfiliere]</div>";}?>
                        <select class="select-field" id="filterFiliere1" name="filiere" value="<?=$modress[4]?>">
                            <option value="" disabled selected>filière</option>
                            <?php 
                                include("../../others/code-conexionAvecDB.php");
                                $reqfl =$db->query("SELECT * FROM filiere");
                                $fili = $reqfl->fetchAll(PDO::FETCH_NUM);
                                #fetching dans les options 
                                foreach($fili as $f){
                                    $s="";
                                    if($modress[8]==$f[1]) $s="selected";
                                    echo"<option value='$f[0]' $s> $f[1]</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <?php if(isset($msg["errmodule"])){echo"<div style='color : red;'>$msg[errmodule]</div>";}?>
                        <select id="filterModule1" class="select-field" name="module">
                            <option value="" disabled selected>module</option>
                            <?php 
                                include("../../others/code-conexionAvecDB.php");
                                $reqmod =$db->query("SELECT * FROM module");
                                $modu = $reqmod->fetchAll(PDO::FETCH_NUM);
                                #fetching dans les options 
                                foreach($modu as $m){
                                    $s="";
                                    if($modress[9]==$m[1]) $s="selected";
                                    echo"<option value='$m[0]' $s> $m[1]</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <?php if(isset($msg["errfichier"])){echo"<div style='color : red;'>$msg[errfichier]</div>";}?>


                    <div class="button-container">
                        <label class="btn btn-primary" style="cursor: pointer">
                            Modifier fichier
                            <input type="file" style="display: none" name="fichier" />
                        </label>
                        <button class="btn btn-publish" type="submit" name="importer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                            Modifier
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
                    window.location.href = "/ISMOSHARE/pages/ressource/ressources.php";
                }
            });
            <?php if (!empty($msg)) echo "modal.style.display = 'flex'; "?>
            </script>
        </main>
    </div>
    <!-- Filter Modal -->
    <form id="filterModal" class="modal-overlay" style="display: none" method="post">
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
                    <option value="" disabled selected>Select Module</option>
                    <?php 
                        include("../../others/code-conexionAvecDB.php");
                        $reqmod =$db->query("SELECT * FROM module");
                        $modu = $reqmod->fetchAll(PDO::FETCH_NUM);
                        #fetching dans les options 
                        foreach($modu as $m){
                        echo"<option value='$m[0]'> $m[1]</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="filterDate">Date</label>
                <input type="date" id="filterDate" class="select-field" name="filterDate" />
            </div>
            <div class="button-container">
                <button class="btn btn-primary" id="applyFilter">Apply Filter</button>
                <button class="btn btn-secondary" id="closeFilterModal" type="submit">
                    Cancel
                </button>
            </div>
        </div>
    </form>
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
                    if ($r) $msgsucces="commentee avec succes";
                    else $msgechec="Erreur dans la publication";
                }
                else {
                    date_default_timezone_set('Africa/Casablanca');
                    date_default_timezone_set('Africa/Casablanca');
                    $date = date("Y-m-d");
                    $reqi=$db->prepare("INSERT into commentaireressource(ID_USER,ID_RESSOURCE,CONTENU_COMMENT,DATE_COMMENTAIRE) values(?,?,?,?)");
                    $r=$reqi->execute([$id_user,$id_ressource,$newComment,$date]);
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