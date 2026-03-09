<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	
	<?php
// Connexion à la base
$host = "localhost";
$user = "evelyne";        // ton utilisateur MySQL
$password = "secretaire"; // ton mot de passe
$database = "evelyne";    // ta base

$conn = new mysqli($host, $user, $password, $database);

// Vérification
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Si le formulaire est soumis
if (isset($_POST['classe'])) {
    $classe = $_POST['classe'];

    // Requête principale pour récupérer les apprenants
    $sql = "
    SELECT le.Nom_Prenoms, le.Sexe, le.Type, c.Classe,
           fp.Net_a_payer, fp.Montant_paye, fp.Montant_du
    FROM fiche_paie fp
    JOIN liste_eleves le ON fp.ID_eleve = le.ID_eleve
    JOIN classe c ON fp.ID_classe = c.ID_classe
    JOIN regime r ON fp.ID_Reg = r.ID_Reg
    WHERE c.Classe = ?
    ORDER BY le.Nom_Prenoms ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $classe);
    $stmt->execute();
    $result = $stmt->get_result();

    // Compteurs
    $nb_filles = 0;
    $nb_garcons = 0;
    $nb_sourdes = 0;
    $nb_sourds = 0;
    $nb_entendantes = 0;
    $nb_entendants = 0;
    $effectif_total = 0;
    $total_paye = 0;
    $total_du = 0;

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        $effectif_total++;

        // Totaux financiers
        $total_paye += (int)$row['Montant_paye'];
        $total_du   += (int)$row['Montant_du'];

        // Comptage filles/garçons et sourds/entendants
        if (strtoupper($row['Sexe']) == 'F') {
            $nb_filles++;
            if (stripos($row['Type'], 'Sourd') !== false) {
                $nb_sourdes++;
            } else {
                $nb_entendantes++;
            }
        } else {
            $nb_garcons++;
            if (stripos($row['Type'], 'Sourd') !== false) {
                $nb_sourds++;
            } else {
                $nb_entendants++;
            }
        }
    }

    // Tableau des statistiques
    echo "<h3>Classe : " . htmlspecialchars($classe) . "</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>Filles</th>
            <th>Garçons</th>
            <th>Sourdes</th>
            <th>Sourds</th>
            <th>Entendantes</th>
            <th>Entendants</th>
            <th>Effectif total</th>
            <th>Montant total payé</th>
            <th>Montant total dû</th>
          </tr>";
    echo "<tr>
            <td>$nb_filles</td>
            <td>$nb_garcons</td>
            <td>$nb_sourdes</td>
            <td>$nb_sourds</td>
            <td>$nb_entendantes</td>
            <td>$nb_entendants</td>
            <td>$effectif_total</td>
            <td>" . number_format($total_paye, 0, ',', ' ') . " FCFA</td>
            <td>" . number_format($total_du, 0, ',', ' ') . " FCFA</td>
          </tr>";
    echo "</table><br>";

    // Tableau des apprenants
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>Nom & Prénoms</th>
            <th>Sexe</th>
            <th>Type</th>
            <th>Classe</th>
            <th>Net à payer</th>
            <th>Montant payé</th>
            <th>Montant dû</th>
          </tr>";

    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Nom_Prenoms']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Sexe']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Classe']) . "</td>";
        echo "<td>" . number_format($row['Net_a_payer'], 0, ',', ' ') . "</td>";
        echo "<td>" . number_format($row['Montant_paye'], 0, ',', ' ') . "</td>";
        echo "<td>" . number_format($row['Montant_du'], 0, ',', ' ') . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    $stmt->close();
}
?>

<!-- Formulaire de sélection de la classe -->
<form method="post" action="">
    <label for="classe">Sélectionnez une classe :</label>
    <select name="classe" id="classe">
        <option value="1ere">1ère</option>
        <option value="2nde">2nde</option>
        <option value="3eme">3ème</option>
        <option value="4eme">4ème</option>
        <option value="5eme">5ème</option>
        <option value="CE1">CE1</option>
        <option value="CE2">CE2</option>
        <option value="CI">CI</option>
        <option value="CM1">CM1</option>
        <option value="CM2">CM2</option>
        <option value="CP">CP</option>
        <option value="Maternelle">Maternelle</option>
        <option value="RBC">RBC</option>
        <option value="Sixieme">Sixième</option>
        <option value="Tle">Terminale</option>
    </select>
    <button type="submit">Afficher les apprenants</button>
</form>

</body>
</html>