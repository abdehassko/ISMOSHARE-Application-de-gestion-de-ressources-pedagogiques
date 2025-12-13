<?php
session_start();
extract($_SESSION);
include("../../others/code-conexionAvecDB.php");
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit();
}
if (!isset($_GET['id'])) {
    header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Invalid Request");
    exit;
}

$id_reponse = intval($_GET['id']);
$reqs = $db->prepare("SELECT ID_FORUM FROM REPONSEFORUM WHERE ID_REPONSE = ?");
$reqs->execute([$id_reponse]);
$id_forum = $reqs->fetchColumn();
if (!$id_forum) {
    header("Location:/ISMOSHARE/pages/forum/forum.php?msgechec=Response not found");
    exit;
}
try {
    $db->prepare("UPDATE REPONSEFORUM SET MEILLEUR_REPONSE = 0 WHERE ID_FORUM = ?")->execute([$id_forum]);
    $db->prepare("UPDATE REPONSEFORUM SET MEILLEUR_REPONSE = 1 WHERE ID_REPONSE = ?")->execute([$id_reponse]);
    
    $stmt = $db->prepare("SELECT ID_USER FROM REPONSEFORUM  WHERE ID_REPONSE = ?");
    $stmt->execute([$id_reponse]);
    $owner_id = $stmt->fetchColumn();
    if ($owner_id != $id_user){
        $notif = $db->prepare("INSERT INTO NOTIFICATION (ID_USER, TEXTE_NOTIFICATION,DATE_NOTIFICATION,EST_LUE_) VALUES (?, ?, NOW(),0)");
        $notif->execute([$owner_id, "🏆 Votre commentaire a été marqué comme le meilleur par $nom $prenom."]);
    }    
    header("Location:/ISMOSHARE/pages/forum/forum.php?msgsucces=Marked as best response"); exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>