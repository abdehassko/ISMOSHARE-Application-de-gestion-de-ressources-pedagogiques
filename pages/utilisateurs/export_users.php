<?php
// export_users.php
include("../../others/code-conexionAvecDB.php");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=utilisateurs.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Nom complet</th>
        <th>Email</th>
        <th>Matricule ou CEF</th>
        <th>Status</th>
        <th>Role</th>
        <th>Date d'inscription</th>
      </tr>";

$req = $db->query("SELECT * FROM utilisateur");
$users=$req->fetchAll(PDO::FETCH_ASSOC);

foreach($users as $row){
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['NOM_USER'] . " " . $row['PRENOM_USER']) . "</td>";
    echo "<td>" . htmlspecialchars($row['EMAIL_INSTITUTIONNEL']) . "</td>";
    echo "<td>" . htmlspecialchars($row['MATRICULE_OU_CEF']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ETAT_COMPTE']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ROLE']) . "</td>";
    echo "<td>" . htmlspecialchars($row['inscription_date']) . "</td>";
    echo "</tr>";
}
echo "</table>";
exit();