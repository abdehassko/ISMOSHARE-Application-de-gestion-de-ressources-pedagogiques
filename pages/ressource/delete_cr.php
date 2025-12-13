<?php
session_start();
include("../../others/code-conexionAvecDB.php");

if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
extract($_SESSION);

if (isset($_GET['id'])) {
    try{
        $id_comment = intval($_GET['id']);
        $reqs = $db->prepare("SELECT ID_USER FROM commentaireressource WHERE ID_COMMENT = ?");
        $reqs->execute([$id_comment]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=reponse not found!!"); exit;}
        if ($role === 'admin' || $role === 'formateur' || $post['ID_USER'] == $id_user) {
            $reqd = $db->prepare("DELETE FROM commentaireressource WHERE ID_COMMENT= ?");

            $stmt = $db->prepare("SELECT ID_USER FROM commentaireressource  WHERE ID_COMMENT = ?");
            $stmt->execute([$id_comment]);
            $owner_id = $stmt->fetchColumn();
            if ($owner_id != $id_user){
                $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                $notif->execute([$owner_id, "🚮 Votre commentaire a été supprimee par $nom $prenom."]);
            }

            $reqd->execute([$id_comment]);
            header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgsucces=reponse deleted successfully");
            exit;
        } else {
            header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=Unauthorized");
            exit;
        }
    }
    catch (\PDOException $e) {
        echo"erreur selection produits :".$e->getMessage();
    }
} else {
    header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=Invalid Request");
    exit;
}
?>