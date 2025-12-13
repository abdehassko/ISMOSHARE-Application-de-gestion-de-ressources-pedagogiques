<?php
session_start();
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    extract($_POST);
    if (isset($connect)) {
        $msg = [];
        if(!isset($email) || empty($email)) $msg['erremail']='vous devez entrez votre email';
        elseif(!preg_match("/^[\w\.-]+@ismo.ma$/",$email)) $msg['erremail']='vous devez entrez votre email valide';
        if(!isset($password) || empty($password)) $msg['errpassword']='vous devez entrez votre mot de passe';
        if(empty($msg)){
            include("../../others/code-conexionAvecDB.php");
            try {
                $reqs=$db->prepare("SELECT * from UTILISATEUR where EMAIL_INSTITUTIONNEL=? ");
                $reqs->execute([$email]);
                $info_user=$reqs->fetch(PDO::FETCH_ASSOC);
                if ($info_user) {
                    if (password_verify($password,$info_user["MOT_DE_PASSE"])){
                        if ($info_user["ETAT_COMPTE"]=="valide") {
                            $_SESSION['etat'] = $info_user['ETAT_COMPTE'];
                            $_SESSION['id_user'] = $info_user['ID_USER'];
                            $_SESSION['etat'] = $info_user['ETAT_COMPTE'];
                            $_SESSION['prenom'] = $info_user['PRENOM_USER'];
                            $_SESSION['nom'] = $info_user['NOM_USER'];
                            $_SESSION['role'] = $info_user['ROLE'];
                            $_SESSION['photo'] = $info_user['CHEMIN_PHOTO'];
                            $_SESSION['email'] = $info_user['EMAIL_INSTITUTIONNEL'];
                            $reqsf=$db->prepare("SELECT m.*,f.* from membre m join filiere f using(ID_FILIERE) where m.ID_USER=? ");
                            $reqsf->execute([$info_user['ID_USER']]);
                            $filiere=$reqsf->fetch(PDO::FETCH_ASSOC);
                            $_SESSION['filiere'] = $filiere['NOM_FILIERE'];
                            header("Location:/ISMOSHARE/pages/principale/page principale.php");
                            exit;
                        }
                        elseif ($info_user["ETAT_COMPTE"]=="en attente") $msg["errconex"]="Votre compte est en cours de validation";
                        else $msg["errconex"]="Votre compte est bloque.";
                    }
                    else $msg["errconex"]="Email ou mot de passe incorrect.";
                }
                else $msg["errconex"]="Utilisateur introuvable.";
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
    <title>IsmoShare - Login</title>
    <link rel="icon" href="/ISMOSHARE/assets/logoapp.png" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/login-style.css" />
</head>

<body>
    <div class="container">
        <div class="left-section">
            <img src="/ISMOSHARE/assets/logo.png" alt="IsmoShare Logo" class="logo" />
            <h1>Welcome Back!</h1>
            <?php if(isset($_GET['mod'])){echo"<div style='color : green;'>$_GET[mod]</div>";}?>
            <?php if(isset($_GET['m'])){echo"<div style='color : green;'>$_GET[m]</div>";}?>
            <?php if(isset($msg["errconex"])){echo"<div style='color : red;'>$msg[errconex]</div>";}?>
            <form method="post">
                <label>Sign in</label>
                <?php if(isset($msg["erremail"])){echo"<div style='color : red;'>$msg[erremail]</div>";}?>
                <input type="email" placeholder="youremail@ismo.ma" name="email" />
                <?php if(isset($msg["errpassword"])){echo"<div style='color : red;'>$msg[errpassword]</div>";}?>
                <input type="password" placeholder="Password *" name="password" />
                <div class="links">
                    <button type="submit" class="login-btn" name="connect">LOG IN ➝</button>
                    <a href="#">Forgot your password?</a>
                </div>
                <a href="/ISMOSHARE/pages/inscription/registration.php"><button type="button" class="signup-btn">
                        DON'T HAVE AN ACCOUNT? SIGN UP
                    </button></a>
            </form>
        </div>

        <div class="right-section">
            <div class="quote-box">
                <p class="quote-mark">“</p>
                <p class="quote-word">PARTAGER</p>
                <p class="quote-word">ECHANGER</p>
                <p class="quote-word">REUSSIR</p>
                <p class="at">@ismoShare</p>
            </div>
        </div>
    </div>
</body>

</html>