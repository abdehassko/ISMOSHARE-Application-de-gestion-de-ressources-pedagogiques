<?php
session_start();
session_unset();
session_destroy();
header("Location: /ISMOSHARE/pages/connexion/page-conexion.php");
exit;
?>