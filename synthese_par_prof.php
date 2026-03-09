<!doctype html>
<html>
<head>
<meta charset="UTF-8"> <title>REMPLISSAGE DE LA FICHE DE CUMULE DES HEURES DE VACATION</title> <!-- Lien vers le fichier CSS --> <link rel="stylesheet" href="style.css">
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

// Vérifier si un professeur a été sélectionné
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["idprof"])) {
    $idprof = $_POST["idprof"];

    // Requête pour calculer le cumul des heures par classe pour le professeur sélectionné
    $sql = "
        SELECT h.Classe,
               SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(h.HeureFin, h.HeureDebut)))) AS TotalHeuresClasse
        FROM HeuresProfesseurs h
        WHERE h.idprof = ?
        GROUP BY h.Classe
        ORDER BY h.Classe
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idprof);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Nombre d'heures par classe</h2>";
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Classe</th><th>Total Heures</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Classe"] . "</td>";
            echo "<td>" . $row["TotalHeuresClasse"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>Aucune heure enregistrée pour ce professeur.</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Consultation des heures par classe</title>
</head>
<body>
    <h2>Choisir un professeur pour consulter ses heures par classe</h2>
    <form method="post" action="">
        <label for="idprof">Professeur :</label>
        <select name="idprof" id="idprof" required>
            <?php
            // Récupérer la liste des professeurs
            $result = $conn->query("SELECT idprof, Nom, Prenoms FROM Listeprofesseurs");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["idprof"] . "'>" . $row["Nom"] . " " . $row["Prenoms"] . "</option>";
            }
            ?>
        </select>
        <br><br>
        <input type="submit" value="Consulter">
    </form>
</body>
</html>

<?php
$conn->close();
?>

</body>
</html>