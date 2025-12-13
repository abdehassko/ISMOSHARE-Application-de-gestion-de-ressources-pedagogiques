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
        $check = $db->prepare("SELECT COUNT(*) FROM REACTION WHERE ID_USER = ? AND ID_REPONSE = ?");
        $check->execute([$id_user, $id_comment]);
        $reacted = $check->fetchColumn();
        if ($reacted) {
            header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Vous avez déjà réagi !");
            exit;
        }
        $reqs = $db->prepare("SELECT REACTION FROM reponseforum WHERE ID_REPONSE = ?");
        $reqs->execute([$id_comment]);
        $post = $reqs->fetchColumn();
        if ($post === false){ header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=reponse not found!!"); exit;}
        $post+=1;
        $reqd = $db->prepare("UPDATE reponseforum set REACTION=? WHERE ID_REPONSE= ?");
        $reqd->execute([$post,$id_comment]);
        $insert = $db->prepare("INSERT INTO REACTION (ID_USER, ID_REPONSE) VALUES (?, ?)");
        $insert->execute([$id_user, $id_comment]);

        $stmt = $db->prepare("SELECT ID_USER FROM REPONSEFORUM  WHERE ID_REPONSE = ?");
        $stmt->execute([$id_comment]);
        $owner_id = $stmt->fetchColumn();
        if ($owner_id != $id_user){
            $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
            $notif->execute([$owner_id, "❤️ $nom $prenom a aime votre commentaire."]);
        }

        header("Location:/ISMOSHARE/pages/forum/forum.php?msgsucces=Réaction ajoutée !"); exit;
    }
    catch (\PDOException $e) {
        echo"erreur selection reactions :".$e->getMessage();
    }
} else {
    header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Invalid Request");
    exit;
}
?>