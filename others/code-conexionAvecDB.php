<?php
try {
    $db=new PDO("mysql:host=localhost;port=3306;dbname=ismoshare_db","root","1212",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo "Erreur Conexion".$e->getMessage();
}
?>