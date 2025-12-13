<?php
/*include("ismoconnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST); 

     if (isset($ID_USER) && isset($action)) {
        if ($action === 'valide' || $action === 'refusé') {
            $sql = "UPDATE utilisateur SET ETAT_COMPTE = ? WHERE ID_USER = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$action, $ID_USER]);
        }
        if ($status === 'refusé') {
        // Delete user from database
        $stmt = $db->prepare("DELETE FROM utilisateur WHERE id = ?");
        $stmt->execute([$ID_USER]);
    }
    }}

header("../pages/utilisateurs/utilisateurs.php");
exit();*/


include("../../others/code-conexionAvecDB.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST); // Extracts $ID_USER and $action

    if (isset($ID_USER) && isset($action)) {
        if ($action === 'refusé') {
            // Delete user if status is "refusé"
            $stmt = $db->prepare("DELETE FROM utilisateur WHERE ID_USER = ?");
            $stmt->execute([$ID_USER]);
        } elseif ($action === 'valide') {
            // Only update status if it's "valide"
            $sql = "UPDATE utilisateur SET ETAT_COMPTE = ? WHERE ID_USER = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$action, $ID_USER]);
        }
    }
}

header("Location:/ISMOSHARE/pages/utilisateurs/utilisateurs.php");
exit();
?>