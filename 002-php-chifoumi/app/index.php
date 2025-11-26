<?php

//choix possible pour php
$possibleChoice = ["rock", "paper", "scissors"];

//règles du pierre feuille ciseaux
$rules = [
    "rock"     => ["scissors"],
    "paper"    => ["rock"],
    "scissors" => ["paper"]
];

//différents types de choix:
$userChoice = "";
/*$AIChoice = array_rand($possibleChoice);*/
$AIChoice = $possibleChoice[array_rand($possibleChoice)];
$emptyChoice = null;

$result = "";

//GET côté php
if (isset($_GET["userChoice"])) {
    //gain de temps et plus de compréhension en créant le $userGetChoice
    $userGetChoice = $_GET["userChoice"];
    //tous les choix possibles
    if (!in_array($userGetChoice, $possibleChoice)) {
        echo "Raté petit malin";
    } else if ($userGetChoice == $emptyChoice || $AIChoice == $emptyChoice) {
        echo "Faites un choix :";
    } else if ($userGetChoice === $AIChoice) {
        $result = "match nul";
    } else if (in_array($userGetChoice, $rules[$AIChoice])) {
        $result = "Vous avez gagné";
    } else {
        $result = "Vous avez perdu";
    }
}

$page = <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rock Paper Scissor</title>
</head>
    <body>
        <main>
        <form method="get" action="index.php">
            <h1>Rock Paper Scissor</h1>
            <a href="?userChoice=rock" class="button">Rock</a>
            <a href="?userChoice=paper" class="button">Paper</a>
            <a href="?userChoice=scissors" class="button">Scissors</a>
            </form>
            <h2>$result</h2>
        </main>
    </body>
</html>
HTML;

echo $page;