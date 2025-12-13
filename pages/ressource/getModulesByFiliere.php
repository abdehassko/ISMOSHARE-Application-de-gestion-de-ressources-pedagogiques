<?php
include("../../others/code-conexionAvecDB.php");

if (isset($_GET['filiere_id'])) {
    $filiere_id = intval($_GET['filiere_id']);
    $rqs = $db->prepare("
        SELECT m.ID_MODULE, m.NOM_MODULE 
        FROM module m
        JOIN programme p ON m.ID_MODULE = p.ID_MODULE
        WHERE p.ID_FILIERE = ?
    ");
    $rqs->execute([$filiere_id]);
    $modules = $rqs->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($modules);
}
?>