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
        <a class="menu-btn" href="FORMULAIRE_LISTE.php">INSCRIPTION</a>
      </td>
		 <td>
        <a class="menu-btn" href="MODIFIER-FICHE-PAYE.php">MODIFIER LE PAYEMENT</a>
      </td>
		<td>
        <a class="menu-btn" href="MODIFIER_LISTE_ELEVE.php">MODIFIER LA LISTE</a>
      </td>
		<td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT_PAR_CLASSE.php">POINT ENFANTS DU PERSONNEL_PAR_CLASSE</a> 
      </td>
		<td><a class="menu-btn" href="MODIFICATION_PAR_CASE_A_COCHER.php">POINT ENFANTS DU PERSONNEL_PAR_CLASSE</a></td>
		<td><a class="menu-btn" href="TELECHARGEMENT.php">TECHARGER </a></td>
    </tr>
  </table>
</body>
</html>

</head>

<body>
	<?php
// Paramètres de connexion
$servername = "localhost";
$username   = "caeis";
$password   = "INCLUSION";
$dbname     = "Centresourd";

// Connexion
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Liste des classes
$classes = ["Maternelle","RBC","Sixieme","Tle","CI","CP","CE1","CE2","CM1","CM2","5eme","4eme","3eme","2nde","1ere"];

$selectedClasse = $_POST['Classe'] ?? null;
$synthese = null;
$details = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($selectedClasse)) {
    // Requête de synthèse
    $sqlSynthese = "
        SELECT 
            COUNT(DISTINCT f.Id_liste) AS effectif_total,
            SUM(f.Montant_paye) AS total_paye,
            SUM(f.Montan_du) AS total_du,
            SUM(CASE WHEN l.Sexe='F' THEN 1 ELSE 0 END) AS filles,
            SUM(CASE WHEN l.Sexe='M' THEN 1 ELSE 0 END) AS garcons,
            SUM(CASE WHEN l.Type='Sourd' THEN 1 ELSE 0 END) AS sourds,
            SUM(CASE WHEN l.Type='Sourde' THEN 1 ELSE 0 END) AS sourdes,
            SUM(CASE WHEN l.Type='Entendant' THEN 1 ELSE 0 END) AS entendants,
            SUM(CASE WHEN l.Type='Entendante' THEN 1 ELSE 0 END) AS entendantes,
            SUM(CASE WHEN f.Regime='Interne' THEN 1 ELSE 0 END) AS internes,
            SUM(CASE WHEN f.Regime='Externe' THEN 1 ELSE 0 END) AS externes
        FROM fiche_paie f
        JOIN tb_liste l ON f.Id_liste = l.Id_liste
        WHERE f.Classe = ?
    ";
    $stmtSynthese = $conn->prepare($sqlSynthese);
    $stmtSynthese->bind_param("s", $selectedClasse);
    $stmtSynthese->execute();
    $synthese = $stmtSynthese->get_result()->fetch_assoc();
    $stmtSynthese->close();

    // Requête détaillée : total payé par élève
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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Synthèse des paiements par classe</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
    .form-container { background: #fff; padding: 20px; border-radius: 8px; width: 500px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    table { width: 95%; margin: 20px auto; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #4bb3a7; color: #fff; }
    .btn { width: 100%; padding: 10px; background: #4bb3a7; border: none; border-radius: 6px; color: #fff; font-size: 16px; cursor: pointer; }
    .btn:hover { background: #3a8d80; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Synthèse des paiements par classe</h2>
    <form method="POST">
      <label for="Classe">Sélectionner une classe :</label>
      <select id="Classe" name="Classe" required>
        <option value="">--Choisir--</option>
        <?php foreach($classes as $c): ?>
          <option value="<?= $c ?>" <?= ($selectedClasse==$c)?'selected':'' ?>><?= $c ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn">Afficher</button>
    </form>
  </div>

  <?php if ($synthese): ?>
    <h2 style="text-align:center;">Tableau de synthèse - Classe <?= htmlspecialchars($selectedClasse) ?></h2>
    <table>
      <tr>
        <th>Effectif total</th>
        <th>Total payé</th>
        <th>Total dû</th>
        <th>Filles</th>
        <th>Garçons</th>
        <th>Sourds</th>
        <th>Sourdes</th>
        <th>Entendants</th>
        <th>Entendantes</th>
        <th>Internes</th>
        <th>Externes</th>
      </tr>
      <tr>
        <td><?= $synthese['effectif_total'] ?? 0 ?></td>
        <td><?= $synthese['total_paye'] ?? 0 ?></td>
        <td><?= $synthese['total_du'] ?? 0 ?></td>
        <td><?= $synthese['filles'] ?? 0 ?></td>
        <td><?= $synthese['garcons'] ?? 0 ?></td>
        <td><?= $synthese['sourds'] ?? 0 ?></td>
        <td><?= $synthese['sourdes'] ?? 0 ?></td>
        <td><?= $synthese['entendants'] ?? 0 ?></td>
        <td><?= $synthese['entendantes'] ?? 0 ?></td>
        <td><?= $synthese['internes'] ?? 0 ?></td>
        <td><?= $synthese['externes'] ?? 0 ?></td>
      </tr>
    </table>
  <?php endif; ?>

  <?php if ($details && $details->num_rows > 0): ?>
    <h2 style="text-align:center;">Détails des paiements - Classe <?= htmlspecialchars($selectedClasse) ?></h2>
    <table>
      <tr>
        <th>ID Élève</th>
        <th>Nom & Prénoms</th>
        <th>Sexe</th>
        <th>Type</th>
        <th>Classe</th>
        <th>Régime</th>
        <th>Total payé</th>
        <th>Total dû</th>
        <th>Reste à payer</th>
      </tr>
      <?php while($row = $details->fetch_assoc()): ?>
        <tr>
          <td><?= $row['Id_liste'] ?></td>
          <td><?= htmlspecialchars($row['Nom_Prenoms']) ?></td>
          <td><?= htmlspecialchars($row['Sexe']) ?></td>
          <td><?= htmlspecialchars($row['Type']) ?></td>
          <td><?= htmlspecialchars($row['Classe']) ?></td>
          <td><?= htmlspecialchars($row['Regime']) ?></td>
          <td><?= htmlspecialchars($row['total_paye']) ?></td>
          <td><?= htmlspecialchars($row['total_du']) ?></td>
          <td><?= ($row['total_du'] - $row['total_paye']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php elseif ($selectedClasse): ?>
    <p style="text-align:center;color:red;">Aucun paiement enregistré pour cette classe.</p>
  <?php endif; ?>
</body>
</html>

</body>
</html>