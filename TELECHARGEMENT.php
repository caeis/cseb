<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
		<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil - Centresourd</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
    }
    header {
      background: #4bb3a7;
      color: #fff;
      padding: 20px;
      text-align: center;
    }
    table {
      width: 80%;
      margin: 40px auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    td {
      border: 1px solid #ccc;
      padding: 30px;
      text-align: center;
    }
    .menu-btn {
      display: inline-block;
      padding: 15px 30px;
      background: #4bb3a7;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-size: 18px;
      font-weight: bold;
      transition: background 0.3s;
    }
    .menu-btn:hover {
      background: #3a8d80;
    }
  </style>
</head>
<body>
  <header>
    <h1>Bienvenue sur le site Centresourd</h1>
    <p>Gestion des contributions et inscriptions</p>
  </header>

  <table>
    <tr>
      <td>
        <a class="menu-btn" href="FORMULAIRE_CONTRIBUTIONCentresourd.php">PAYER CONTRIBUTION</a>
      </td>
      <td>
        <a class="menu-btn" href="Synthese_par_classe.php">POINT DES CONTRIBUTIONS PAR CLASSE</a>
      </td>
      <td>
        <a class="menu-btn" href="FORMULAIRE_LISTE.php">INSCRIPTION</a>
      </td>
		 <td>
        <a class="menu-btn" href="MODIFIER-FICHE-PAYE.php">MODIFIER LE PAYEMENT</a>
      </td>
		 <td>
        <a class="menu-btn" href="MODIFIER_LISTE_ELEVE.php">MODIFIER LA LISTE</a>
      </td>
		 
		 <td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT.php">POINT FILS PERSONNEL</a>
      </td>
		<td>
        <a class="menu-btn" href="MODIFICATION_PAR_CASE_A_COCHER.php">MODIFIER PAR CASE A COCHER</a>
      </td>
		 
    </tr>
  </table>
</body>
</html>

</head>
</head>

<body>
	<?php
// Paramètres de connexion
$servername = "localhost";
$username   = "caeis";
$password   = "INCLUSION";
$dbname     = "Centresourd";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Liste des classes
$classes = ["Maternelle","RBC","Sixieme","Tle","CI","CP","CE1","CE2","CM1","CM2","5eme","4eme","3eme","2nde","1ere"];

$selectedClasse = $_POST['Classe'] ?? null;
$download       = isset($_POST['download']); // bouton télécharger
$details        = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($selectedClasse)) {
    // Requête détaillée
    $sqlDetails = "
        SELECT 
            l.Id_liste,
            l.Nom_Prenoms,
            l.Sexe,
            l.Type,
            f.Classe,
            f.Regime,
            SUM(f.Montant_paye) AS total_paye,
            SUM(f.Montan_du) AS total_du
        FROM fiche_paie f
        JOIN tb_liste l ON f.Id_liste = l.Id_liste
        WHERE f.Classe = ?
        GROUP BY l.Id_liste, l.Nom_Prenoms, l.Sexe, l.Type, f.Classe, f.Regime
        ORDER BY l.Nom_Prenoms ASC
    ";
    $stmtDetails = $conn->prepare($sqlDetails);
    $stmtDetails->bind_param("s", $selectedClasse);
    $stmtDetails->execute();
    $details = $stmtDetails->get_result();
    $stmtDetails->close();

    // Si on clique sur "Télécharger", on génère un CSV
    if ($download && $details->num_rows > 0) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="paiements_'.$selectedClasse.'.csv"');

        $output = fopen("php://output", "w");
        // En-têtes
        fputcsv($output, ["ID Élève","Nom & Prénoms","Sexe","Type","Classe","Régime","Total payé","Total dû","Reste à payer"], ";");

        while($row = $details->fetch_assoc()) {
            fputcsv($output, [
                $row['Id_liste'],
                $row['Nom_Prenoms'],
                $row['Sexe'],
                $row['Type'],
                $row['Classe'],
                $row['Regime'],
                $row['total_paye'],
                $row['total_du'],
                $row['total_du'] - $row['total_paye']
            ], ";");
        }
        fclose($output);
        exit; // très important pour ne pas afficher le HTML après téléchargement
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Synthèse des paiements par classe</title>
</head>
<body>
  <form method="POST">
    <label for="Classe">Sélectionner une classe :</label>
    <select id="Classe" name="Classe" required>
      <option value="">--Choisir--</option>
      <?php foreach($classes as $c): ?>
        <option value="<?= $c ?>" <?= ($selectedClasse==$c)?'selected':'' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Afficher</button>
    <?php if ($selectedClasse): ?>
      <button type="submit" name="download" value="1">Télécharger CSV</button>
    <?php endif; ?>
  </form>
</body>
</html>

</body>
</html>