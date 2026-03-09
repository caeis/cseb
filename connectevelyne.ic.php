<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
</head>

<body>
	<?php
// Paramètres de connexion
$host = "localhost";      // ou l'adresse IP du serveur MySQL
$user = "evelyne"; // remplace par ton nom d'utilisateur MySQL
$password = "secretaire"; // remplace par ton mot de passe MySQL
$database = "evelyne";    // nom de ta base de données

// Connexion
$conn = new mysqli($host, $user, $password, $database);

// Vérification
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
} else {
    echo "Connexion réussie à la base de données evelyne";
}

// Fermer la connexion (optionnel à la fin du script)
$conn->close();
?>

</body>
</html>