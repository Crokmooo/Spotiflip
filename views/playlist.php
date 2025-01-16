<?php
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('#^/playlist/([a-zA-Z0-9]+)$#', $urlPath, $matches)) {
    header('Location: /404');
    exit;
}

$playlistId = $matches[1];

if (!$playlistId) {
    header('Location: /404');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title id="pageTitle">Détails de la Playlist - Spotiflip</title>
</head>
<style>
    .hover\:underline::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 1px;
        background: linear-gradient(to right, #06b6d4, #a855f7, #ec4899);
        bottom: -2px;
        left: 0;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease-in-out;
    }
    .hover\:underline:hover::after {
        transform: scaleX(1);
    }
</style>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-4 md:p-8">
    <div id="playlistDetails" class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <!-- Contenu dynamique inséré ici -->
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script src="/components/albumComponent.js"></script>
<script>
    const playlistId = '<?php echo $playlistId; ?>';
    const token = "<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>";

    async function loadPlaylistDetails() {
        if (!playlistId) {
            document.getElementById('playlistDetails').innerHTML = "<p class='text-center text-red-500'>ID de la playlist non fourni.</p>";
            return;
        }

        try {
            const userPlaylistsResponse = token && await fetch('http://localhost:3000/api/user/playlists', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            const userPlaylists = token && await userPlaylistsResponse.json();
            const userPlaylistIds = token && userPlaylists.map(playlist => playlist._id);
            const favouritesResponse = token && await fetch('http://localhost:3000/api/favourites/playlists', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            const favouritePlaylist = token && await favouritesResponse.json();
            let favouritePlaylistIds = [];
            if(token) {
                favouritePlaylist.forEach(playlist => {
                    favouritePlaylistIds.push(playlist._id);
                });
            }

            const response = await fetch(`http://localhost:3000/api/playlists/${playlistId}`);
            if (!response.ok) {
                document.getElementById('playlistDetails').innerHTML = "<p class='text-center text-red-500'>Erreur lors du chargement des détails de la playlist.</p>";
                return;
            }

            const playlist = await response.json();
            if (playlist.visibility.toString() === 'false' && token !== 'Bearer ' + playlist.creator.session_token) {
                window.location.href = '/connect';
            }
            const container = document.getElementById('playlistDetails');
            document.getElementById('pageTitle').innerHTML = `${playlist.name} - Spotiflip`;
            const isFavourite = favouritePlaylistIds.includes(playlistId);
            const isOwner = userPlaylistIds.includes(playlistId);

            container.innerHTML = `
            <div class="relative mb-6">
                 <button onclick="togglePlaylistFavourite('${playlistId}', '${token || ''}')"
                        class="absolute top-2 right-2 ${isFavourite ? 'text-red-500' : 'text-gray-500'} hover:text-red-500 focus:outline-none">
                        <i class="bi bi-${isFavourite ? 'heart-fill' : 'heart'} text-2xl" id="playlist-heart-${playlistId}"></i>
                </button>
            ${isOwner ? `
                <button
                    onclick="window.location.href='/playlist/${playlistId}/edit'"
                    class="absolute top-2 right-12 text-orange-400 hover:text-red-500 focus:outline-none transition-all ease-in-out">
                    <i class="bi bi-pen text-2xl" id="pen-${playlistId}"></i>
                </button>` : ''}

                <div class="flex items-center space-x-4">
                    <img src="${playlist.cover_image || 'https://via.placeholder.com/150'}" alt="${playlist.name}"
                         class="w-48 h-48 object-cover rounded-lg">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">${playlist.name}</h1>
                        <p class="text-gray-500 mt-2">${playlist.description || 'Aucune description disponible.'}</p>
                        <p class="text-md text-gray-600 mt-4">
                            Créée par
                            <span class="relative cursor-pointer font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-500 to-pink-500 animate-gradient-move hover:underline"
                                  onclick="window.location = '/user/${playlist.creator}'">
                                ${playlist.creator.username || 'Utilisateur inconnu'}
                            </span>
                        </p>
                        <p class="text-gray-500">Visibilité : ${playlist.visibility ? 'Publique' : 'Privée'}</p>
                        <p class="text-gray-500">Likes : ${playlist.likes || 0}</p>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Pistes</h2>
                <ul class="space-y-2">
                    ${playlist.tracks && playlist.tracks.length > 0
                ? playlist.tracks.map((track, index) => `
                            <li class="flex items-center space-x-4">
                                <img src="${track.album_id?.cover_image || 'https://via.placeholder.com/50'}"
                                     alt="Album Cover" class="w-12 h-12 object-cover rounded">
                                <span class="text-gray-500">${index + 1}.</span>
                                <span class="text-gray-800 font-medium">${track.title}</span>
                                <a href="${track.audio_url}" target="_blank" class="text-synthwave-dark hover:underline">Écouter</a>
                            </li>
                        `).join('')
                : '<p class="text-gray-600">Aucune piste dans cette playlist.</p>'
            }
                </ul>
            </div>
        `;
        } catch (error) {
            console.error('Erreur lors du chargement des détails de la playlist :', error);
            document.getElementById('playlistDetails').innerHTML = "<p class='text-center text-red-500'>Une erreur s'est produite.</p>";
        }
    }

    document.addEventListener('DOMContentLoaded', loadPlaylistDetails);
</script>

</body>
</html>
