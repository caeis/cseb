<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
</head>

<body>
	<?php include("menu_principal.php"); ?>

	<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "emilie", "emilie", "winner");

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Requête pour récupérer les professeurs
$sql = "SELECT Nom, Prenoms FROM Listeprofesseurs";
$result = $conn->query($sql);

// Affichage du tableau HTML
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Nom</th><th>Prénoms</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["Nom"] . "</td><td>" . $row["Prenoms"] . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='2'>Aucun professeur trouvé</td></tr>";
}

echo "</table>";

// Fermer la connexion
$conn->close();
?>

</body>
</html>