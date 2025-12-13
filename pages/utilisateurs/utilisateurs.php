<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['etat']!= "valide"){
    header("Location:/ISMOSHARE/pages/connexion/page-conexion.php");
    exit;
}
elseif ($_SESSION['role'] !== 'admin') {
    header("Location:/ISMOSHARE/others/non-autorise.php");
    exit;
}
else {
    include("../../others/code-conexionAvecDB.php");
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
    <link rel="stylesheet" href="/ISMOSHARE/styles/users-style.css" />
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <a href="/ISMOSHARE/pages/principale/page principale.php"><img src="/ISMOSHARE/assets/logo.png" /></a>
            </div>

            <div class="profile">
                <img src="<?=$_SESSION['photo']?>" />
                <div class="profile-info">
                    <p class="profile-name"><?= $_SESSION['prenom']." ".$_SESSION['nom'] ?? 'Admin' ?></p>
                    <p class="profile-role">
                        <span class="online-dot"></span><?= $_SESSION['role'] ?>
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
                if ($_SESSION['role']=="admin") {
                    echo"<a href='/ISMOSHARE/pages/utilisateurs/utilisateurs.php' class='menu-item active'>
                    <img src='/ISMOSHARE/assets/Vector11.png' />&nbsp;
                    <p>Utilisateurs</p>
                    </a>";
                    echo"<a href='/ISMOSHARE/pages/validation/validation.php' class='menu-item'>
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
            <h1>Gestion des utilisateurs</h1>

            <div class="containeru">
                <div class="toolbar">
                    <div class="search-filters">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search" />
                        </div>

                        <select id="roleFilter" class="filter-button">
                            <option value="tousRole" selected>Tous</option>
                            <option value="admin">Admin</option>
                            <option value="formateur">Formateur</option>
                            <option value="stagiaire">Stagiaire</option>
                        </select>
                        <select id="statusFilter" class="filter-button">
                            <option value="tousStatus" selected>Tous</option>
                            <option value="valide">Valide</option>
                            <option value="en attente">En attente</option>
                        </select>
                    </div>

                    <div class="actions">
                        <form method="post" action="export_users.php">
                            <button class="add-user-button">Export</button>
                        </form>
                    </div>
                </div>
                <?php if(isset($_GET["msgy"])){echo"<center><div style='color : green;'>$_GET[msgy]</div></center>";}?>
                <?php if(isset($_GET["msge"])){echo"<center><div style='color : red;'>$_GET[msge]</div></center>";}?>
                <table>
                    <thead>
                        <tr>
                            <th class="sortable">Full Name</th>
                            <th class="sortable">Email</th>
                            <th class="sortable">Matricule ou CEF</th>
                            <th class="sortable">Status</th>
                            <th class="sortable">Role</th>
                            <th class="sortable">Joined Date</th>
                            <th class="sortable">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $reqs = $db->query("SELECT * FROM utilisateur");
                            $users = $reqs->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($users as $user) {
                                echo "<tr>";
                                echo "<td><div class='user-info'><img class='avatar' src='$user[CHEMIN_PHOTO]'>".htmlspecialchars($user['NOM_USER'])." ".htmlspecialchars($user['PRENOM_USER'])."</td>";
                                echo "<td>" . htmlspecialchars($user['EMAIL_INSTITUTIONNEL']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['MATRICULE_OU_CEF']) . "</td>";
                                if ($user['ETAT_COMPTE']=='valide') {
                                    echo "<td>"."<div class='status-badge status-active'>" . htmlspecialchars($user['ETAT_COMPTE']) ."</div>"."</td>";
                                } else {
                                    echo "<td>"."<div class='status-badge status-banned'>" . htmlspecialchars($user['ETAT_COMPTE']) ."</div>"."</td>";
                                }
                                echo "<td>" . htmlspecialchars($user['ROLE']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['inscription_date']) . "</td>";
                                echo"<td>
                                        <div class='action-icons'>
                                            <span class='action-icon'><a title='accept user' onclick=\"openValidationForm(" . $user['ID_USER'] . ", '" . $user['PRENOM_USER'] . "')\"><i class='fa-solid fa-user-check' style='color: #63E6BE;'></i></a></span>
                                            <span class='action-icon'> <a title='delete user' href='delete_user.php?id_user={$user['ID_USER']}' onclick=\"return confirm('Are you sure you want to delete this user?');\">
                                            <i class='fa-solid fa-user-xmark' style='color: #c81e1e;'></i>
                                            </a></span>
                                            <a href='users_profiles.php?idp=$user[ID_USER]' title='modifier user'><span class='action-icon'><i class='fa-solid fa-user-pen'></i></a></span>
                                        </div>
                                    </td>";
                                echo "</tr>";
                            }
                        } 
                        catch (PDOException $e) {
                            echo "<tr><td colspan='7'>Erreur de récupération : " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <div>
                        <?php
                        try {
                            $reqi = $db->query("SELECT count(*) FROM utilisateur where ETAT_COMPTE='valide' ");
                            $nb = $reqi->fetchColumn();
                            echo "<span>nombre d'utilisateur actifs : $nb </span>";
                        } catch (PDOException $e) {
                            echo "erreur de selection " . $e->getMessage();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Popup -->
    <div id="validationPopup"
        style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%,-30%); background:white; padding:20px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.3); z-index:1000;">
        <form method="POST" action="validate_user.php">
            <input type="hidden" name="ID_USER" id="userIdField">
            <p id="popupMessage">Do you want to validate this user?</p>
            <button type="submit" name="action" value="valide" class="btn btn-success">Accept</button>
            <button type="submit" name="action" value="refusé" class="btn btn-danger">Decline</button>
            <button type="button" onclick="closeValidationForm()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>

    <script>
    function openValidationForm(id, prenom) {
        document.getElementById("userIdField").value = id;
        document.getElementById("popupMessage").innerText = "Do you want to validate the user " + prenom + "?";
        document.getElementById("validationPopup").style.display = "block";
    }

    function closeValidationForm() {
        document.getElementById("validationPopup").style.display = "none";
    }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const roleFilter = document.getElementById("roleFilter");
        const statusFilter = document.getElementById("statusFilter");
        const rows = document.querySelectorAll("tbody tr");

        function filterTable() {
            const searchValue = searchInput.value.toLowerCase();
            const selectedRole = roleFilter.value;
            const selectedStatus = statusFilter.value;

            rows.forEach(row => {
                const fullName = row.children[0].innerText.toLowerCase();
                const email = row.children[1].innerText.toLowerCase();
                const matricule = row.children[2].innerText.toLowerCase();
                const status = row.children[3].innerText.toLowerCase();
                const role = row.children[4].innerText.toLowerCase();

                const matchesSearch = fullName.includes(searchValue) || email.includes(searchValue) ||
                    matricule.includes(searchValue);
                const matchesRole = selectedRole === "tousRole" || role === selectedRole.toLowerCase();
                const matchesStatus = selectedStatus === "tousStatus" || status === selectedStatus
                    .toLowerCase();

                if (matchesSearch && matchesRole && matchesStatus) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        searchInput.addEventListener("input", filterTable);
        roleFilter.addEventListener("change", filterTable);
        statusFilter.addEventListener("change", filterTable);
    });
    </script>

    <style>
    #validationPopup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        width: 400px;
        max-width: 90%;
        text-align: center;
        animation: fadeIn 0.3s ease-out;
        border: 1px solid #e0e0e0;
    }

    #popupMessage {
        font-size: 18px;
        margin-bottom: 25px;
        color: #333;
        line-height: 1.5;
    }

    #validationPopup form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    #validationPopup button {
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 16px;
    }

    #validationPopup button[type="submit"][value="valide"] {
        background-color: #28a745;
        color: white;
    }

    #validationPopup button[type="submit"][value="valide"]:hover {
        background-color: #218838;
    }

    #validationPopup button[type="submit"][value="refusé"] {
        background-color: #dc3545;
        color: white;
    }

    #validationPopup button[type="submit"][value="refusé"]:hover {
        background-color: #c82333;
    }

    #validationPopup button[type="button"] {
        background-color: #6c757d;
        color: white;
    }

    #validationPopup button[type="button"]:hover {
        background-color: #5a6268;
    }

    .add-user-button::before {
        content: "-";
        font-size: 18px;
        font-weight: bold;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translate(-50%, -45%);
        }

        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }
    </style>
</body>

</html>