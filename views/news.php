<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <title>Albums Récents - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once "components/navbar.php"?>

<main class="p-8">
    <h2 class="text-2xl font-bold text-gray-700">Albums Récents</h2>
    <p class="text-xl mt-4 text-gray-600 mb-7">Découvrez les albums les plus récents ajoutés à Spotiflip !</p>

    <div class="swiper mySwiper">
        <div class="swiper-wrapper" id="recentAlbumContainer">
            <!-- Albums dynamiques insérés ici -->
        </div>
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="../components/albumComponent.js"></script>
<script>
    const swiper = new Swiper('.mySwiper', {
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

    async function loadRecentAlbums() {
        try {
            const token = "<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>";

            // Récupérer les albums récents
            const albumsResponse = await fetch('http://localhost:3000/api/albums/recent');
            const albums = await albumsResponse.json();

            // Récupérer les albums favoris si un token est présent
            let favouriteAlbums = [];
            if (token) {
                const favouritesResponse = await fetch('http://localhost:3000/api/favourites', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                favouriteAlbums = await favouritesResponse.json();
            }

            const container = document.getElementById('recentAlbumContainer');
            albums.forEach(album => {
                const isFavourite = token && favouriteAlbums.includes(album._id);
                const albumElement = createAlbumElement(album, isFavourite, token);
                container.appendChild(albumElement);
            });

            swiper.update();
        } catch (error) {
            console.error('Erreur lors du chargement des albums récents :', error);
        }
    }

    document.addEventListener('DOMContentLoaded', loadRecentAlbums);
</script>
</body>
</html>
