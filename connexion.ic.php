<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
</head>

<body>
	<?php
// Paramètres de connexion
$servername = "localhost";   // ou l'adresse IP du serveur
$username   = "emilie";        // ton nom d'utilisateur MySQL
$password   = "emilie";            // ton mot de passe MySQL
$dbname     = "winner";      // nom de la base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

echo "Connexion réussie à la base de données 'winner' !";
?>

	
</body>
</html>