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
                const slide = document.createElement('div');
                slide.classList.add('swiper-slide');
                slide.innerHTML = `
                    <div class="relative group overflow-hidden rounded-lg shadow-lg w-64 h-64 mx-auto">
                        <!-- Icône de cœur -->
                        <button onclick="toggleFavourite('${album._id}', '${token}')"
                                class="absolute top-2 right-2 ${isFavourite ? 'text-red-500' : 'text-gray-500'} hover:text-red-500 focus:outline-none">
                            <i class="bi bi-${isFavourite ? 'heart-fill' : 'heart'} text-2xl" id="heart-${album._id}"></i>
                        </button>
                        <!-- Image de l'album -->
                        <img src="${album.cover_image}" alt="${album.title}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-white/70 to-transparent group-hover:translate-y-full transition-transform duration-500">
                            <div class="absolute bottom-0 p-4 text-left">
                                <h3 class="text-sm font-bold text-gray-800 mb-1">${album.title}</h3>
                                <p class="text-ss text-gray-600">${album.artist_id.name}</p>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(slide);
            });

            swiper.update(); // Mettez à jour Swiper après l'ajout des slides
        } catch (error) {
            console.error('Erreur lors du chargement des albums récents :', error);
        }
    }

    async function toggleFavourite(albumId, token) {
        if (!token) {
            // Redirigez l'utilisateur vers la page de connexion s'il n'est pas connecté
            window.location.href = '/connect';
            return;
        }

        try {
            const response = await fetch('http://localhost:3000/api/favourite-album', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ albumId }),
            });

            const result = await response.json();

            if (response.ok) {
                const heartIcon = document.getElementById(`heart-${albumId}`);
                if (result.message.includes('ajouté')) {
                    heartIcon.classList.add('text-red-500');
                    heartIcon.classList.add('bi-heart-fill');
                    heartIcon.classList.remove('text-gray-500');
                    heartIcon.classList.remove('bi-heart');
                } else {
                    heartIcon.classList.add('text-gray-500');
                    heartIcon.classList.remove('bi-heart-fill');
                    heartIcon.classList.add('bi-heart');
                    heartIcon.classList.remove('text-red-500');
                }
            } else {
                alert(result.error || 'Une erreur est survenue.');
            }
        } catch (error) {
            console.error('Erreur lors de la gestion des favoris :', error);
            alert('Erreur de communication avec le serveur.');
        }
    }

    document.addEventListener('DOMContentLoaded', loadRecentAlbums);
</script>
</body>
</html>

<?php
function getToken() {
    if(isset($_SESSION['token'])) {
        return $_SESSION['token'];
    } else {
        header('Location: /connect?form=register');
    }
}

?>