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
        <a class="menu-btn" href="Synthese_par_classe.php">POINT DES CONTRIBUTIONS PAR CLASSE</a>
      </td>
      <td>
        <a class="menu-btn" href="FORMULAIRE_LISTE.php">INSCRIPTION</a>
      </td>
		 <td>
        <a class="menu-btn" href="MODIFIER-FICHE-PAYE.php">MODIFIER LE PAYEMENT</a>
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

// Connexion à la base
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Charger les options pour la liste Id_liste
$listeOptions = $conn->query("SELECT Id_liste, Nom_Prenoms FROM tb_liste ORDER BY Nom_Prenoms ASC");

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_liste     = $_POST['Id_liste'] ?? null;
    $classe       = $_POST['Classe'] ?? null;
    $regime       = $_POST['Regime'] ?? null;
    $date_paie    = $_POST['Date_paie'] ?? null;
    $montant_paye = $_POST['Montant_paye'] ?? null;
    $montan_du    = $_POST['Montan_du'] ?? null;
    $fp           = $_POST['Fp'] ?? null; // Nouveau champ

    if (empty($id_liste) || empty($classe) || empty($regime) || empty($date_paie) || empty($montant_paye) || empty($fp)) {
        echo "<p style='color:red;'>Veuillez remplir tous les champs obligatoires.</p>";
    } else {
        // Vérifier si un paiement identique existe déjà
        $checkSql = "SELECT COUNT(*) as total FROM fiche_paie 
                     WHERE Id_liste = ? AND Date_paie = ? AND Montant_paye = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("isd", $id_liste, $date_paie, $montant_paye);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['total'] > 0) {
            echo "<p style='color:red;'>Paiement déjà enregistré pour cette personne, ce montant et cette date.</p>";
        } else {
            // Vérifier si l'élève a déjà une classe différente
            $classCheckSql = "SELECT Classe FROM fiche_paie WHERE Id_liste = ?";
            $classCheckStmt = $conn->prepare($classCheckSql);
            $classCheckStmt->bind_param("i", $id_liste);
            $classCheckStmt->execute();
            $classResult = $classCheckStmt->get_result();

            $classConflict = false;
            while ($classRow = $classResult->fetch_assoc()) {
                if ($classRow['Classe'] !== $classe) {
                    $classConflict = true;
                    break;
                }
            }

            if ($classConflict) {
                echo "<p style='color:red;'>Cet élève est déjà enregistré dans une autre classe. Attribution bloquée.</p>";
            } else {
                // Insertion avec Fp
                $sql = "INSERT INTO fiche_paie (Id_liste, Classe, Regime, Date_paie, Montant_paye, Montan_du, Fp) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($montan_du === '') {
                    $montan_du = null;
                }
                $stmt->bind_param("isssdds", $id_liste, $classe, $regime, $date_paie, $montant_paye, $montan_du, $fp);

                if ($stmt->execute()) {
                    echo "<p style='color:green;'>Enregistrement réussi !</p>";
                } else {
                    echo "<p style='color:red;'>Erreur: " . $stmt->error . "</p>";
                }

                $stmt->close();
            }

            $classCheckStmt->close();
        }

        $checkStmt->close();
    }
}

// Charger les paiements enregistrés pour le tableau récapitulatif
$recapSql = "SELECT f.Id_paie, l.Nom_Prenoms, f.Classe, f.Regime, f.Date_paie, f.Montant_paye, f.Montan_du, f.Fp 
             FROM fiche_paie f 
             JOIN tb_liste l ON f.Id_liste = l.Id_liste 
             ORDER BY f.Date_paie DESC";
$recapResult = $conn->query($recapSql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fiche de Paie</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
    .form-container { background: #fff; padding: 20px; border-radius: 8px; width: 500px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
    .btn { width: 100%; padding: 10px; background: #4bb3a7; border: none; border-radius: 6px; color: #fff; font-size: 16px; cursor: pointer; }
    .btn:hover { background: #3a8d80; }
    table { width: 95%; margin: 30px auto; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #4bb3a7; color: #fff; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Nouvelle Fiche de Paie</h2>
    <form method="POST">
      <div class="form-group">
        <label for="Id_liste">Nom & Prénoms</label>
        <select id="Id_liste" name="Id_liste" required>
          <option value="">--Choisir--</option>
          <?php while($row = $listeOptions->fetch_assoc()): ?>
            <option value="<?= $row['Id_liste'] ?>"><?= htmlspecialchars($row['Nom_Prenoms']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="Classe">Classe</label>
        <select id="Classe" name="Classe" required>
          <option value="">--Choisir--</option>
          <?php
          $classes = ["Maternelle","RBC","Sixieme","Tle","CI","CP","CE1","CE2","CM1","CM2","5eme","4eme","3eme","2nde","1ere"];
          foreach($classes as $c): ?>
            <option value="<?= $c ?>"><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="Regime">Régime</label>
        <select id="Regime" name="Regime" required>
          <option value="">--Choisir--</option>
          <option value="Interne">Interne</option>
          <option value="Externe">Externe</option>
        </select>
      </div>

      <div class="form-group">
        <label for="Fp">Fils de personnel ?</label>
        <select id="Fp" name="Fp" required>
          <option value="">--Choisir--</option>
          <option value="Fils_personnel">Fils_personnel</option>
          <option value="Non">Non</option>
        </select>
      </div>

      <div class="form-group">
        <label for="Date_paie">Date de Paie</label>
        <input type="date" id="Date_paie" name="Date_paie" required>
      </div>

      <div class="form-group">
        <label for="Montant_paye">Montant payé</label>
        <input type="number" step="0.01" id="Montant_paye" name="Montant_paye" required>
      </div>

      <div class="form-group">
        <label for="Montan_du">Montant dû (facultatif)</label>
        <input type="number" step="0.01" id="Montan_du" name="Montan_du">
      </div>

      <button type="submit" class="btn">Enregistrer</button>
    </form>
  </div>
</body>
</html>

</html>