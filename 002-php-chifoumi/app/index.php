<?php

$possibleChoice = ["rock", "paper", "scissors"];
$phpChoice = array_rand($possibleChoice);

$rules = [
    "rock"     => ["scissors"],
    "paper"    => ["rock"],
    "scissors" => ["paper"]
];

$AI = $possibleChoice;

if ($choice = "pierre") {
    $_GET["choice"] = $choice;
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
            <h1>Rock Paper Scissor</h1>
            <a href="?choice=rock" class="button">Rock</a>
            <a href="?choice=paper" class="button">Paper</a>
            <a href="?choice=scissor" class="button">Scissor</a>
        </main>
    </body>
</html>
HTML;

echo $page;