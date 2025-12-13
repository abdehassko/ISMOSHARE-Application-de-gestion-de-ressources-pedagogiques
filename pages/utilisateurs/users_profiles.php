<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
elseif ($_SESSION['role'] !== 'admin') {
    header("Location:/ISMOSHARE/others/non-autorise.php");
    exit;
}
else {
    extract($_SESSION);
    extract($_GET);
    include("../../others/code-conexionAvecDB.php");
    $reqm = $db->prepare("SELECT * from utilisateur where ID_USER=?");
    $reqm->execute([$idp]);
    $modpro=$reqm->fetch(PDO::FETCH_NUM);
    $reqs = $db->prepare("SELECT EMAIL_INSTITUTIONNEL FROM utilisateur WHERE ID_USER != ? ");
    $reqs->execute([$id_user]);
    $EMAIL_INS = $reqs->fetchAll(PDO::FETCH_COLUMN);
    $reqs2 = $db->query("SELECT MATRICULE_OU_CEF FROM utilisateur");
    $M_OU_CEF = $reqs2->fetchAll(PDO::FETCH_COLUMN);
    $reqs3 = $db->prepare("SELECT MOT_DE_PASSE FROM utilisateur WHERE ID_USER = ? ");
    $reqs3->execute([$id_user]);
    $M = $reqs3->fetch(PDO::FETCH_COLUMN);
}
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    $msgerror=[];
    if (!isset($Name) || empty($Name)) $msgerror['errName']='vous devez entrez votre nom';
    elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $Name)) $msgerror['errName']='vous devez entrez un nom valide';
    if (!isset($lastname) || empty($lastname)) $msgerror['errlastname']='vous devez entrez votre prenom';
    elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $lastname)) $msgerror['errlastname']='vous devez entrez un prenom valide';
    if (!isset($Email) || empty($Email)) $msgerror['errEmail']='vous devez choisir un email';
    elseif (!preg_match("/^[\w\.-]+@ismo.ma$/",$Email)) $msgerror['errEmail']='vous devez choisir un email sous format anything@ismo.ma';
    elseif(in_array($Email, $EMAIL_INS) && $Email != $modpro[5]) $msgerror['errEmail'] = "cette email existe deja merci de choisir un email different";
    if (!isset($Filière) || empty($Filière)) $msgerror['errFilière']='vous devez choisir une filiere';
    if (!isset($etat) || empty($etat)) $msgerror['erretat']='vous devez choisir une etat';
    if (!isset($roles) || empty($roles)) $msgerror['errroles']='vous devez choisir un role';
    extract($_FILES);
    if (($image['error']==0)){
        if($image['size']>40*1024*1024) $msgerror['errimage']='taille maximal est 40 MO'; 
        elseif(empty($msgerror)){
            move_uploaded_file($image['tmp_name'],"..\\..\\uploaded_files\\$image[name]");
            $Name=trim(htmlspecialchars($Name));
            $lastname=trim(htmlspecialchars($lastname));
            $Email=filter_var($Email,FILTER_SANITIZE_EMAIL);
            try {
                    $insciption=$db->prepare("UPDATE UTILISATEUR set NOM_USER=?,PRENOM_USER=?,EMAIL_INSTITUTIONNEL=?,CHEMIN_PHOTO=?,ROLE=?,ETAT_COMPTE=? where ID_USER =?");
                    $r=$insciption->execute([$Name,$lastname,$Email,"/ISMOSHARE/uploaded_files/$image[name]",$roles,$etat,$idp]);
                    if ($r){
                        $delete = $db->prepare("DELETE FROM MEMBRE WHERE ID_USER = ?");
                        $delete->execute([$idp]);
                        $prog=$db->prepare("INSERT INTO MEMBRE(ID_USER, ID_FILIERE) VALUES (?, ?)");
                        $p=$prog->execute([$idp,$Filière]);

                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$idp, "✍🏻 $nom $prenom a modifier des infos sur votre profile"]);

                        $msgsucces="Modification done!";
                    }
                        else $msgecche= "Erreur Modification Donnes non insere"; 
                }
            catch (PDOException $e) {
                echo "Erreur inserstion".$e->getMessage();
            }
        }     
    } 
    else{
        if(empty($msgerror)){
            $Name=trim(htmlspecialchars($Name));
            $lastname=trim(htmlspecialchars($lastname));
            $Email=filter_var($Email,FILTER_SANITIZE_EMAIL);
            try {
                    $insciption=$db->prepare("UPDATE UTILISATEUR set NOM_USER=?,PRENOM_USER=?,EMAIL_INSTITUTIONNEL=?,ROLE=?,ETAT_COMPTE=? WHERE ID_USER = ?");
                    $r=$insciption->execute([$Name,$lastname,$Email,$roles,$etat,$idp]);
                    if ($r){
                        $delete = $db->prepare("DELETE FROM MEMBRE WHERE ID_USER = ?");
                        $delete->execute([$idp]);
                        $prog=$db->prepare("INSERT INTO MEMBRE(ID_USER, ID_FILIERE) VALUES (?, ?)");
                        $p=$prog->execute([$idp,$Filière]);

                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$idp, "✍🏻 $nom $prenom a modifier des infos sur votre profile"]);
                        
                        $msgsucces="Modification effectuee avec succes";
                    }
                        else $msgecche= "Erreur Modification Donnes non insere";
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
    <link rel="stylesheet" href="/ISMOSHARE/styles/profile.css" />
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
                <a href="chat.php" class="menu-item">
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
            <div class="containerp">
                <h2 class="welcome-header">Modifiction Profile : <?=$modpro[3]?> <?=$modpro[4]?></h2>
                <p class="date"><?=date('l d F Y');?></p>

                <div class="gradient-bar"></div>

                <div class="profile-section">
                    <img src="<?= $modpro[9] ?>" class="profile-image" />
                    <div class="profile-infop">
                        <h3 class="profile-namep"><?=$modpro[3]?> <?=$modpro[4]?></h3>
                        <p class="profile-emailp"><?=$modpro[5]?></p>
                    </div>
                    <button class="edit-button" type="submit" name="Edit" form="editForm">Edit</button>
                </div>
                <?php if(isset($msgsucces)){echo"<div style='color : green;'>$msgsucces</div>";}?>
                <?php if(isset($msgecche)){echo"<div style='color : red;'>$msgecche</div>";}?>
                <form id="editForm" class="form-grid" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <?php if(isset($msgerror["errName"])){echo"<div style='color : red;'>$msgerror[errName]</div>";}?>
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" placeholder="Your First Name" name="Name"
                            value="<?=$modpro[3]?>" />
                    </div>

                    <div class="form-group">
                        <?php if(isset($msgerror["errlastname"])){echo"<div style='color : red;'>$msgerror[errlastname]</div>";}?>
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" placeholder="Your Last Name" name="lastname"
                            value="<?=$modpro[4]?>" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <div class="select-wrapper">
                            <?php if(isset($msgerror["errroles"])){echo"<div style='color : red;'>$msgerror[errroles]</div>";}?>
                            <select class="select-field" name="roles" value="<?=$modpro[7]?>">
                                <?php
                                if ($modpro[7]=="admin") {
                                    echo"<option value='$modpro[7]' selected>$modpro[7]</option>
                                        <option value='formateur' >Formateur</option>
                                        <option value='stagiaire' >Stagiaire</option>";
                                } elseif($modpro[7]=="formateur") {
                                    echo"<option value='$modpro[7]' selected>$modpro[7]</option>
                                        <option value='admin' >Admin</option>
                                        <option value='stagiaire' >Stagiaire</option>";
                                }
                                elseif($modpro[7]=="stagiaire") {
                                    echo"<option value='$modpro[7]' selected>$modpro[7]</option>
                                        <option value='admin' >Admin</option>
                                        <option value='formateur' >Formateur</option>";
                                }
                                else {
                                    echo"<option value='' selected disabled>Attribuer un role</option>
                                        <option value='admin' >Admin</option>
                                        <option value='formateur' >Formateur</option>
                                        <option value='stagiaire' >Stagiaire</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Filière</label>
                        <div class="select-wrapper">
                            <?php if(isset($msgerror["errFilière"])){echo"<div style='color : red;'>$msgerror[errFilière]</div>";}?>
                            <select class="select-field" name="Filière">
                                <?php
                                $reqsf=$db->query("SELECT * from filiere");
                                $ff=$reqsf->fetchAll(PDO::FETCH_NUM);
                                $s=$db->prepare("SELECT m.*,f.NOM_FILIERE from membre m join FILIERE f using(ID_FILIERE) where ID_USER=?");
                                $s->execute([$id_user]);
                                $fel=$s->fetch(PDO::FETCH_ASSOC);
                                foreach($ff as $f){
                                    $s="";
                                    if ($f[1]==$fel["NOM_FILIERE"]) $s="selected";
                                    echo"<option value='$f[0]' $s>$f[1]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php if(isset($msgerror["errEmail"])){echo"<div style='color : red;'>$msgerror[errEmail]</div>";}?>
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="Email" name="Email"
                            value="<?=$modpro[5]?>" />
                    </div>
                    <div class="form-group">
                        <?php if(isset($msgerror["erretat"])){echo"<div style='color : red;'>$msgerror[erretat]</div>";}?>
                        <label class="form-label">Etat de Compte</label>
                        <select class="select-field" name="etat" value=<?="$modpro[8]"?>>
                            <?php
                                if ($modpro[8]=="en attente") {
                                    echo"<option value='$modpro[8]' selected>$modpro[8]</option>
                                        <option value='valide'>Valide</option>";
                                }
                                else {
                                    echo"<option value='$modpro[8]' selected>$modpro[8]</option>
                                        <option value='en attente'>En Attente</option>";
                                }
                                ?>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2">
                        <?php if(isset($msgerror["errimage"])){echo"<div style='color : red;'>$msgerror[errimage]</div>";}?>
                        <label class="form-label">Nouveau profile image</label>
                        <input type="file" class="form-control" name="image" />
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>