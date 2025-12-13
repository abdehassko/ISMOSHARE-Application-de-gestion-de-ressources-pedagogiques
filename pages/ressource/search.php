<?php
if (!empty($_GET['search_r'])) {
  $search = '%' . $_GET['search_r'] . '%';
  $requette .= " AND (
    TITRE_RESSOURCE LIKE ? OR
    NOM_MODULE LIKE ? OR
    NOM_FILIERE LIKE ?
  )";
  $params[] = $search;
  $params[] = $search;
  $params[] = $search;
}
?>