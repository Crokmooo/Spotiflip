<?php
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Vérification de l'ID de l'artiste dans l'URL
if (!preg_match('#^/artist/([a-zA-Z0-9]+)$#', $urlPath, $matches)) {
    header('Location: /404');
    exit;
}

$artistId = $matches[1];

// Si l'ID est absent, redirection vers la page 404
if (!$artistId) {
    header('Location: /404');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title id="pageTitle">Artiste - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-8">
    <!-- Nom de l'artiste -->
    <h2 id="artistName" class="text-3xl font-bold text-gray-700 mb-6 text-center"></h2>

    <!-- Informations sur l'artiste -->
    <div class="flex flex-col items-center mb-8">
        <!-- Image de l'artiste avec bouton Edit -->
        <div id="artistImageContainer" class="relative group w-64 h-64">
            <img id="artistImage" src="" alt="Image de l'artiste"
                 class="w-full h-full object-cover rounded-lg shadow-lg bg-gray-100">
            <button id="editImageButton"
                    class="absolute top-2 right-2 bg-synthwave-dark text-white text-sm px-3 py-1 rounded-full shadow hover:bg-synthwave-light focus:outline-none">
                Modifier
            </button>
        </div>

        <!-- Zone pour modifier l'image -->
        <div id="editImageZone" class="hidden mt-4 w-full">
            <input type="text" id="newImageUrl" placeholder="Lien vers l'image"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
            <button id="saveImageButton"
                    class="mt-2 bg-synthwave-dark text-white px-4 py-2 rounded-lg hover:bg-synthwave-light focus:outline-none">
                Enregistrer
            </button>
        </div>

        <!-- Genres de l'artiste -->
        <div id="artistGenres" class="text-center mt-4">
            <!-- Les genres seront insérés ici dynamiquement -->
        </div>

        <!-- Nombre d'écoutes -->
        <div id="artistListens" class="text-center text-gray-600 mt-2">
            <!-- Nombre d'écoutes sera inséré ici -->
        </div>
    </div>

    <!-- Liste des albums -->
    <h3 class="text-2xl font-semibold text-gray-700 mb-4">Albums</h3>
    <div class="swiper mySwiper">
        <div class="swiper-wrapper" id="artistAlbumContainer">
            <!-- Les albums seront insérés ici dynamiquement -->
        </div>
    </div>
</main>

<footer class="bg-white mt-12 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="/components/albumComponent.js"></script>
<script>
    const swiper = new Swiper('.mySwiper', {
        slidesPerView: 2,
        spaceBetween: 24,
        breakpoints: {
            640: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 5 },
        },
        navigation: false,
        pagination: false,
    });

    const artistId = "<?php echo $artistId; ?>";

    async function loadArtistPage() {
        try {
            // Charger les informations de l'artiste
            const artistResponse = await fetch(`http://localhost:3000/api/artist/${artistId}`);
            const artist = await artistResponse.json();

            if (!artistResponse.ok) throw new Error('Impossible de charger les informations de l’artiste.');

            // Afficher le nom de l'artiste
            document.getElementById('artistName').textContent = artist.name;

            // Afficher l'image de l'artiste
            const artistImage = document.getElementById('artistImage');
            artistImage.src = artist.picture || 'https://via.placeholder.com/256';
            artistImage.alt = artist.name;

            // Afficher les genres
            const genresContainer = document.getElementById('artistGenres');
            if (artist.genres.length > 0) {
                genresContainer.innerHTML = `Genres : <span class="text-synthwave-dark font-semibold">${artist.genres.join(', ')}</span>`;
            } else {
                genresContainer.textContent = 'Genres : Non spécifiés';
            }

            // Afficher le nombre d'écoutes
            const listensContainer = document.getElementById('artistListens');
            listensContainer.textContent = `Nombre d'écoutes : ${artist.listens || 0}`;

            // Charger les albums de l'artiste
            const albums = artist.albums;
            const container = document.getElementById('artistAlbumContainer');
            if (albums && albums.length > 0) {
                albums.forEach(album => {
                    const slide = createAlbumElement(album, false, ''); // Utilisation du composant album
                    container.appendChild(slide);
                });
                swiper.update();
            } else {
                container.innerHTML = '<p class="text-gray-600">Cet artiste n\'a pas encore d\'albums enregistrés.</p>';
            }

            // Gestion du bouton Modifier
            const editButton = document.getElementById('editImageButton');
            const editZone = document.getElementById('editImageZone');
            const newImageUrl = document.getElementById('newImageUrl');
            const saveButton = document.getElementById('saveImageButton');

            editButton.addEventListener('click', () => {
                editZone.classList.toggle('hidden');
            });

            saveButton.addEventListener('click', async () => {
                const updatedUrl = newImageUrl.value.trim();
                if (updatedUrl) {
                    const response = await fetch(`http://localhost:3000/api/artist/${artistId}/update-image`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ image_url: updatedUrl }),
                    });

                    if (response.ok) {
                        artistImage.src = updatedUrl;
                        editZone.classList.add('hidden');
                        alert('Image mise à jour avec succès.');
                    } else {
                        alert('Erreur lors de la mise à jour de l’image.');
                    }
                }
            });

        } catch (error) {
            console.error('Erreur :', error);
            alert('Une erreur s\'est produite lors du chargement de la page.');
        }
    }

    document.addEventListener('DOMContentLoaded', loadArtistPage);
</script>

</body>
</html>
