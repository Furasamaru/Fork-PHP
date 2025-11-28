<?php
//options de charactères générés
$tabMaj = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
$tabMin = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
$tabNumb = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
$tabSymb = ["!", "\"", "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~"];

/**
 * Generates a string of HTML <option> elements for a select dropdown, with one option marked as selected.
 *
 * @param int|string $selected The value to be marked as selected in the dropdown.
 * @return string The generated HTML string containing <option> elements.
 */
function generateSelectOptions($selected = 12): string
{
    // on initialise une variable html vide
    $html = "";

    // utilisation de la fonction range pour générer un tableau de valeurs
    $options = range(8, 42);

    // pour chaque nombre de 8 à 42
    foreach ($options as $value) {
        // si le nombre courant est celui sélectionné, on ajoute l'attribut selected à l'option
        $attribute = "";
        if ((int) $value == (int) $selected) {
            $attribute = "selected";
        }

        // on crée une option avec l'attribut et la valeur'
        $html .= "<option $attribute value=\"$value\">$value</option>";
    }

    return $html;
}

// Valeurs par défaut (1er chargement)
$range = 12;
$useAlphaMaj = 0;
$useAlphaMin = 0;
$useNumb = 0;
$useSymb = 0;
$createPassword = "";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $range = (int) ($_POST["size"] ?? 12);
    $useAlphaMaj = $_POST["use-alpha-maj"] ?? 0;
    $useAlphaMin = $_POST["use-alpha-min"] ?? 0;
    $useNumb = $_POST["use-numb"] ?? 0;
    $useSymb = $_POST["use-symb"] ?? 0;

    $pool = [];

    if ($useAlphaMaj) $pool = array_merge($pool, $tabMaj);
    if ($useAlphaMin) $pool = array_merge($pool, $tabMin);
    if ($useNumb) $pool = array_merge($pool, $tabNumb);
    if ($useSymb) $pool = array_merge($pool, $tabSymb);

    if (empty($pool)) {
        $errorMessage = "Veuillez sélectionner au moins une option";
    } else {
        for ($i = 0; $i < $range; $i++) {
            $createPassword .=$pool[random_int(0, (count($pool) - 1))];
        }

    }


}

if (isset($_POST["createPassword"])) {

}

if($tabMaj == TRUE) {
    array_rand($tabMaj);
} else {return null;}

if ($tabMin == TRUE) {
    array_rand($tabMin);
} else {return null;}

if ($tabNumb == TRUE) {
    array_rand($tabNumb);
} else {return null;}

if ($tabSymb == TRUE) {
    array_rand($tabSymb);
} else {return null;}

$optionsHTML = generateSelectOptions($range ?? 12);

$page =<<<HTML
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générateur MDP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="POST" action="index.php">
    <h1>Générateur de mot de passe</h1>
    <div>
    <label for="size" class="form-label">Taille</label>
    <select class="form-select" aria-label="Default select example" name="size">
    <option value="$optionsHTML"></option> 
    </select>
</div>
    <section>
        <label>
            <input type="checkbox" class="check" value="1" name="use-alpha-maj" {$useAlphaMaj}>
            Majuscules
        </label>
    </section>
    <section>
        <label>
            <input type="checkbox" class="check" value="1" name="use-alpha-min" {$useAlphaMin}>
            Minuscules
        </label>
    </section>
    <section>
        <label>
            <input type="checkbox" class="check" value="1" name="use-numb" {$useNumb}>
            Nombre
        </label>
    </section>
    <section>
        <label>
            <input type="checkbox" class="check" value="1" name="use-symb" {$useSymb}>
            Charactères spéciaux
        </label>
    </section>
    <div>
        $errorMessage
    </div>
    <h2>mot de passe généré :</h2>
    <div class="generated-password">
        <p><strong>$createPassword</strong></p>
    </div>
    <div class="?CreatePassword">
        <button type="submit" class="btn" name="createPassword">Créer le mot de passe</button>
    </div>
    </form>
</body>
</html>
HTML;

echo $page;