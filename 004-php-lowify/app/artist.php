<?php

// -- importation des librairies à l'aide de require_once
require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';

const ERROR_REQUEST_MESSAGE ="Erreur lors de la requête à la base de données : ";

// -- initialisation de la connexion à la base de donnée

//try/catch en cas d'erreur et message d'erreur au cas où
try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    echo "Erreur lors de la connexion à la base de données : " . $ex->getMessage();
    exit;
}

//vérifie si l'id est présente, donne un message d'erreur au cas où
if (empty($_GET['id'])) {
    header("Location: error.php?message=Aucun-artiste-spécifié");
    exit;
}
$verifyArtistId = (int)$_GET['id'];

function formatListeners($n) {
    $n = (float)$n;

    if ($n >= 1000000000) {
        $v = $n / 1000000000;
        $s = number_format($v, 1, '.', '');
        return (substr($s, -2) === '.0') ? (string)(int)$v . 'B' : $s . 'B';
    }

    if ($n >= 1000000) {
        $v = $n / 1000000;
        $s = number_format($v, 1, '.', '');
        return (substr($s, -2) === '.0') ? (string)(int)$v . 'M' : $s . 'M';
    }

    $v = $n >= 1000 ? $n / 1000 : $n;
    $s = $n >= 1000 ? number_format($v, 1, '.', '') : (string)(int)$v;
    return (substr($s, -2) === '.0') ? (string)(int)$v . ($n >= 1000 ? 'k' : '') : $s . ($n >= 1000 ? 'k' : '');
}

function formatDuration($seconds): string
{
    $seconds = (int)$seconds;
    $min = floor($seconds / 60);
    $sec = $seconds % 60;
    return sprintf('%02d:%02d', $min, $sec);
}


// -- on récupère toutes les infos de l'artiste concerné depuis la base de données
$artistInfos = [];

//try/catch en cas d'erreur et message d'erreur au cas où
//infos sur l'artiste en lui-même
try {
    $ArtistInfos = $db->executeQuery(<<<SQL
    SELECT
        a.id AS artist_id,
        a.name AS artist_name,
        a.biography AS artist_biography,
        a.cover AS artist_cover,
        a.monthly_listeners AS artist_monthly_listeners
    FROM artist a
    WHERE a.id = :id
SQL,
        [':id' => $verifyArtistId]
    );

} catch (PDOException $ex) {
    echo ERROR_REQUEST_MESSAGE . $ex->getMessage();
    exit;
}

// -- on crée une variable pour contenir le HTML qui représentera les infos de l'artiste
$artistInfosHTML = "";

if(!empty($ArtistInfos)) {
    $artist = $ArtistInfos[0]; // prends le premier (et unique) élément
    $artistId = $artist['artist_id'];
    $artistName = $artist['artist_name'];
    $artistBio = $artist['artist_biography'];
    $artistCover = $artist['artist_cover'];
    $artistMonthlyListeners = $artist['artist_monthly_listeners'];

    $formattedListeners = formatListeners($artistMonthlyListeners);

// -- on ajoute une carte HTML représentant l'artiste courant
    $artistInfosHTML = <<<HTML
<div class="d-flex align-items-center gap-4 p-4 mb-4 rounded bg-secondary">
    <img src="$artistCover" class="rounded-circle shadow"
         style="width:150px;height:150px;object-fit:cover" alt="$artistName">

    <div>
        <h2 class="fw-bold">$artistName</h2>
        <p class="text-white-50 mb-2">$artistBio</p>
        <p class="fw-semibold">$formattedListeners auditeurs mensuels</p>
    </div>
</div>
HTML;
} else {
    header("Location: error.php?message=" . ERROR_REQUEST_MESSAGE);
}




$songsInfos = [];

try {
    $SongsInfos = $db->executeQuery(<<<SQL
    SELECT
        s.id AS song_id,
        s.name AS song_name,
        s.duration AS song_duration,
        s.note AS song_note,
        al.cover AS album_cover,
        al.name AS album_name
    FROM song s
    INNER JOIN album al ON s.album_id = al.id
    WHERE s.artist_id = :id
    ORDER BY s.note DESC
    LIMIT 5
SQL,
        [':id' => $verifyArtistId]
    );

} catch (PDOException $ex) {
    echo ERROR_REQUEST_MESSAGE . $ex->getMessage();
    exit;
}

// HTML pour toutes les chansons
$songsInfosHTML = '<h3 class="mt-5 mb-3">Top Titres</h3>';
$songsInfosHTML .= '<div class="list-group list-group-flush">';

foreach ($SongsInfos as $song) {
    $songName = $song['song_name'];
    $albumName = $song['album_name'];
    $albumCover = $song['album_cover'];
    $songDurationFormatted = formatDuration($song['song_duration']);
    $songNote = $song['song_note'];

    $songsInfosHTML .= <<<HTML
        <div class="list-group-item bg-dark text-white border-secondary d-flex align-items-center">
            <img src="$albumCover" class="rounded me-3"
                 style="width:55px;height:55px;object-fit:cover" alt="">

            <div class="flex-grow-1">
                <div class="fw-bold">$songName</div>
                <small class="text-white-50">$albumName</small>
            </div>

            <div class="text-end">
                <div class="small text-white-50">$songDurationFormatted</div>
                <div class="small">$songNote / 10</div>
            </div>
        </div>
HTML;
}

$songsInfosHTML .= "</div>";


$albumsInfos = [];

try {
    $albumsInfos = $db->executeQuery(<<<SQL
    SELECT *
    FROM album
    WHERE artist_id = :id
    ORDER BY release_date DESC
SQL,
        [':id' => $verifyArtistId]
    );

} catch (PDOException $ex) {
    echo ERROR_REQUEST_MESSAGE . $ex->getMessage();
    exit;
}

$albumsInfosHTML = '<h3 class="mt-5 mb-3">Albums</h3>';
$albumsInfosHTML .= '<div class="row row-cols-1 row-cols-md-3 g-4">';

foreach ($albumsInfos as $album) {
    $albumName = $album['name'];
    $albumCover = $album['cover'];
    $releaseDate = $album['release_date'];

    $albumsInfosHTML .= <<<HTML
        <div class="col">
            <div class="card bg-secondary text-white h-100 shadow-sm border-dark">
                <img src="$albumCover" class="card-img-top"
                     style="height:200px;object-fit:cover">

                <div class="card-body">
                    <h5 class="card-title">$albumName</h5>
                    <p class="card-text text-white-50 small">Sortie : $releaseDate</p>
                </div>
            </div>
        </div>
HTML;
}

$albumsInfosHTML .= "</div>";


// -- on crée la structure HTML de notre page
// en injectant le HTML correspondant à la liste des artistes
$html = <<<HTML
<div class="container py-4">

    <a href="index.php" class="text-white-50 mb-3 d-inline-block">&lt; Retour à l'accueil</a>

    <h1 class="mb-4">Artiste</h1>

    {$artistInfosHTML}
    {$songsInfosHTML}
    {$albumsInfosHTML}

</div>
HTML;

echo (new HTMLPage(title: "$artistInfosHTML - Lowify"))
    ->setupBootstrap([
        "class" => "bg-dark text-white p-4",
        "data-bs-theme " => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($html)
    ->render();