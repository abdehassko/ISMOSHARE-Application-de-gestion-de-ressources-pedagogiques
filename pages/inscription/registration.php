<?php
include ("../../others/code-conexionAvecDB.php");
$reqs = $db->query("SELECT EMAIL_INSTITUTIONNEL FROM utilisateur");
$EMAIL_INS = $reqs->fetchAll(PDO::FETCH_COLUMN);
$reqs2 = $db->query("SELECT MATRICULE_OU_CEF FROM utilisateur");
$M_OU_CEF = $reqs2->fetchAll(PDO::FETCH_COLUMN);
if ($_SERVER['REQUEST_METHOD']=='POST'){
    extract($_POST);
    extract($_FILES);
    if (isset($envoyer)) {
        $msgerror=[];
        if (!isset($nom) || empty($nom)) $msgerror['errnom']='vous devez entrez votre nom';
        elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nom)) $msgerror['errnom']='vous devez entrez un nom valide';
        if (!isset($prenom) || empty($prenom)) $msgerror['errprenom']='vous devez entrez votre prenom';
        elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $prenom)) $msgerror['errprenom']='vous devez entrez un prenom valide';
        if (!isset($email) || empty($email)) $msgerror['erremail']='vous devez choisir un email';
        elseif (!preg_match("/^[\w\.-]+@ismo.ma$/",$email)) $msgerror['erremail']='vous devez choisir un email sous format anything@ismo.ma';
        elseif(in_array($email,$EMAIL_INS)) $msgerror['erremail'] = "cette email existe deja merci de choisir un email different";
        if (!isset($CEF) || empty($CEF)) $msgerror['errCEF']='vous devez entrez votre CEF ou Matricule';
        elseif(in_array($CEF,$M_OU_CEF)) $msgerror['errCEF'] = "cette CEF ou MATRICULE existe deja";
        elseif(!ctype_digit($CEF)) $msgerror['errCEF']='vous devez entrez un CEF/Matricule valide';
        if (!isset($mdp) || empty($mdp)) $msgerror['errmdp']='vous devez entrez votre mot de passe';
        if (!isset($confmdp) || empty($confmdp)) $msgerror['errconfmdp']='vous devez resaissir votre mot de passe';
        elseif ($confmdp !=$mdp) $msgerror['errconfmdp']='vous devez resaissir le meme mot de passe';
        if (!isset($filiere) || empty($filiere)) $msgerror['errfiliere']='vous devez choisir une filiere';
        if (($img['error']!=0)) $msgerror['errimg']='vous devez charger votre photo';
        elseif(!in_array($img['type'],["image/jpeg","image/jpg","image/png","image/tiff","image/jif","image/heif","image/heic"])) $msgerr['errimg']='extension de votre photo invalid';
        elseif($img['size']>40*1024*1024) $msgerror['errimg']='taille maximal est 40 MO';
        if (empty($msgerror)){
            $mdp=password_hash($mdp,PASSWORD_ARGON2I);
            move_uploaded_file($img['tmp_name'],"..\\..\\uploaded_files\\$img[name]");
            $nom=trim(htmlspecialchars($nom));
            $prenom=trim(htmlspecialchars($prenom));
            $CEF=trim(htmlspecialchars($CEF));
            $email=filter_var($email,FILTER_SANITIZE_EMAIL);
            try {
                $today = date('Y-m-d');
                $insciption=$db->prepare("INSERT into UTILISATEUR(MATRICULE_OU_CEF,NOM_USER,PRENOM_USER,EMAIL_INSTITUTIONNEL,MOT_DE_PASSE,CHEMIN_PHOTO,inscription_date) values (?,?,?,?,?,?,?)");
                $r=$insciption->execute([$CEF,$nom,$prenom,$email,$mdp,"/ISMOSHARE/uploaded_files/$img[name]",$today]);
                if ($r){
                    $reqs = $db->prepare("SELECT ID_USER FROM utilisateur where EMAIL_INSTITUTIONNEL=?");
                    $reqs->execute([$email]);
                    $pp=$reqs->fetch(PDO::FETCH_COLUMN);
                    $prog=$db->prepare("INSERT into MEMBRE(ID_USER,ID_FILIERE) values (?,?)");
                    $p=$prog->execute([$pp,$filiere]);

                    $admins = $db->query("SELECT ID_USER FROM UTILISATEUR WHERE ROLE = 'admin'");
                    foreach ($admins as $admin) {
                        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                        $notif->execute([$admin['ID_USER'], "⚠️ Nouvelle Registration request à valider."]);
                    }
                    
                    $msgsucces="Demande d inscription recue";
                    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php?m=$msgsucces");
                    exit;
                }
                    else echo "<div style='red:red;'>Erreur Donnes non insere </div>";
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
    <title>IsmoShare - Sign Up</title>
    <link rel="icon" href="/ISMOSHARE/assets/logoapp.png" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/registration-style.css" />
</head>

<body>
    <div class="container">
        <div class="form-section">
            <img src="/ISMOSHARE/assets/logo.png" alt="IsmoShare Logo" class="logo" />
            <h2>Sign up to Ismoshare</h2>
            <form method="post" enctype="multipart/form-data">
                <?php if(isset($msgerror["errnom"])){echo"<div style='color : red;'>$msgerror[errnom]</div>";}?>
                <?php if(isset($msgerror["errprenom"])){echo"<div style='color : red;'>$msgerror[errprenom]</div>";}?>
                <div class="name-group">
                    <input type="text" name="nom" placeholder="First name" />
                    <input type="text" name="prenom" placeholder="Last name" />
                </div>
                <?php if(isset($msgerror["erremail"])){echo"<div style='color : red;'>$msgerror[erremail]</div>";}?>
                <input type="email" name="email" placeholder="Email : youremail@ismo.ma" />
                <?php if(isset($msgerror["errCEF"])){echo"<div style='color : red;'>$msgerror[errCEF]</div>";}?>
                <input type="text" name="CEF" placeholder="CEF ou Matricule" />
                <?php if(isset($msgerror["errmdp"])){echo"<div style='color : red;'>$msgerror[errmdp]</div>";}?>
                <input type="password" name="mdp" placeholder="mot de passe" />
                <?php if(isset($msgerror["errconfmdp"])){echo"<div style='color : red;'>$msgerror[errconfmdp]</div>";}?>
                <input type="password" name="confmdp" placeholder="confirmation de mot de passe" />
                <?php if(isset($msgerror["errfiliere"])){echo"<div style='color : red;'>$msgerror[errfiliere]</div>";}?>
                <select name="filiere" id="Filière">
                    <option value="none" selected disabled>Choisissez votre filière</option>
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
                <?php if(isset($msgerror["errimg"])){echo"<div style='color : red;'>$msgerror[errimg]</div>";}?>
                <label for="file">Importer votre image :</label>
                <input type="file" name="img" accept=".jpg,.jpeg,.png" />
                <input type="submit" class="signup-btn" name="envoyer" value="SIGN UP">
                <a href="/ISMOSHARE/pages/connexion/page-conexion.php"><button type="button" class="login-btn">BACK TO
                        LOGIN</button></a>
            </form>
        </div>
        <div class="side-panel">
            <p class="brand">ISMOSHARE</p>
        </div>
    </div>
</body>

</html>