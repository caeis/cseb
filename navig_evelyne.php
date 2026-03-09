<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<link href="PHP_images/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
</body>
</html>
<?php
function creer_navbar($num_deb = 0, $articles_par_page = 50, $nbre) {
// Création d’une barre de navigation
$page_courante = $_SERVER["PHP_SELF"];
if (($num_deb < 0) || (! is_numeric($num_deb))) {
$num_deb = 0;
}
$navbar = "";
$navbar_prec = "";
$navbar_suiv = "";
if ($nbre > $articles_par_page) {
$cpteur_nav = 0;
$nb_pages = 1;
$nav_passee = false;
while ($cpteur_nav < $nbre) {
// Est-on sur la page courante ?
if (($num_deb <= $cpteur_nav) && ($nav_passee != true)) {
$navbar .= "<b><a href=\"$page_courante?debut=$cpteur_nav\">
[$nb_pages]</a></b>";
$nav_passee = true;
// Faut-il un lien "Précédent" ?
if ($num_deb != 0) {
$num_prec = $cpteur_nav - $articles_par_page;
if ($num_prec < 1) {
$num_prec = 0;
}
$navbar_prec = "<a href=\"$page_courante?debut=$num_prec \">
&lt;&lt;Précédent - </a>";
}
$num_suiv = $articles_par_page + $cpteur_nav;
// Faut-il un lien "Suivant" ?
if ($num_suiv < $nbre) {
$navbar_suiv = "<a href=\" $page_courante?debut=$num_suiv\">
- Suivant&gt;&gt; </a><br>";
}
} else {
// Affichage normal.
$navbar .= "<a href=\"$page_courante?debut=$cpteur_nav\">
[$nb_pages]</a>";
}
$cpteur_nav += $articles_par_page;
$nb_pages++;
}
$navbar = $navbar_prec . $navbar . $navbar_suiv ;
return $navbar;
}
}
?>