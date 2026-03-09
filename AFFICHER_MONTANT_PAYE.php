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
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT_PAR_CLASSE.php">POINT PAR CLASSE SANS FILS PERSONNEL </a>
      </td>
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

$nomEleve = $_POST['Nom_Prenoms'] ?? null;
$resultat = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($nomEleve)) {
    // Requête pour récupérer le total payé et dû par élève
    $sql = "
        SELECT 
            l.Nom_Prenoms,
            SUM(f.Montant_paye) AS total_paye,
            SUM(f.Montan_du) AS total_du
        FROM fiche_paie f
        JOIN tb_liste l ON f.Id_liste = l.Id_liste
        WHERE l.Nom_Prenoms = ?
        GROUP BY l.Nom_Prenoms
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nomEleve);
    $stmt->execute();
    $resultat = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Montant payé et dû par élève</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
    .form-container { background: #fff; padding: 20px; border-radius: 8px; width: 500px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    table { width: 70%; margin: 20px auto; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #4bb3a7; color: #fff; }
    .btn { width: 100%; padding: 10px; background: #4bb3a7; border: none; border-radius: 6px; color: #fff; font-size: 16px; cursor: pointer; }
    .btn:hover { background: #3a8d80; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Recherche du montant payé et dû par élève</h2>
    <form method="POST">
      <label for="Nom_Prenoms">Nom & Prénoms de l'élève :</label>
      <input type="text" id="Nom_Prenoms" name="Nom_Prenoms" required>
      <button type="submit" class="btn">Afficher</button>
    </form>
  </div>

  <?php if ($resultat): ?>
    <h2 style="text-align:center;">Résultats pour <?= htmlspecialchars($resultat['Nom_Prenoms']) ?></h2>
    <table>
      <tr>
        <th>Nom & Prénoms</th>
        <th>Total payé</th>
        <th>Total dû</th>
        <th>Reste à payer</th>
      </tr>
      <tr>
        <td><?= htmlspecialchars($resultat['Nom_Prenoms']) ?></td>
        <td><?= $resultat['total_paye'] ?? 0 ?></td>
        <td><?= $resultat['total_du'] ?? 0 ?></td>
        <td><?= ($resultat['total_du'] - $resultat['total_paye']) ?></td>
      </tr>
    </table>
  <?php elseif ($nomEleve): ?>
    <p style="text-align:center;color:red;">Aucun paiement trouvé pour cet élève.</p>
  <?php endif; ?>
</body>
</html>

</body>
</html>