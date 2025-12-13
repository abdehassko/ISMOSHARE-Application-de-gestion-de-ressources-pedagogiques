<?php
if(isset($_GET) && !empty($_GET)){
    include("../../others/code-conexionAvecDB.php");
    #requete de la supprission
    $reqi = $db->prepare("DELETE FROM utilisateur WHERE ID_USER = ?");
    $r = $reqi->execute([$_GET['id_user']]);

    if($r){
        header("Location:/ISMOSHARE/pages/utilisateurs/utilisateurs.php?msgy='utilisateur supprime avec sucess'");
        exit;
    }
    else header("Location:/ISMOSHARE/pages/utilisateurs/utilisateurs.php?msge='erreur de supprission '");
    exit;

}
?>