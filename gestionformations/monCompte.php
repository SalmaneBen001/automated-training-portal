<?php
require_once "gestionFormations_connexion.php";
session_start();
$message = "";
if (!isset($_SESSION["matricule"])) {
    header("location: responsableFormations_login.php");
    exit();
}
if (isset($_GET["action"])) {
    if ($_GET["action"] === "deconnecter") {
        session_destroy();
        header("location: responsableFormations_login.php");
        exit();
    }
    if ($_GET["action"] === "annuler") {
        $stmt = $db->prepare("select status from formation where idFormation=:idFormation");
        $stmt->execute([":idFormation" => $_GET["id"]]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rs["status"] == 1) {
            $message = "Impossible d'annuler une formation déjà terminée !";
        } else {
            $st = $db->prepare("delete from formation where idFormation=:idFormation");
            $st->execute([":idFormation" => $_GET["id"]]);
        }
    }
    if ($_GET["action"] === "terminer") {
        $stmt = $db->prepare("select status from formation where idFormation=:idFormation");
        $stmt->execute([":idFormation" => $_GET["id"]]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rs["status"] == 1) {
            $message = "Cette formation est déjà terminée !";
        } else {
            $st = $db->prepare("update formation set status = 1 where idFormation=:idFormation");
            $st->execute([":idFormation" => $_GET["id"]]);
        }
    }
}
$st = $db->query("select idFormation, titre, logo, dateDebut, dateFin, nom, prenom, status from formation
    inner join responsableFormation on formation.matriculeResponsable = responsableFormation.matricule
    order by dateDebut desc");
$formations = $st->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        /* Barre d'actions supérieure */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin-bottom: 5px;
        }

        /* Style des boutons généraux */
        .btn {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-add {
            background-color: #007bff;
            color: white;
        }

        .btn-add:hover {
            background-color: #0056b3;
        }

        .btn-logout {
            background-color: #e0e0e0;
            color: #4f4f4f;
        }

        .btn-logout:hover {
            background-color: #e53e3e;
            color: white;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
            border: 1px solid #eceff1;
            border-radius: 6px;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            background-color: #fff;
        }

        th {
            background-color: #f8f9fa;
            color: #546e7a;
            padding: 14px 16px;
            font-weight: 600;
            border-bottom: 2px solid #eceff1;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #eceff1;
            color: #37474f;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f9fbfd;
        }

        .img-logo {
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #cfd8dc;
            background-color: #fff;
            padding: 2px;
            display: block;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .status-done {
            background-color: #def7ec;
            color: #03543f;
        }

        .status-ongoing {
            background-color: #fef3c7;
            color: #92400e;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            white-space: nowrap;
        }

        .action-link {
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .action-cancel {
            color: #e53e3e;
            background-color: rgba(229, 62, 62, 0.08);
        }

        .action-cancel:hover {
            background-color: rgba(229, 62, 62, 0.15);
        }

        .action-finish {
            color: #2b6cb0;
            background-color: rgba(43, 108, 176, 0.08);
        }

        .action-finish:hover {
            background-color: rgba(43, 108, 176, 0.15);
        }

        .alert-error {
            background-color: #fde8e8;
            border: 1px solid #f8b4b4;
            color: #e53e3e;
            padding: 12px 20px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <!-- BARRE D'ACTIONS SUPERIEURE -->
    <div class="top-bar">
        <a href="NouvelleFormation.php" class="btn btn-add">+ Ajouter Formation</a>
        <a href="monCompte.php?action=deconnecter" class="btn btn-logout">Se Déconnecter</a>
    </div>

    <!-- ZONE DE TABLEAU -->
    <div class="container">
        <?php if ($st->rowCount() > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Formation</th>
                            <th>Titre</th>
                            <th>Logo</th>
                            <th>Date de Début</th>
                            <th>Date de Fin</th>
                            <th>Durée</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formations as $formation): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($formation["idFormation"]) ?></strong></td>
                                <td><?= htmlspecialchars($formation["titre"]) ?></td>
                                <td>
                                    <img src="images/<?= htmlspecialchars($formation['logo']) ?>"
                                        alt="Logo <?= htmlspecialchars($formation["titre"]) ?>"
                                        width="60" height="40" class="img-logo">
                                </td>
                                <td><?= htmlspecialchars($formation["dateDebut"]) ?></td>
                                <td><?= htmlspecialchars($formation["dateFin"]) ?></td>
                                <td>
                                    <strong>
                                        <?= date_diff(new DateTime(htmlspecialchars($formation["dateDebut"])), new DateTime(htmlspecialchars($formation["dateFin"])))->format("%a jours") ?>
                                    </strong>
                                </td>
                                <td><?= htmlspecialchars($formation["nom"]) . " " . htmlspecialchars($formation["prenom"]) ?></td>
                                <td>
                                    <?php if ($formation["status"] == 1): ?>
                                        <span class="status-badge status-done">Terminée</span>
                                    <?php else: ?>
                                        <span class="status-badge status-ongoing">En cours</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="monCompte.php?action=annuler&id=<?= htmlspecialchars($formation["idFormation"]) ?>" class="action-link action-cancel" onclick="return confirm('Annuler cette formation ?');">Annuler</a>
                                    <a href="monCompte.php?action=terminer&id=<?= htmlspecialchars($formation["idFormation"]) ?>" class="action-link action-finish">Terminer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #777; text-align: center; margin: 20px 0;">Aucune formation enregistrée dans votre compte.</p>
        <?php endif; ?>

        <!-- AFFICHAGE DES MESSAGES -->
        <?php if (!empty($message)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>