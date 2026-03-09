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
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT_PAR_CLASSE.php">POINT PAR CLASSE SANS FILS PERSONNEL</a>
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

$totalDu = 0;
$result = null;

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['afficher'])) {
    // Requête pour récupérer tous les fils de personnel
    $sql = "
        SELECT f.Id_paie, l.Id_liste, l.Nom_Prenoms, l.Sexe, l.Type,
               f.Classe, f.Regime, f.Frais_inscription, f.Scolarite,
               f.Montant_paye, f.Montan_du, f.Date_paie, f.Fp
        FROM Fiche_paie f
        INNER JOIN tb_liste l ON f.Id_liste = l.Id_liste
        WHERE f.Fp = 'Fils_personnel'
        ORDER BY l.Nom_Prenoms ASC
    ";
    $result = $conn->query($sql);

    // Calcul du montant total dû
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $totalDu += $row['Montan_du'];
        }
        // Revenir au début pour affichage
        $result->data_seek(0);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fils de personnel - Montant dû</title>
    <style>
        table { border-collapse: collapse; margin: auto; }
        th, td { border: 1px solid #333; padding: 6px 12px; text-align: center; }
        h2 { margin-top: 20px; text-align: center; }
    </style>
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

$result = null;

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['afficher'])) {
    // Requête pour récupérer les enfants du personnel par classe
    $sql = "
        SELECT f.Classe,
               l.Id_liste,
               l.Nom_Prenoms,
               l.Sexe,
               l.Type,
               f.Regime,
               f.Montant_paye,
               f.Montan_du,
               f.Date_paie,
               f.Fp
        FROM Fiche_paie f
        INNER JOIN tb_liste l ON f.Id_liste = l.Id_liste
        WHERE f.Fp = 'Fils_personnel'
        ORDER BY f.Classe, l.Nom_Prenoms ASC
    ";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enfants du personnel par classe</title>
    <style>
        table { border-collapse: collapse; margin: auto; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 12px; text-align: center; }
        h2 { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <h2>Afficher les enfants du personnel par classe</h2>
    <form method="post" action="">
        <button type="submit" name="afficher">Afficher</button>
    </form>

    <?php
    if ($result && $result->num_rows > 0) {
        $currentClasse = null;
        $totalDuClasse = 0;

        while ($row = $result->fetch_assoc()) {
            // Changement de classe -> afficher tableau précédent
            if ($currentClasse !== $row['Classe']) {
                if ($currentClasse !== null) {
                    // Afficher total dû pour la classe précédente
                    echo "<tr><td colspan='8' style='text-align:right;font-weight:bold;'>Total dû pour la classe $currentClasse : $totalDuClasse</td></tr>";
                    echo "</table>";
                    $totalDuClasse = 0;
                }
                // Nouvelle classe
                $currentClasse = $row['Classe'];
                echo "<h2>Classe " . htmlspecialchars($currentClasse) . "</h2>";
                echo "<table>
                        <tr>
                            <th>ID Élève</th>
                            <th>Nom & Prénoms</th>
                            <th>Sexe</th>
                            <th>Type</th>
                            <th>Régime</th>
                            <th>Montant payé</th>
                            <th>Montant dû</th>
                            <th>Date de paiement</th>
                        </tr>";
            }

            // Ligne élève
            echo "<tr>
                    <td>{$row['Id_liste']}</td>
                    <td>" . htmlspecialchars($row['Nom_Prenoms']) . "</td>
                    <td>" . htmlspecialchars($row['Sexe']) . "</td>
                    <td>" . htmlspecialchars($row['Type']) . "</td>
                    <td>" . htmlspecialchars($row['Regime']) . "</td>
                    <td>" . htmlspecialchars($row['Montant_paye']) . "</td>
                    <td>" . htmlspecialchars($row['Montan_du']) . "</td>
                    <td>" . htmlspecialchars($row['Date_paie']) . "</td>
                  </tr>";

            // Ajout au total dû de la classe
            $totalDuClasse += $row['Montan_du'];
        }

        // Afficher total dû pour la dernière classe
        echo "<tr><td colspan='8' style='text-align:right;font-weight:bold;'>Total dû pour la classe $currentClasse : $totalDuClasse</td></tr>";
        echo "</table>";
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        echo "<p style='text-align:center;color:red;'>Aucun enfant du personnel trouvé.</p>";
    }
    ?>
</body>
</html>


</body>
</html>