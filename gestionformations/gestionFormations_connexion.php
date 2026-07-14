<?php
// Note : Port configuré sur 3307 pour mon environnement local (à modifier en 3306 si nécessaire)
$dsn = "mysql:host=localhost;dbname=gestionFormations;port=3307";
$user = "root";
$pass = "";
try {
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error : ". $e->getMessage());
}
?>