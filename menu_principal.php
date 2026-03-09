<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Document sans titre</title>
</head>

<body>
	<!-- menu.php -->
<nav>
    <ul>
        <li><a href="menu_principal.php">Accueil</a></li>
        <li><a href="insertionheures.php">REMPLIR LA FICHE</a></li>
        <li><a href="synthese_par_prof.php">Nombre d'heures par prof</a></li>
        <li><a href="Listeprofs.php">Liste des professeurs</a></li>
        <li><a href="Rapports.php">Rapports</a></li>
        <li><a href="Contact.php">Contact</a></li>
    </ul>
</nav>

<style>
    /* Style du menu principal */
    nav {
        background-color: #3498db;
        padding: 10px;
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: flex-start;
    }

    nav ul li {
        margin-right: 20px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 8px 12px;
        border-radius: 4px;
        transition: background-color 0.3s, color 0.3s;
    }

    nav ul li a:hover {
        background-color: #2980b9;
        color: #f1c40f;
    }
</style>

	
</body>
</html>