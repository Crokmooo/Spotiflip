<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php' ?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <title>Mes Collections - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php' ?>

<main class="p-4 md:p-8">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Appréciez vos albums favoris ❤️</h2>

    <div id="albumMessageContainer" class="text-center text-gray-500 text-lg mb-6 hidden">
        <!-- Message vide ajouté ici dynamiquement -->
    </div>

    <div class="swiper mySwiper albumsSwiper hidden">
        <div class="swiper-wrapper" id="albumContainer">
            <!-- Albums dynamiques insérés ici -->
        </div>
    </div>

    <h2 class="text-2xl font-bold text-gray-700 mb-6 mt-16">Retrouvez vos playlists ✨</h2>

    <div id="playlistMessageContainer" class="text-center text-gray-500 text-lg mb-6 hidden">
        <!-- Message vide ajouté ici dynamiquement -->
    </div>

    <div class="swiper mySwiper playlistsSwiper hidden">
        <div class="swiper-wrapper" id="playlistContainer">
            <!-- Playlists dynamiques insérées ici -->
        </div>
    </div>

</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="../components/albumComponent.js"></script>
<script>
    const albumSwiper = new Swiper('.albumsSwiper', {
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

    const playlistSwiper = new Swiper('.playlistsSwiper', {
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

    async function loadLikedAlbums() {
        try {
            const token = "<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>";
            const albumMessageContainer = document.getElementById('albumMessageContainer');
            const albumContainer = document.getElementById('albumContainer');
            const albumSwiperContainer = document.querySelector('.albumsSwiper');

            if (!token) {
                albumMessageContainer.textContent = "Vous devez être connecté pour voir vos favoris.";
                albumMessageContainer.classList.remove('hidden');
                return;
            }

            const response = await fetch('http://localhost:3000/api/favourites', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) {
                albumMessageContainer.textContent = "Erreur lors du chargement de vos favoris.";
                albumMessageContainer.classList.remove('hidden');
                return;
            }

            const albumIds = await response.json();
            if (albumIds.length === 0) {
                albumMessageContainer.textContent = "Aucun album dans vos favoris pour le moment.";
                albumMessageContainer.classList.remove('hidden');
                return;
            }

            const detailsResponse = await fetch('http://localhost:3000/api/album-details', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ albumIds }),
            });

            if (!detailsResponse.ok) {
                albumMessageContainer.textContent = "Erreur lors du chargement des détails des albums.";
                albumMessageContainer.classList.remove('hidden');
                return;
            }

            const albums = await detailsResponse.json();
            albumSwiperContainer.classList.remove('hidden');
            albumMessageContainer.classList.add('hidden');

            albums.forEach(album => {
                const albumElement = createAlbumElement(album, true, token);
                albumContainer.appendChild(albumElement);
            });

            albumSwiper.update();
        } catch (error) {
            console.error('Erreur lors du chargement des albums favoris :', error);
            const albumMessageContainer = document.getElementById('albumMessageContainer');
            albumMessageContainer.textContent = "Une erreur s'est produite.";
            albumMessageContainer.classList.remove('hidden');
        }
    }

    async function loadUserPlaylists() {
        try {
            const token = "<?php echo $_SESSION['token']; ?>";
            const playlistMessageContainer = document.getElementById('playlistMessageContainer');
            const playlistContainer = document.getElementById('playlistContainer');
            const playlistSwiperContainer = document.querySelector('.playlistsSwiper');

            if (!token) {
                playlistMessageContainer.textContent = "Vous devez être connecté pour voir vos playlists.";
                playlistMessageContainer.classList.remove('hidden');
                return;
            }

            // Requête pour récupérer les playlists créées par l'utilisateur
            const userPlaylistsResponse = await fetch('http://localhost:3000/api/user/playlists', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!userPlaylistsResponse.ok) {
                playlistMessageContainer.textContent = "Erreur lors du chargement de vos playlists.";
                playlistMessageContainer.classList.remove('hidden');
                return;
            }

            const userPlaylists = await userPlaylistsResponse.json();
            const userPlaylistIds = userPlaylists.map(playlist => playlist._id); // IDs des playlists créées

            // Requête pour récupérer toutes les playlists favorites
            const favouritesResponse = await fetch('http://localhost:3000/api/favourites/playlists', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!favouritesResponse.ok) {
                playlistMessageContainer.textContent = "Erreur lors du chargement des favoris.";
                playlistMessageContainer.classList.remove('hidden');
                return;
            }

            const favouritePlaylist = await favouritesResponse.json();
            let favouritePlaylistIds = [];
            favouritePlaylist.forEach(playlist => {
                favouritePlaylistIds.push(playlist._id);
            });

            // Requête pour récupérer toutes les playlists à afficher
            const allPlaylistsResponse = await fetch('http://localhost:3000/api/playlists', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!allPlaylistsResponse.ok) {
                playlistMessageContainer.textContent = "Erreur lors du chargement des playlists.";
                playlistMessageContainer.classList.remove('hidden');
                return;
            }

            const allPlaylists = await allPlaylistsResponse.json();
            if (allPlaylists.length === 0) {
                playlistMessageContainer.textContent = "Aucune playlist disponible.";
                playlistMessageContainer.classList.remove('hidden');
                return;
            }

            playlistSwiperContainer.classList.remove('hidden');
            playlistMessageContainer.classList.add('hidden');


            allPlaylists.playlists.forEach(playlist => {
                const isFavourite = favouritePlaylistIds.includes(playlist._id);
                const isOwner = userPlaylistIds.includes(playlist._id);
                const playlistElement = createPlaylistElement(playlist, isFavourite, isOwner, token);
                playlistContainer.appendChild(playlistElement);
            });

            playlistSwiper.update();
        } catch (error) {
            console.error('Erreur lors du chargement des playlists :', error);
            const playlistMessageContainer = document.getElementById('playlistMessageContainer');
            playlistMessageContainer.textContent = "Une erreur s'est produite.";
            playlistMessageContainer.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadLikedAlbums();
        loadUserPlaylists();
    });
</script>
</body>
</html>
