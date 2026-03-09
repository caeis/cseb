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
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT.php">MODIFIER LA LISTE</a>
      </td>
		 <td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT.php">POINT FILS PERSONNEL</a>
      </td>
		 <td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT_PAR_CLASSE.php">POINT FILS PERSONNEL PAR CLASSE</a>
      </td>
    </tr>
  </table>
</body>
</html>

</head>
<body>
	<?php
// Paramètres de connexion
$host = "localhost";
$dbname = "Centresourd";
$user = "caeis";
$pass = "INCLUSION";

// Connexion à la base
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération des classes disponibles
$classes = [];
$resClasses = $conn->query("SELECT Id_classe, Classe FROM classe ORDER BY Classe ASC");
if ($resClasses && $resClasses->num_rows > 0) {
    while ($row = $resClasses->fetch_assoc()) {
        $classes[] = $row;
    }
}

$synthese = null;
$selectedClasse = $_POST['classe'] ?? null;

if ($selectedClasse) {
    // Requête de synthèse pour la classe choisie
    $sqlSynthese = "
        SELECT 
            SUM(Montan_du) AS total_du_classe,
            SUM(CASE WHEN Fp='Fils_personnel' THEN Montan_du ELSE 0 END) AS total_du_fp
        FROM Fiche_paie
        WHERE Classe = ?
    ";
    $stmt = $conn->prepare($sqlSynthese);
    $stmt->bind_param("s", $selectedClasse);
    $stmt->execute();
    $synthese = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Synthèse des montants dus par classe</title>
    <style>
        table { border-collapse: collapse; margin: auto; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 12px; text-align: center; }
        h2 { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Choisir une classe</h2>
    <form method="post" action="">
        <label for="classe">Classe :</label>
        <select name="classe" id="classe" required>
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($classes as $classe): ?>
                <option value="<?= htmlspecialchars($classe['Classe']) ?>" 
                    <?= ($selectedClasse === $classe['Classe']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($classe['Classe']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Afficher</button>
    </form>

    <?php if ($synthese): ?>
        <h2>Synthèse - Classe <?= htmlspecialchars($selectedClasse) ?></h2>
        <table>
            <tr>
                <th>Total dû (tous les apprenants)</th>
                <th>Total dû (enfants du personnel)</th>
            </tr>
            <tr>
                <td><?= $synthese['total_du_classe'] ?? 0 ?></td>
                <td><?= $synthese['total_du_fp'] ?? 0 ?></td>
            </tr>
        </table>
    <?php elseif ($selectedClasse): ?>
        <p style="text-align:center;color:red;">Aucune donnée trouvée pour cette classe.</p>
    <?php endif; ?>
</body>
</html>

</body>
</html>