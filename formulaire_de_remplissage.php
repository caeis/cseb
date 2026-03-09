<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
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

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_eleve   = $_POST['eleve'];
    $id_classe  = $_POST['classe'];
    $id_regime  = $_POST['regime'];
    $net_a_payer = $_POST['net_a_payer'];
    $montant_paye = $_POST['montant_paye'];
    $montant_du   = $net_a_payer - $montant_paye;

    $sql = "INSERT INTO fiche_paie (ID_Reg, ID_classe, ID_eleve, Net_a_payer, Montant_paye, Montant_du)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiidd", $id_regime, $id_classe, $id_eleve, $net_a_payer, $montant_paye, $montant_du);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Paiement enregistré avec succès !</p>";
    } else {
        echo "<p style='color:red;'>Erreur : " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!-- Formulaire de saisie -->
<form method="post" action="">
    <label for="eleve">Élève :</label>
    <select name="eleve" id="eleve" required>
        <?php
        $res = $conn->query("SELECT ID_eleve, Nom_Prenoms FROM liste_eleves ORDER BY Nom_Prenoms ASC");
        while ($row = $res->fetch_assoc()) {
            echo "<option value='" . $row['ID_eleve'] . "'>" . htmlspecialchars($row['Nom_Prenoms']) . "</option>";
        }
        ?>
    </select><br><br>

    <label for="classe">Classe :</label>
    <select name="classe" id="classe" required>
        <?php
        $res = $conn->query("SELECT ID_classe, Classe FROM classe ORDER BY Classe ASC");
        while ($row = $res->fetch_assoc()) {
            echo "<option value='" . $row['ID_classe'] . "'>" . htmlspecialchars($row['Classe']) . "</option>";
        }
        ?>
    </select><br><br>

    <label for="regime">Régime :</label>
    <select name="regime" id="regime" required>
        <?php
        $res = $conn->query("SELECT ID_Reg, Regime FROM regime ORDER BY Regime ASC");
        while ($row = $res->fetch_assoc()) {
            echo "<option value='" . $row['ID_Reg'] . "'>" . htmlspecialchars($row['Regime']) . "</option>";
        }
        ?>
    </select><br><br>

    <label for="net_a_payer">Net à payer :</label>
    <input type="number" name="net_a_payer" id="net_a_payer" required><br><br>

    <label for="montant_paye">Montant payé :</label>
    <input type="number" name="montant_paye" id="montant_paye" required><br><br>

    <button type="submit">Enregistrer le paiement</button>
</form>

</body>
</html>