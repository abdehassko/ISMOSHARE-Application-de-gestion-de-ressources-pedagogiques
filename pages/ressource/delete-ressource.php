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
        $id_ressource = intval($_GET['id']);
        $reqs = $db->prepare("SELECT ID_USER FROM ressource WHERE ID_RESSOURCE = ?");
        $reqs->execute([$id_ressource]);
        $post = $reqs->fetch(PDO::FETCH_ASSOC);
        if (!$post){ header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgechec=Ressource not found!!"); exit;}
        if ($role === 'admin' || $role === 'formateur' || $post['ID_USER'] == $id_user) {
            $reqd = $db->prepare("DELETE FROM ressource WHERE ID_RESSOURCE= ?");

            $stmt = $db->prepare("SELECT ID_USER FROM ressource  WHERE ID_RESSOURCE = ?");
            $stmt->execute([$id_ressource]);
            $owner_id = $stmt->fetchColumn();
            if ($owner_id != $id_user){
                $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
                $notif->execute([$owner_id, "🚮 Votre ressource a été supprimee par $nom $prenom."]);
            }

            $reqd->execute([$id_ressource]);
            header("Location:/ISMOSHARE/pages/ressource/ressources.php?msgsucces=Ressource deleted successfully");
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