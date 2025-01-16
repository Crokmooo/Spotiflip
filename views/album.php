<?php
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('#^/album/([a-zA-Z0-9]+)$#', $urlPath, $matches)) {
    header('Location: /404');
    exit;
}

$albumId = $matches[1];

if (!$albumId) {
    header('Location: /404');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title id="pageTitle"> - Spotiflip</title>
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
    <div id="albumDetails" class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <!-- Contenu dynamique inséré ici -->
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script>
    const albumId = '<?php echo $albumId?>';

    async function loadAlbumDetails() {
        if (!albumId) {
            document.getElementById('albumDetails').innerHTML = "<p class='text-center text-red-500'>ID de l'album non fourni.</p>";
            return;
        }

        try {
            const response = await fetch(`http://localhost:3000/api/albums/${albumId}`);
            if (!response.ok) {
                document.getElementById('albumDetails').innerHTML = "<p class='text-center text-red-500'>Erreur lors du chargement des détails de l'album.</p>";
                return;
            }

            const album = await response.json();
            const container = document.getElementById('albumDetails');
            document.getElementById('pageTitle').innerHTML = `${album.title} - Spotiflip`;
            container.innerHTML = `
                <div class="mb-6">
                    <div class="flex items-center space-x-4">
                        <img src="${album.cover_image}" alt="${album.title}" class="w-48 h-48 object-cover rounded-lg">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">${album.title}</h1>
                            <p class="text-md text-gray-600">
                                Par <span class="relative cursor-pointer font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-500 to-pink-500 animate-gradient-move hover:underline"
                                        onclick="window.location = '/artist/${album.artist_id._id}'">${album.artist_id.name}
                                </span>
                            </p>
                            <p class="text-gray-500">Date de sortie : ${new Date(album.release_date).toLocaleDateString()}</p>
                        </div>

                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-700 mb-4">Pistes</h2>
                    <ul class="space-y-2">
                        ${album.track_list
                .map(
                    (track, index) => `
                                    <li class="flex items-center space-x-2">
                                        <span class="text-gray-500">${index + 1}.</span>
                                        <span class="text-gray-800 font-medium">${track.title}</span>
                                        <a href="${track.audio_url}" target="_blank" class="text-synthwave-dark hover:underline">Écouter</a>
                                    </li>
                                `
                )
                .join('')}
                    </ul>
                </div>
            `;
        } catch (error) {
            console.error('Erreur lors du chargement des détails de l\'album :', error);
            document.getElementById('albumDetails').innerHTML = "<p class='text-center text-red-500'>Une erreur s'est produite.</p>";
        }
    }

    document.addEventListener('DOMContentLoaded', loadAlbumDetails);
</script>

</body>
</html>
