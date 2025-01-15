<?php
session_start();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$path = trim($path, '/');
if(preg_match('#^album/([a-zA-Z0-9]+)$#', $path)) {
    $path = "album";
}
if(preg_match('#^artist/([a-zA-Z0-9]+)$#', $path)) {
    $path = "artist";
}
if(preg_match('#^playlist/([a-zA-Z0-9]+)$#', $path)) {
    $path = "playlist";
}
if (preg_match('#^playlist/([a-zA-Z0-9]+)/edit$#', $path)) {
    $path = "editPlaylist";
}

// Routeur avec un switch
switch ($path) {
    case '':
    case 'home':
        include __DIR__ . '/views/home.php';
        break;
    case 'collections':
        include __DIR__ . '/views/collections.php';
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
    case 'addAlbum':
        include __DIR__ . '/views/addAlbums.php';
        break;
    case 'album':
        include __DIR__ . '/views/album.php';
        break;
    case 'artist':
        include __DIR__ . '/views/artist.php';
        break;
    case 'playlist':
        include __DIR__ . '/views/playlist.php';
        break;
    case 'createPlaylist':
        include __DIR__ . '/views/createPlaylists.php';
        break;
    case 'editPlaylist':
        include __DIR__ . '/views/editPlaylist.php';
        break;


    default: // Page non trouvée
        include __DIR__ . '/views/404.php';
        break;
}
