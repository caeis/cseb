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
        <a class="menu-btn" href="MODIFIER_LISTE_ELEVE.php">MODIFIER LE PAYEMENT</a>
      </td>
		 <td>
        <a class="menu-btn" href="FORMULAIRE_FICHE_ENSEIGNANT.php">POINT FILS PERSONNEL</a>
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
$host = "localhost";
$user = "caeis";
$pass = "INCLUSION";
$dbname = "Centresourd";

// Connexion à la base
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement de la mise à jour
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['Id_liste']);
    $nom = $conn->real_escape_string($_POST['Nom_Prenoms']);
    $sexe = $conn->real_escape_string($_POST['Sexe']);
    $type = $conn->real_escape_string($_POST['Type']);

    $sql = "UPDATE tb_liste 
            SET Nom_Prenoms='$nom', Sexe='$sexe', Type='$type' 
            WHERE Id_liste=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Mise à jour réussie !</p>";
    } else {
        echo "<p style='color:red;'>Erreur : " . $conn->error . "</p>";
    }
}

// Récupération des données pour le menu déroulant
$result = $conn->query("SELECT Id_liste, Nom_Prenoms FROM tb_liste ORDER BY Nom_Prenoms ASC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Correction des données - tb_liste</title>
</head>
<body>
    <h2>Corriger les données de tb_liste</h2>

    <!-- Menu déroulant -->
    <form method="get" action="">
        <label for="Id_liste">Choisir un enregistrement :</label>
        <select name="Id_liste" id="Id_liste" onchange="this.form.submit()">
            <option value="">-- Sélectionnez --</option>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?php echo $row['Id_liste']; ?>"
                    <?php if (isset($_GET['Id_liste']) && $_GET['Id_liste'] == $row['Id_liste']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($row['Nom_Prenoms']); ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <?php
    // Affichage du formulaire de correction si un Id est choisi
    if (isset($_GET['Id_liste']) && $_GET['Id_liste'] != "") {
        $id = intval($_GET['Id_liste']);
        $sql = "SELECT * FROM tb_liste WHERE Id_liste=$id";
        $res = $conn->query($sql);
        if ($res && $res->num_rows > 0) {
            $data = $res->fetch_assoc();
    ?>
        <form method="post" action="">
            <input type="hidden" name="Id_liste" value="<?php echo $data['Id_liste']; ?>">
            
            <label>Nom & Prénoms :</label><br>
            <input type="text" name="Nom_Prenoms" value="<?php echo htmlspecialchars($data['Nom_Prenoms']); ?>" required><br><br>
            
            <label>Sexe :</label><br>
            <select name="Sexe" required>
                <option value="M" <?php if ($data['Sexe']=="M") echo "selected"; ?>>Masculin</option>
                <option value="F" <?php if ($data['Sexe']=="F") echo "selected"; ?>>Féminin</option>
            </select><br><br>
            
            <label>Type :</label><br>
            <input type="text" name="Type" value="<?php echo htmlspecialchars($data['Type']); ?>" required><br><br>
            
            <button type="submit" name="update">Mettre à jour</button>
        </form>
    <?php
        }
    }
    $conn->close();
    ?>
</body>
</html>

	
</body>
</html>