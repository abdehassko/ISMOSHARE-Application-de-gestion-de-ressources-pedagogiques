<?php
session_start();
include("../../others/code-conexionAvecDB.php");
extract($_GET);
extract($_SESSION);

if ($id_user && $chemin && $id_r) {

    // Convert the URL path (/ISMOSHARE/...) → real server path (C:/xampp/htdocs/ISMOSHARE...)
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $chemin;

    // Register the download
    $reqi = $db->prepare("INSERT INTO TELECHARGEMENT (ID_USER, ID_RESSOURCE, DATE_TEL) VALUES (?, ?, NOW())");
    $reqi->execute([$id_user, $id_r]);

    // Check if file exists on disk
    if (file_exists($fullPath)) {

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));

        readfile($fullPath);
        exit;

    } else {
        echo "Fichier introuvable. Chemin : $fullPath";
    }

} else {
    echo "Paramètres manquants ou utilisateur non connecté.";
}