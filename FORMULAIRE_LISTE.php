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
     
    </tr>
  </table>
</body>
</html>

	 <div class="container">
    
    
  </div>
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

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_prenoms = trim($_POST['Nom_Prenoms'] ?? '');
    $sexe        = $_POST['Sexe'] ?? '';
    $type        = $_POST['Type'] ?? '';

    if (empty($nom_prenoms) || empty($sexe) || empty($type)) {
        echo "<p style='color:red;'>Veuillez remplir tous les champs obligatoires.</p>";
    } else {
        // Vérifier si une entrée identique existe déjà
        $checkSql = "SELECT COUNT(*) as total FROM tb_liste WHERE Nom_Prenoms = ? AND Sexe = ? AND Type = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("sss", $nom_prenoms, $sexe, $type);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['total'] > 0) {
            echo "<p style='color:red;'>Cet enregistrement existe déjà. Insertion bloquée.</p>";
        } else {
            // Insertion
            $sql = "INSERT INTO tb_liste (Nom_Prenoms, Sexe, Type) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nom_prenoms, $sexe, $type);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>Enregistrement réussi !</p>";
            } else {
                echo "<p style='color:red;'>Erreur: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $checkStmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajout Élève - tb_liste</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
    .form-container { background: #fff; padding: 20px; border-radius: 8px; width: 400px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
    .btn { width: 100%; padding: 10px; background: #4bb3a7; border: none; border-radius: 6px; color: #fff; font-size: 16px; cursor: pointer; }
    .btn:hover { background: #3a8d80; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Ajouter un élève</h2>
    <form method="POST">
      <div class="form-group">
        <label for="Nom_Prenoms">Nom & Prénoms</label>
        <input type="text" id="Nom_Prenoms" name="Nom_Prenoms" required>
      </div>

      <div class="form-group">
        <label for="Sexe">Sexe</label>
        <select id="Sexe" name="Sexe" required>
          <option value="">--Choisir--</option>
          <option value="M">Masculin (M)</option>
          <option value="F">Féminin (F)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="Type">Type</label>
        <select id="Type" name="Type" required>
          <option value="">--Choisir--</option>
          <option value="Sourd">Sourd</option>
          <option value="Sourde">Sourde</option>
          <option value="Entendant">Entendant</option>
          <option value="Entendante">Entendante</option>
        </select>
      </div>

      <button type="submit" class="btn">Enregistrer</button>
    </form>
  </div>
</body>
</html>

</body>
</html>

</body>
</html>