<?php
session_start();
include("code-conexionAvecDB.php");
if (!isset($_SESSION['id_user'])){
    header("Location: /ISMOSHARE/pages/principale/page principale.php");
    exit;
}
elseif ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'formateur' ) {
    header("Location: /ISMOSHARE/others/non-autorise.php");
    exit;
}
extract($_SESSION);
if (isset($_GET['idr'])) {
    try{
        $id_ressource = intval($_GET['idr']);
        $reqs = $db->prepare("SELECT ID_RESSOURCE FROM ressource WHERE ID_RESSOURCE = ?");
        $reqs->execute([$id_ressource]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Ressource not found!!"); exit;}
        if ($role === 'admin' || $role === 'formateur' ) {
            $requ = $db->prepare("UPDATE ressource set ETAT_VALIDATION_R='valide' WHERE ID_RESSOURCE= ?");
            $requ->execute([$id_ressource]);

            $stmt = $db->prepare("SELECT ID_USER FROM RESSOURCE WHERE ID_RESSOURCE = ?");
            $stmt->execute([$id_ressource]);
            $owner_id = $stmt->fetchColumn();
            $notif = $db->prepare("INSERT INTO notification (ID_USER,TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?,?, NOW(),0)");
            $notif->execute([$owner_id,"✔️​ Votre demande de publication d une ressource a ete acceptee "]);
            
            header("Location:/ISMOSHARE/pages/validation/validation.php?msgsucces=Ressource accepted successfully");
            exit;
        } else {
            header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Unauthorized");
            exit;
        }
    }
    catch (\PDOException $e) {
        echo"erreur selection produits :".$e->getMessage();
    }
}
elseif (isset($_GET['idf'])){
    try{
        $id_forum = intval($_GET['idf']);
        $reqs = $db->prepare("SELECT ID_FORUM FROM forum WHERE ID_FORUM = ?");
        $reqs->execute([$id_forum]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Forum not found!!"); exit;}
        if ($role === 'admin' || $role === 'formateur') {
            $requ = $db->prepare("UPDATE forum set ETAT_VALIDATION_F='valide' WHERE ID_FORUM= ?");
            $requ->execute([$id_forum]);

            $stmt = $db->prepare("SELECT ID_USER FROM forum WHERE ID_FORUM = ?");
            $stmt->execute([$id_forum]);
            $owner_id = $stmt->fetchColumn();
            $notif = $db->prepare("INSERT INTO notification (ID_USER,TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?,?, NOW(),0)");
            $notif->execute([$owner_id,"✔️​ Votre demande de publication d un sujet forum a ete acceptee "]);

            header("Location:/ISMOSHARE/pages/validation/validation.php?msgsucces=Forum accepted successfully");
            exit;
        } else {
            header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Unauthorized");
            exit;
        }
    }
    catch (\PDOException $e) {
        echo"erreur selection produits :".$e->getMessage();
    }
}
elseif (isset($_GET['idcr'])){
    try{
        $id_cr = intval($_GET['idcr']);
        $reqs = $db->prepare("SELECT ID_COMMENT FROM commentaireressource WHERE ID_COMMENT = ?");
        $reqs->execute([$id_cr]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=commentaire not found!!"); exit;}
        if ($role === 'admin' || $role === 'formateur') {
            $requ = $db->prepare("UPDATE commentaireressource set ETAT_VALIDATION_C='valide' WHERE ID_COMMENT= ?");
            $requ->execute([$id_cr]);
            
            $stmt = $db->prepare("SELECT ID_USER FROM commentaireressource WHERE ID_COMMENT = ?");
            $stmt->execute([$id_cr]);
            $owner_id = $stmt->fetchColumn();
            $notif = $db->prepare("INSERT INTO notification (ID_USER,TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?,?, NOW(),0)");
            $notif->execute([$owner_id,"✔️​ Votre demande de publier un commentaire a ete acceptee "]);

            header("Location:/ISMOSHARE/pages/validation/validation.php?msgsucces=commentaire accepted successfully");
            exit;
        } else {
            header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Unauthorized");
            exit;
        }
    }
    catch (\PDOException $e) {
        echo"erreur selection produits :".$e->getMessage();
    }
}
elseif (isset($_GET['idcf'])){
    try{
        $id_cf = intval($_GET['idcf']);
        $reqs = $db->prepare("SELECT ID_REPONSE FROM reponseforum WHERE ID_REPONSE = ?");
        $reqs->execute([$id_cf]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=commentaire not found!!"); exit;}
        if ($role === 'admin' || $role === 'formateur') {
            $requ = $db->prepare("UPDATE reponseforum set ETAT_VALIDATION_C='valide' WHERE ID_REPONSE= ?");
            $requ->execute([$id_cf]);

            $stmt = $db->prepare("SELECT ID_USER FROM reponseforum WHERE ID_REPONSE = ?");
            $stmt->execute([$id_cf]);
            $owner_id = $stmt->fetchColumn();
            $notif = $db->prepare("INSERT INTO notification (ID_USER,TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?,?, NOW(),0)");
            $notif->execute([$owner_id,"✔️​ Votre demande de publier un commentaire a ete acceptee "]);

            header("Location:/ISMOSHARE/pages/validation/validation.php?msgsucces=commentaire accepted successfully");
            exit;
        } else {
            header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Unauthorized");
            exit;
        }
    }
    catch (\PDOException $e) {
        echo"erreur selection produits :".$e->getMessage();
    }
}
else {
    header("Location:/ISMOSHARE/pages/validation/validation.php?msgechec=Invalid Request");
    exit;
}
?>