<?php
session_start();
// Récupère le chemin dans l'URL (par exemple : /albums ou /)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Supprime les éventuels `/` en début et fin pour normaliser les routes
$path = trim($path, '/');

// Routeur avec un switch
switch ($path) {
    case '':
    case 'home':
        include __DIR__ . '/views/home.php';
        break;
    case 'collections':
        include __DIR__ . '/views/albums.php';
        break;
    case 'news':
        include __DIR__ . '/views/news.php';
        break;
    case 'connect':
        include __DIR__ . '/views/connect.php';
        break;
    case 'setSession':
        include __DIR__ . '/managers/setSession.php';
        break;
    case 'logout':
        include __DIR__ . '/managers/logout.php';
        break;
    case 'addAlbums':
        include __DIR__ . '/views/addAlbums.php';
        break;


    default: // Page non trouvée
        include __DIR__ . '/views/404.php';
        break;
}
