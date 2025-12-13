<?php
session_start();
include("../../others/code-conexionAvecDB.php");

if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location: /ISMOSHARE/pages/principale/page principale.php");
    exit();
}
extract($_SESSION);

if (isset($_GET['id'])) {
    try{
        $id_annonce = intval($_GET['id']);
        $reqs = $db->prepare("SELECT ID_USER FROM annonce WHERE ID_ANNONCE = ?");
        $reqs->execute([$id_annonce]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/annonce/annonce.php?msgechec=Post not found!!"); exit;}
        if ($role === 'admin'  || $post['ID_USER'] == $id_user) {
            $reqd = $db->prepare("DELETE FROM annonce WHERE ID_ANNONCE= ?");
            $stmt = $db->prepare("SELECT ID_USER FROM ANNONCE  WHERE ID_ANNONCE = ?");
            $stmt->execute([$id_annonce]);
            $owner_id = $stmt->fetchColumn();
            if ($owner_id != $id_user){
                $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                $notif->execute([$owner_id, "🚮 Votre annonce a été supprimee par $nom $prenom."]);
            }
            $reqd->execute([$id_annonce]);
            header("Location:/ISMOSHARE/pages/annonce/annonce.php?msgsucces=Post deleted successfully");
            exit;
        } else {
            header("Location:/ISMOSHARE/pages/annonce/annonce.php?msgechec=Unauthorized");
            exit;
        }
    }
    catch (\PDOException $e) {
        echo"erreur selection produits :".$e->getMessage();
    }
} else {
    header("Location:/ISMOSHARE/pages/annonce/annonce.php?msgechec=Invalid Request");
    exit;
}
?>