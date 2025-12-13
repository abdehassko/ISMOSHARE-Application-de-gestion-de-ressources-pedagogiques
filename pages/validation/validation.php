<?php
session_start();
if (!isset($_SESSION['id_user'])){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit;
}
elseif ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'formateur') {
    header("Location:/ISMOSHARE/others/non-autorise.php");
    exit;
}
else {
    include("../../others/code-conexionAvecDB.php");
    extract($_SESSION);
    $reqress = $db->query("SELECT u.*,r.* FROM UTILISATEUR u
    JOIN RESSOURCE r USING(ID_USER)
    WHERE r.ETAT_VALIDATION_R = 'en attente'
    ORDER BY r.DATE_UPLOAD DESC");
    $ressources = $reqress->fetchAll(PDO::FETCH_ASSOC);
    
    $reqforum = $db->query("SELECT u.*,f.* FROM UTILISATEUR u
    JOIN FORUM f USING(ID_USER)
    WHERE f.ETAT_VALIDATION_F = 'en attente'
    ORDER BY f.DATE_PUB_FORUM DESC");
    $forums = $reqforum->fetchAll(PDO::FETCH_ASSOC);

    $reqannonce = $db->query("SELECT u.*,a.* FROM UTILISATEUR u
    JOIN ANNONCE a USING(ID_USER)
    WHERE a.ETAT_VALIDATION_A = 'en attente'
    ORDER BY a.DATE_ANNONCE DESC");
    $annonces = $reqannonce->fetchAll(PDO::FETCH_ASSOC);

    $reqcr = $db->query("SELECT u.*,cr.* FROM UTILISATEUR u
    JOIN commentaireressource cr USING(ID_USER)
    WHERE cr.ETAT_VALIDATION_C = 'en attente'");
    $commentR = $reqcr->fetchAll(PDO::FETCH_ASSOC);

    $reqcf = $db->query("SELECT u.*,cf.* FROM UTILISATEUR u
    JOIN reponseforum cf USING(ID_USER)
    WHERE cf.ETAT_VALIDATION_C = 'en attente'");
    $commentF = $reqcf->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISMOSHARE Forum</title>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" href="/ISMOSHARE/assets/logoapp.png" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/forum-style.css" />
    <link rel="stylesheet" href="/ISMOSHARE/styles/validation-style.css" />
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <a href="/ISMOSHARE/pages/principale/page principale.php"><img src="/ISMOSHARE/assets/logo.png" /></a>
            </div>

            <div class="profile">
                <img src="<?= $photo ?>" width="60" />
                <div class="profile-info">
                    <p class="profile-name"><?=$nom?> <?=$prenom?></p>
                    <p class="profile-role">
                        <span class="online-dot"></span><?=$role?>
                    </p>
                </div>
            </div>

            <nav class="menu">
                <a href="/ISMOSHARE/pages/forum/forum.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/icons8-forum-20.png" />
                    <p>&nbsp;&nbsp;&nbsp; Forum</p>
                </a>
                <a href="/ISMOSHARE/pages/annonce/annonce.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/icons8-annonce-20.png" />&nbsp;
                    <p>Annonces</p>
                </a>
                <a href="/ISMOSHARE/pages/ressource/ressources.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/Vector-2.png" />&nbsp;
                    <p>Ressources</p>
                </a>
                <a href="/ISMOSHARE/others/non-autorise.php" class="menu-item">
                    <img src="/ISMOSHARE/assets/bx_message-detail.png" />&nbsp;
                    <p>Chat</p>
                    <span class="notification-badge">soon</span>
                </a>
                <?php
                if ($role=="admin") {
                    echo"<a href='/ISMOSHARE/pages/utilisateurs/utilisateurs.php' class='menu-item'>
                    <img src='/ISMOSHARE/assets/Vector11.png' />&nbsp;
                    <p>Utilisateurs</p>
                    </a>";
                }
                if ($role=="admin" || $role=="formateur") {
                    echo"<a href='/ISMOSHARE/pages/validation/validation.php' class='menu-item active'>
                    <img src='/ISMOSHARE/assets/carbon_settings.png' />&nbsp;
                    <p>Validation</p>
                    </a>";
                }
                ?>
                <a href="/ISMOSHARE/others/logout.php" class="menu-item sign-out">
                    <img src="/ISMOSHARE/assets/Group 340.png" />&nbsp;
                    <p>Sign Out</p>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="containerv">
                <h1>Gestion des Ressource/Forum/Annonce</h1>

                <div class="search-filters">
                    <div class="search-box">
                        <input type="text" placeholder="Search by name of publisher" id="searchInput" />
                    </div>

                    <div class="dropdown">
                        <select class="dropdown-button" id="typeFilter">
                            <option value="tousType" selected>Tous</option>
                            <option value="ressource">Ressource</option>
                            <option value="forum">Forum</option>
                            <option value="commentaire">Commentaire</option>
                        </select>
                    </div>
                </div>
                <?php if(isset($_GET["msgsucces"])){echo"<center><div style='color : green;'>$_GET[msgsucces]</div></center>";}?>
                <?php if(isset($_GET["msgechec"])){echo"<center><div style='color : red;'>$_GET[msgechec]</div></center>";}?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30px"><input type="checkbox" /></th>
                            <th class="sortable" style="width: 180px">Full Name</th>
                            <th class="sortable" style="width: 120px">Titre</th>
                            <th class="sortable" style="width: 180px">Contenu</th>
                            <th class="sortable" style="width: 100px">Type</th>
                            <th class="sortable" style="width: 120px">Role</th>
                            <th class="sortable" style="width: 150px">Publish Date</th>
                            <th class="sortable" style="width: 150px">fichier importé</th>
                            <th style="width: 80px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($ressources as $ressource){
                            $name = substr(basename($ressource['CHEMIN_FICHIER']), 0, 10);
                            echo"<tr>
                            <td><input type='checkbox' /></td>
                            <td>
                            <div class='user-info'>
                                <img src='$ressource[CHEMIN_PHOTO]' class='avatar'/>
                                <span>$ressource[NOM_USER] $ressource[PRENOM_USER]</span>
                            </div>
                            </td>
                            <td>$ressource[TITRE_RESSOURCE]</td>
                            <td class='contenu'></td>
                            <td><span class='badge resource'>Ressource</span></td>
                            <td>$ressource[ROLE]</td>
                            <td>$ressource[DATE_UPLOAD]</td>
                            <td><a href='$ressource[CHEMIN_FICHIER]' class='download-link'>$name</a></td>
                            <td>
                            <div class='actions'>
                                <a href='/ISMOSHARE/others/refuser.php?idr=$ressource[ID_RESSOURCE]' style='color:red;'><i class='fa-solid fa-trash'></i></a>
                                <a href='/ISMOSHARE/others/accepter.php?idr=$ressource[ID_RESSOURCE]' style='color:green;'><i class='fa-regular fa-circle-check'></i></a>
                            </div>
                            </td>
                            </tr>";
                        }
                        foreach($forums as $forum){
                            $name=basename($forum['CHEMIN_PHOTO_F']);
                            echo"<tr>
                            <td><input type='checkbox' /></td>
                            <td>
                            <div class='user-info'>
                                <img src='$forum[CHEMIN_PHOTO]' class='avatar'/>
                                <span>$forum[NOM_USER] $forum[PRENOM_USER]</span>
                            </div>
                            </td>
                            <td>$forum[TITRE_FORUM]</td>
                            <td class='contenu'>$forum[CONTENU_SUJET]</td>
                            <td><span class='badge forum'>Forum</span></td>
                            <td>$forum[ROLE]</td>
                            <td>$forum[DATE_PUB_FORUM]</td>
                            <td><a href='$forum[CHEMIN_PHOTO_F]' class='download-link'>$name</a></td>
                            <td>
                            <div class='actions'>
                                <a href='/ISMOSHARE/others/refuser.php?idf=$forum[ID_FORUM]' style='color:red;'><i class='fa-solid fa-trash'></i></a>
                                <a href='/ISMOSHARE/others/accepter.php?idf=$forum[ID_FORUM]' style='color:green;'><i class='fa-regular fa-circle-check'></i></a>
                            </div>
                            </td>
                            </tr>";
                        }
                        foreach($commentF as $c){
                            echo"<tr>
                            <td><input type='checkbox' /></td>
                            <td>
                            <div class='user-info'>
                                <img src='$c[CHEMIN_PHOTO]' class='avatar'/>
                                <span>$c[NOM_USER] $c[PRENOM_USER]</span>
                            </div>
                            </td>
                            <td></td>
                            <td class='contenu'>$c[CONTENU_REPONSE]</td>
                            <td><span class='badge commentaire'>Commentaire</span></td>
                            <td>$c[ROLE]</td>
                            <td>$c[DATE_REPONSE]</td>
                            <td></td>
                            <td>
                            <div class='actions'>
                                <a href='/ISMOSHARE/others/refuser.php?idcf=$c[ID_REPONSE]' style='color:red;'><i class='fa-solid fa-trash'></i></a>
                                <a href='/ISMOSHARE/others/accepter.php?idcf=$c[ID_REPONSE]' style='color:green;'><i class='fa-regular fa-circle-check'></i></a>
                            </div>
                            </td>
                            </tr>";
                        }
                        foreach($commentR as $c){
                            echo"<tr>
                            <td><input type='checkbox' /></td>
                            <td>
                            <div class='user-info'>
                                <img src='$c[CHEMIN_PHOTO]' class='avatar'/>
                                <span>$c[NOM_USER] $c[PRENOM_USER]</span>
                            </div>
                            </td>
                            <td></td>
                            <td class='contenu'>$c[CONTENU_COMMENT]</td>
                            <td><span class='badge commentaire'>Commentaire</span></td>
                            <td>$c[ROLE]</td>
                            <td>$c[DATE_COMMENTAIRE]</td>
                            <td></td>
                            <td>
                            <div class='actions'>
                                <a href='/ISMOSHARE/others/refuser.php?idcr=$c[ID_COMMENT]' style='color:red;'><i class='fa-solid fa-trash'></i></a>
                                <a href='/ISMOSHARE/others/accepter.php?idcr=$c[ID_COMMENT]' style='color:green;'><i class='fa-regular fa-circle-check'></i></a>
                            </div>
                            </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const typeFilter = document.getElementById("typeFilter");
    const rows = document.querySelectorAll("tbody tr");

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;

        rows.forEach(row => {
            const fullName = row.children[1].innerText.toLowerCase(); // NOM + PRENOM
            const title = row.children[2].innerText.toLowerCase(); // TITRE (can be empty)
            const content = row.children[3].innerText.toLowerCase(); // CONTENU
            const type = row.children[4].innerText.toLowerCase(); // Type (badge content)

            const matchesSearch =
                fullName.includes(searchValue) ||
                title.includes(searchValue) ||
                content.includes(searchValue);

            const matchesType =
                selectedType === "tousType" || type.includes(selectedType);

            row.style.display = (matchesSearch && matchesType) ? "table-row" : "none";
        });
    }

    searchInput.addEventListener("input", filterTable);
    typeFilter.addEventListener("change", filterTable);
});
</script>

<style>
table {
    table-layout: fixed;
    width: 100%;
}

.contenu {
    overflow: auto;
    width: 150px;
    max-width: 150px;
    height: 100px;
    overflow: auto;
    display: block;
    white-space: nowrap;
}

.badge.commentaire {
    background-color: #188ad6;
}
</style>

</html>