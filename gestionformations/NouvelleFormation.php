<?php
require_once "gestionFormations_connexion.php";
session_start();
$message = "";
$dateDebut = "";
$dateFin = "";
if (isset($_POST["ajouter"])) {
    $pattern = '/^[F]_2024_\d{3}$/';
    if (!preg_match($pattern, $_POST["idFormation"])) {
        $message = "Format invalide !";
    }
    if ($message == "") {
        $st = $db->prepare("select idFormation from formation where idFormation=:idFormation");
        $st->execute([":idFormation" => $_POST["idFormation"]]);
        $rs = $st->fetch(PDO::FETCH_ASSOC);
        if ($rs) {
            $message = "Déjà existant";
        }
    }
    if ($message == "") {
        $today = new DateTime();
        $dateDebut = new DateTime($_POST["dateDebut"]);
        $dateFin = new DateTime($_POST["dateFin"]);
        if ($dateDebut > $today || $dateDebut > $dateFin) {
            $message = "Date invalide";
        }
    }
    if ($message == "") {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $nomFichier = $_FILES['logo']['name'];
            $cheminTemporaire = $_FILES['logo']['tmp_name'];
            $dossierDestination = 'images/' . $nomFichier;
            if (move_uploaded_file($cheminTemporaire, $dossierDestination)) {
                $stmt = $db->prepare("insert into formation (idFormation, titre, logo, dateDebut, dateFin, matriculeResponsable) values
                    (:idFormation, :titre, :logo, :dateDebut, :dateFin, :matricule)");
                $stmt->execute([
                    ":idFormation" => $_POST["idFormation"],
                    ":titre" => $_POST["titre"],
                    ":logo" => $_FILES["logo"]["name"],
                    ":dateDebut" => $dateDebut->format("Y-m-d"),
                    ":dateFin" => $dateFin->format("Y-m-d"),
                    ":matricule" => $_SESSION["matricule"]
                ]);
                header("location: monCompte.php");
                exit();
            } else {
                $message = "Erreur lors de transfert de l'image";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Formation</title>
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
        }

        .container {
            width: 100%;
            max-width: 650px;
            background: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        h3 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #2c3e50;
            border-bottom: 2px solid #eceff1;
            padding-bottom: 12px;
            font-size: 22px;
            font-weight: 600;
        }

        /* Organisation des groupes de champs */
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .date-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 480px) {
            .date-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        label {
            margin-bottom: 8px;
            color: #546e7a;
            font-weight: 600;
            font-size: 14px;
        }

        /* Styles unifiés pour tous les champs de saisie standard */
        input[type="text"],
        input[type="date"] {
            padding: 12px;
            border: 1px solid #cfd8dc;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fafafa;
            box-sizing: border-box;
            width: 100%;
        }

        input[type="text"]:focus,
        input[type="date"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
            outline: none;
            background-color: #fff;
        }

        /* Style moderne spécifique pour le champ Fichier (Logo) */
        input[type="file"] {
            padding: 10px;
            border: 1px dashed #b0bec5;
            border-radius: 6px;
            background-color: #fafafa;
            cursor: pointer;
            font-size: 14px;
            color: #546e7a;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="file"]:hover {
            background-color: #f1f3f4;
            border-color: #007bff;
        }

        /* Bouton d'action principal */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        /* Bouton d'annulation ou de retour */
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #78909c;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: #2c3e50;
            text-decoration: underline;
        }

        /* Conteneur d'alerte pour les messages d'erreur */
        .alert-error {
            background-color: #fde8e8;
            border: 1px solid #f8b4b4;
            color: #e53e3e;
            padding: 12px 20px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Ajouter une Nouvelle Formation</h3>

        <form action="" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label for="id">ID Formation :</label>
                <input type="text" name="idFormation" id="id" required placeholder="Ex: F_2024_001">
            </div>

            <div class="form-group">
                <label for="titre">Titre de la formation :</label>
                <input type="text" name="titre" id="titre" required placeholder="Ex: Développement Web">
            </div>

            <div class="form-group">
                <label for="logo">Logo / Image illustrative :</label>
                <input type="file" name="logo" id="logo" accept="image/*" required>
            </div>

            <div class="date-grid">
                <div class="form-group">
                    <label for="dateD">Date de Début :</label>
                    <input type="date" name="dateDebut" id="dateD" required>
                </div>

                <div class="form-group">
                    <label for="dateF">Date de Fin :</label>
                    <input type="date" name="dateFin" id="dateF" required>
                </div>
            </div>

            <input type="submit" value="Créer la formation" name="ajouter" class="btn-submit">
        </form>

        <a href="monCompte.php" class="btn-back">← Annuler et retourner au tableau de bord</a>

        <?php if (!empty($message)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>