<?php
require_once "gestionFormations_connexion.php";
session_start();
$message = "";
if (isset($_POST["connecter"])) {
    if (!empty($_POST["matricule"]) && !empty($_POST["motpasse"])) {
        $matricule = $_POST["matricule"];
        $motPasse = $_POST["motpasse"];
        $st = $db->prepare("select matricule, motPasse from responsableFormation where matricule=:matricule");
        $st->execute([":matricule" => $matricule]);
        $rs = $st->fetch(PDO::FETCH_ASSOC);
        if ($rs && $rs["motPasse"] == $motPasse) {
            $_SESSION["matricule"] = $matricule;
            header("location: monCompte.php");
            exit();
        } else {
            $message = "Votre matricule ou mot de passe est incorrect";
        }
    } else {
        $message = "Votre matricule ou mot de passe est vide";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Boîtier de connexion */
        .login-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        h3 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 24px;
            text-align: center;
            font-weight: 600;
        }

        /* Groupes de champs */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #546e7a;
            font-weight: 600;
            font-size: 14px;
        }

        /* Inputs text et password */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #cfd8dc;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fafafa;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
            outline: none;
            background-color: #fff;
        }

        /* Bouton de soumission */
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

        /* Alerte d'erreur */
        .alert-error {
            background-color: #fde8e8;
            border: 1px solid #f8b4b4;
            color: #e53e3e;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h3>Connexion</h3>

        <form action="" method="post">
            <div class="form-group">
                <label for="matricule">Matricule :</label>
                <input type="text" name="matricule" id="matricule" required>
            </div>

            <div class="form-group">
                <label for="mdp">Mot de passe :</label>
                <input type="password" name="motpasse" id="mdp" required>
            </div>

            <input type="submit" value="Se Connecter" name="connecter" class="btn-submit">
        </form>

        <?php if (!empty($message)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>