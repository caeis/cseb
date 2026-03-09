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
        <a class="menu-btn" href="MODIFIER_LISTE_ELEVE.php">MODIFIER LA LISTE</a>
      </td>
		 <td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT.php">POINT DES FILS D'ENSEIGNANT</a>
      </td>
		 <td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT.php">POINT FILS PERSONNEL</a>
      </td>
		 <td>
        <a class="menu-btn" href="MODIFICATION_PAR_CASE_A_COCHER.php">MODIFICATION PAR CASE A COCHER</a>
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

// Récupération de la liste des élèves
$sqlEleves = "SELECT Id_liste, Nom_Prenoms FROM tb_liste ORDER BY Nom_Prenoms ASC";
$eleves = $conn->query($sqlEleves);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['Id_liste'])) {
    $idListe = $_POST['Id_liste'];
    $montantPaye = $_POST['Montant_paye'] ?? 0;
    $montantDu   = $_POST['Montan_du'] ?? 0;

    // Mise à jour des montants dans fiche_paie
    $sqlUpdate = "UPDATE fiche_paie SET Montant_paye = ?, Montan_du = ? WHERE Id_liste = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("ddi", $montantPaye, $montantDu, $idListe);

    if ($stmt->execute()) {
        $message = "Mise à jour effectuée avec succès pour l'élève sélectionné.";
    } else {
        $message = "Erreur lors de la mise à jour : " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Correction des montants par élève</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
    .form-container { background: #fff; padding: 20px; border-radius: 8px; width: 500px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .btn { width: 100%; padding: 10px; background: #4bb3a7; border: none; border-radius: 6px; color: #fff; font-size: 16px; cursor: pointer; }
    .btn:hover { background: #3a8d80; }
    .message { text-align:center; margin-top:20px; font-weight:bold; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Corriger le montant payé et dû</h2>
    <form method="POST">
      <label for="Id_liste">Sélectionner un élève :</label>
      <select id="Id_liste" name="Id_liste" required>
        <option value="">--Choisir--</option>
        <?php if ($eleves && $eleves->num_rows > 0): ?>
          <?php while($row = $eleves->fetch_assoc()): ?>
            <option value="<?= $row['Id_liste'] ?>"><?= htmlspecialchars($row['Nom_Prenoms']) ?></option>
          <?php endwhile; ?>
        <?php endif; ?>
      </select><br><br>

      <label for="Montant_paye">Nouveau montant payé :</label>
      <input type="number" step="0.01" id="Montant_paye" name="Montant_paye" required><br><br>

      <label for="Montan_du">Nouveau montant dû :</label>
      <input type="number" step="0.01" id="Montan_du" name="Montan_du" required><br><br>

      <button type="submit" class="btn">Mettre à jour</button>
    </form>
  </div>

  <?php if (!empty($message)): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
</body>
</html>

</body>
</html>