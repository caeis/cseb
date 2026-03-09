
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

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification que tous les champs existent et ne sont pas vides
    if (!empty($_POST["idprof"]) && !empty($_POST["idmatiere"]) && !empty($_POST["idclasse"]) 
        && !empty($_POST["dateCours"]) && !empty($_POST["heureDebut"]) && !empty($_POST["heureFin"])) {
        
        $idprof = $_POST["idprof"];
        $idmatiere = $_POST["idmatiere"];
        $idclasse = $_POST["idclasse"];
        $dateCours = $_POST["dateCours"];
        $heureDebut = $_POST["heureDebut"];
        $heureFin = $_POST["heureFin"];

        // Requête d'insertion
        $sql = "INSERT INTO HeuresProfesseurs (idprof, idmatiere, DateCours, HeureDebut, HeureFin, Classe)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $idprof, $idmatiere, $dateCours, $heureDebut, $heureFin, $idclasse);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Enregistrement ajouté avec succès.</p>";
        } else {
            echo "<p style='color:red;'>Erreur : " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>Veuillez remplir tous les champs obligatoires.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>FICHE DES PROFESSEURS</title>
</head>
<body>
    <h2>REMPLISSAGE DE LA FICHE DE CUMUL DES HEURES DE VACATION</h2>
    <form method="post" action="">
        
        <!-- Menu déroulant Professeur -->
        <label for="idprof">Professeur :</label>
        <select name="idprof" id="idprof" required>
            <?php
            $result = $conn->query("SELECT idprof, Nom, Prenoms FROM Listeprofesseurs");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["idprof"] . "'>" . $row["Nom"] . " " . $row["Prenoms"] . "</option>";
            }
            ?>
        </select><br><br>

        <!-- Menu déroulant Matière -->
        <label for="idmatiere">Matière :</label>
        <select name="idmatiere" id="idmatiere" required>
            <?php
            $result = $conn->query("SELECT idmatiere, NomMatiere FROM ListeMatiere");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["idmatiere"] . "'>" . $row["NomMatiere"] . "</option>";
            }
            ?>
        </select><br><br>

        <!-- Menu déroulant Classe -->
        <label for="idclasse">Classe :</label>
        <select name="idclasse" id="idclasse" required>
            <?php
            $result = $conn->query("SELECT idclasse, NomClasse FROM ListeClasse");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["NomClasse"] . "'>" . $row["NomClasse"] . "</option>";
            }
            ?>
        </select><br><br>

        <!-- Date du cours -->
        <label for="dateCours">Date du cours :</label>
        <input type="date" name="dateCours" id="dateCours" required><br><br>

        <!-- Heure de début -->
        <label for="heureDebut">Heure de début :</label>
        <input type="time" name="heureDebut" id="heureDebut" required><br><br>

        <!-- Heure de fin -->
        <label for="heureFin">Heure de fin :</label>
        <input type="time" name="heureFin" id="heureFin" required><br><br>

        <input type="submit" value="Enregistrer">
    </form>
</body>
</html>

<?php
$conn->close();
?>
	
	<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "emilie", "emilie", "winner");

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Requête pour le cumul global des heures par professeur
$sqlTotal = "
    SELECT p.idprof, p.Nom, p.Prenoms,
           SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(h.HeureFin, h.HeureDebut)))) AS TotalHeures
    FROM Listeprofesseurs p
    INNER JOIN HeuresProfesseurs h ON p.idprof = h.idprof
    GROUP BY p.idprof, p.Nom, p.Prenoms
";

$resultTotal = $conn->query($sqlTotal);

if ($resultTotal->num_rows > 0) {
    echo "<h2>Cumul des heures par professeur</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID Prof</th><th>Nom</th><th>Prénoms</th><th>Total Heures</th></tr>";
    while ($row = $resultTotal->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["idprof"] . "</td>";
        echo "<td>" . $row["Nom"] . "</td>";
        echo "<td>" . $row["Prenoms"] . "</td>";
        echo "<td>" . $row["TotalHeures"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Aucun cumul trouvé.";
}

// Requête pour le cumul des heures par professeur et par classe
$sqlClasse = "
    SELECT p.idprof, p.Nom, p.Prenoms, h.Classe,
           SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(h.HeureFin, h.HeureDebut)))) AS TotalHeuresClasse
    FROM Listeprofesseurs p
    INNER JOIN HeuresProfesseurs h ON p.idprof = h.idprof
    GROUP BY p.idprof, p.Nom, p.Prenoms, h.Classe
    ORDER BY p.idprof, h.Classe
";

$resultClasse = $conn->query($sqlClasse);

if ($resultClasse->num_rows > 0) {
    echo "<h2>Cumul des heures par professeur et par classe</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID Prof</th><th>Nom</th><th>Prénoms</th><th>Classe</th><th>Total Heures Classe</th></tr>";
    while ($row = $resultClasse->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["idprof"] . "</td>";
        echo "<td>" . $row["Nom"] . "</td>";
        echo "<td>" . $row["Prenoms"] . "</td>";
        echo "<td>" . $row["Classe"] . "</td>";
        echo "<td>" . $row["TotalHeuresClasse"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Aucun cumul par classe trouvé.";
}

// Requête pour le détail des heures (cours individuels)
$sqlDetail = "
    SELECT p.idprof, p.Nom, p.Prenoms, h.Classe,
           h.DateCours, h.HeureDebut, h.HeureFin,
           TIMEDIFF(h.HeureFin, h.HeureDebut) AS Duree
    FROM Listeprofesseurs p
    INNER JOIN HeuresProfesseurs h ON p.idprof = h.idprof
    ORDER BY p.idprof, h.Classe, h.DateCours, h.HeureDebut
";

$resultDetail = $conn->query($sqlDetail);

if ($resultDetail->num_rows > 0) {
    echo "<h2>Détail des heures par professeur et par classe</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID Prof</th><th>Nom</th><th>Prénoms</th><th>Classe</th><th>Date</th><th>Heure Début</th><th>Heure Fin</th><th>Durée</th></tr>";
    while ($row = $resultDetail->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["idprof"] . "</td>";
        echo "<td>" . $row["Nom"] . "</td>";
        echo "<td>" . $row["Prenoms"] . "</td>";
        echo "<td>" . $row["Classe"] . "</td>";
        echo "<td>" . $row["DateCours"] . "</td>";
        echo "<td>" . $row["HeureDebut"] . "</td>";
        echo "<td>" . $row["HeureFin"] . "</td>";
        echo "<td>" . $row["Duree"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Aucun détail trouvé.";
}

$conn->close();
?>


</body>
</html>