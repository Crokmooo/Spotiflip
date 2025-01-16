<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <title>Accueil - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">
<?php require_once "components/navbar.php"?>

<main class="p-8">
    <h2 class="text-2xl font-bold text-gray-700">Bienvenue sur Spotiflip</h2>
    <p class="text-xl mt-4 text-gray-600 mb-7">Découvrez les meilleurs albums de Spotiflip !</p>

    <!-- Albums -->
    <div class="swiper albumSwiper">
        <div class="swiper-wrapper" id="albumContainer">
            <!-- Albums dynamiques insérés ici -->
        </div>
    </div>

    <p class="text-xl mt-4 text-gray-600 mb-7 mt-10">Découvrez les playlists partagées par d'autres utilisateurs.</p>

    <!-- Playlists -->
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
    const albumSwiper = new Swiper('.albumSwiper', {
        slidesPerView: 2,
        spaceBetween: 24,
        breakpoints: {
            640: {slidesPerView: 2},
            768: {slidesPerView: 3},
            1024: {slidesPerView: 5},
        },
        navigation: false,
        pagination: false,
    });

    const playlistSwiper = new Swiper('.playlistsSwiper', {
        slidesPerView: 2,
        spaceBetween: 24,
        breakpoints: {
            640: {slidesPerView: 2},
            768: {slidesPerView: 3},
            1024: {slidesPerView: 5},
        },
        navigation: false,
        pagination: false,
    });
    const token = "<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>";

    async function loadAlbums() {
        try {
            // Récupération des albums
            const albumsResponse = await fetch('http://localhost:3000/api/albums');
            const albums = await albumsResponse.json();

            // Récupération des favoris si un token est disponible
            let favouriteAlbums = [];
            if (token) {
                const favouritesResponse = await fetch('http://localhost:3000/api/favourites', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                favouriteAlbums = await favouritesResponse.json();
            }

            const albumContainer = document.getElementById('albumContainer');
            albums.forEach(album => {
                const isFavourite = token && favouriteAlbums.includes(album._id);
                const albumElement = createAlbumElement(album, isFavourite, token);
                albumContainer.appendChild(albumElement);
            });

            albumSwiper.update();
        } catch (error) {
            console.error('Erreur lors du chargement des albums :', error);
        }
    }

    async function loadPublicPlaylists() {
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

            const playlistsResponse = await fetch('http://localhost:3000/api/playlists?visibility=1');
            if (!playlistsResponse.ok) {
                console.error("Erreur lors de la récupération des playlists publiques.");
                return;
            }

            const playlists = await playlistsResponse.json();
            const playlistContainer = document.getElementById('playlistContainer');
            const playlistSwiperContainer = document.querySelector('.playlistsSwiper');

            if (playlists.length === 0) {
                console.log("Aucune playlist publique disponible.");
                return;
            }

            playlists.playlists.forEach(playlist => {
                const isFavourite = token && favouritePlaylistIds.includes(playlist._id);
                const isOwner = token && userPlaylistIds.includes(playlist._id);
                const playlistElement = createPlaylistElement(playlist, isFavourite, isOwner);
                playlistContainer.appendChild(playlistElement);
            });

            playlistSwiperContainer.classList.remove('hidden');
            playlistSwiper.update();
        } catch (error) {
            console.error("Erreur lors du chargement des playlists publiques :", error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAlbums();
        loadPublicPlaylists();
    });
</script>
</body>
</html>
