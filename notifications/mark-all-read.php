<?php
session_start();
include("../others/code-conexionAvecDB.php");

$reqs= $db->prepare("SELECT * from NOTIFICATION WHERE ID_USER = ?");
$reqs->execute([$_SESSION['id_user']]);
$r=$reqs->fetchAll(PDO::FETCH_ASSOC);
if($r){
    $req = $db->prepare("UPDATE NOTIFICATION SET EST_LUE_ = 1 WHERE ID_USER = ?");
    $req->execute([$_SESSION['id_user']]);
}

header("Location: /ISMOSHARE/pages/principale/page principale.php");
?>