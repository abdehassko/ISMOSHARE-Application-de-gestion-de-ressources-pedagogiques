<?php
session_start();
include("../others/code-conexionAvecDB.php");
$req = $db->prepare("UPDATE NOTIFICATION SET EST_LUE_ = 1 WHERE ID_NOTIFICATION = ? AND ID_USER = ?");
$req->execute([$_GET['id_nf'], $_SESSION['id_user']]);

header("Location: /ISMOSHARE/pages/principale/page principale.php");
?>