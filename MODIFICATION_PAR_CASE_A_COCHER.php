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

<body>
	<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "centresourd";
$user = "caeis";
$pass = "INCLUSION";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Étape 1 : Formulaire de sélection de la classe
if (!isset($_POST['id_classe']) && !isset($_POST['update'])) {
    echo '<style>
            table.form-table {
                border-collapse: collapse;
                margin: 30px auto;
                width: 50%;
                background-color: #f9f9f9;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                font-family: Arial, sans-serif;
            }
            table.form-table th {
                background-color: #4CAF50;
                color: white;
                padding: 10px;
                text-align: center;
                font-size: 18px;
            }
            table.form-table td {
                padding: 15px;
                text-align: center;
            }
            select, button {
                padding: 8px 12px;
                font-size: 14px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            button {
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
                transition: 0.3s;
            }
            button:hover {
                background-color: #45a049;
            }
          </style>';

    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr><th colspan="2">Sélectionner une classe</th></tr>';
    echo '<tr><td>';
    echo '<select name="id_classe" id="id_classe">';
    $classes = $pdo->query("SELECT Id_classe, Classe FROM classe");
    while ($row = $classes->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($row['Id_classe']) . "'>" . htmlspecialchars($row['Classe']) . "</option>";
    }
    echo '</select>';
    echo '</td><td>';
    echo '<button type="submit">Afficher les élèves</button>';
    echo '</td></tr>';
    echo '</table>';
    echo '</form>';
}

// Étape 2 : Affichage des élèves de la classe sélectionnée
if (isset($_POST['id_classe']) && !isset($_POST['update'])) {
    $id_classe = $_POST['id_classe'];

    // Récupérer le nom de la classe
    $stmtClasse = $pdo->prepare("SELECT Classe FROM classe WHERE Id_classe = :id_classe");
    $stmtClasse->execute(['id_classe' => $id_classe]);
    $classeRow = $stmtClasse->fetch(PDO::FETCH_ASSOC);
    $classeNom = $classeRow['Classe'] ?? '';

    // Jointure Tb_liste + fiche_paie pour récupérer les élèves de la classe
    $stmt = $pdo->prepare("SELECT t.Id_liste, t.Nom_Prenoms, t.Sexe, t.Type,
                                  f.Scolarite, f.Montant_paye, f.Montan_du, f.Date_paie, f.Fp
                           FROM Tb_liste t
                           INNER JOIN fiche_paie f ON t.Id_liste = f.Id_liste
                           WHERE f.Classe = :classe");
    $stmt->execute(['classe' => $classeNom]);
    $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($eleves) {
        echo "<h3 style='text-align:center;color:#4CAF50;'>Élèves de la classe " . htmlspecialchars($classeNom) . "</h3>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='id_classe' value='" . htmlspecialchars($id_classe) . "'>";
        echo "<input type='hidden' name='classe_nom' value='" . htmlspecialchars($classeNom) . "'>";
        echo "<table border='1' cellpadding='5' style='margin:auto;border-collapse:collapse;width:90%;'>";
        echo "<tr style='background-color:#f2f2f2;'><th>Sélection</th><th>Nom & Prénoms</th><th>Sexe</th><th>Type</th>
                  <th>Scolarité</th><th>Montant payé</th><th>Montant dû</th><th>Date paiement</th><th>FP</th></tr>";

        foreach ($eleves as $eleve) {
            $id_liste = $eleve['Id_liste'];

            echo "<tr>";
            echo "<td><input type='checkbox' name='selection[]' value='" . $id_liste . "'></td>";
            echo "<td>" . htmlspecialchars($eleve['Nom_Prenoms']) . "</td>";
            echo "<td>" . htmlspecialchars($eleve['Sexe']) . "</td>";
            echo "<td>" . htmlspecialchars($eleve['Type']) . "</td>";

            echo "<td><input type='text' name='scolarite[$id_liste]' value='" . htmlspecialchars($eleve['Scolarite'] ?? '') . "'></td>";
            echo "<td><input type='text' name='montant_paye[$id_liste]' value='" . htmlspecialchars($eleve['Montant_paye'] ?? '') . "'></td>";
            echo "<td><input type='text' name='montan_du[$id_liste]' value='" . htmlspecialchars($eleve['Montan_du'] ?? '') . "'></td>";
            echo "<td><input type='date' name='date_paie[$id_liste]' value='" . htmlspecialchars($eleve['Date_paie'] ?? '') . "'></td>";
            echo "<td><input type='text' name='fp[$id_liste]' value='" . htmlspecialchars($eleve['Fp'] ?? '') . "'></td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "<div style='text-align:center;margin-top:15px;'><button type='submit' name='update'>Corriger</button></div>";
        echo "</form>";
    } else {
        echo "<p style='text-align:center;color:red;'>Aucun élève trouvé pour cette classe.</p>";
    }
}

// Étape 3 : Mise à jour des données avec confirmation ligne par ligne
if (isset($_POST['update']) && isset($_POST['selection'])) {
    $id_classe = $_POST['id_classe'];
    $classeNom = $_POST['classe_nom'];

    foreach ($_POST['selection'] as $id_liste) {
        $scolarite    = $_POST['scolarite'][$id_liste] ?? null;
        $montant_paye = $_POST['montant_paye'][$id_liste] ?? null;
        $montan_du    = $_POST['montan_du'][$id_liste] ?? null;
        $date_paie    = $_POST['date_paie'][$id_liste] ?? null;
        $fp           = $_POST['fp'][$id_liste] ?? null;

        $update = $pdo->prepare("UPDATE fiche_paie 
                                 SET Scolarite = :scolarite,
                                     Montant_paye = :montant_paye,
                                     Montan_du = :montan_du,
                                     Date_paie = :date_paie,
                                     Fp = :fp
                                 WHERE Id_liste = :id_liste AND Classe = :classe");
        $update->execute([
            'scolarite'    => $scolarite,
            'montant_paye' => $montant_paye,
            'montan_du'    => $montan_du,
            'date_paie'    => $date_paie,
            'fp'           => $fp,
            'id_liste'     => $id_liste,
            'classe'       => $classeNom
        ]);

        // Confirmation ligne par ligne
        $stmtNom = $pdo->prepare("SELECT Nom_Prenoms FROM Tb_liste WHERE Id_liste = :id_liste");
        $stmtNom->execute(['id_liste' => $id_liste]);
        $nomEleve = $stmtNom->fetchColumn();

        echo "Élève " . htmlspecialchars($nomEleve) . " corrigé avec succès.<br>";
    }
}
?>

</body>
</html>