<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <title>Mes Collections - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'?>

<main class="p-4 md:p-8">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Appréciez vos albums favoris</h2>

    <div id="messageContainer" class="text-center text-gray-500 text-lg mb-6 hidden">
        <!-- Message vide ajouté ici dynamiquement -->
    </div>

    <div class="swiper mySwiper hidden">
        <div class="swiper-wrapper" id="albumContainer">
            <!-- Albums dynamiques insérés ici -->
        </div>
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 MusicWave - Tous droits réservés</p>
</footer>

<!-- Swiper JS -->
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

    async function loadLikedAlbums() {
        try {
            const token = "<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>";
            if (!token) {
                document.getElementById('messageContainer').textContent = "Vous devez être connecté pour voir vos favoris.";
                document.getElementById('messageContainer').classList.remove('hidden');
                return;
            }

            // Récupération des IDs des albums favoris
            const favouritesResponse = await fetch('http://localhost:3000/api/favourites', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!favouritesResponse.ok) {
                document.getElementById('messageContainer').textContent = "Erreur lors du chargement de vos favoris.";
                document.getElementById('messageContainer').classList.remove('hidden');
                return;
            }

            const albumIds = await favouritesResponse.json();

            if (albumIds.length === 0) {
                const messageContainer = document.getElementById('messageContainer');
                messageContainer.textContent = "Aucun album dans vos favoris pour le moment.";
                messageContainer.classList.remove('hidden');
                return;
            }

            // Récupération des détails des albums
            const detailsResponse = await fetch('http://localhost:3000/api/album-details', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ albumIds })
            });

            if (!detailsResponse.ok) {
                document.getElementById('messageContainer').textContent = "Erreur lors du chargement des détails des albums.";
                document.getElementById('messageContainer').classList.remove('hidden');
                return;
            }

            const albums = await detailsResponse.json();
            const container = document.getElementById('albumContainer');
            const swiperContainer = document.querySelector('.swiper');
            const messageContainer = document.getElementById('messageContainer');

            messageContainer.classList.add('hidden');
            swiperContainer.classList.remove('hidden');

            albums.forEach(album => {
                const isFavourite = albumIds.includes(album._id); // Vérification si l'album est favori
                const slide = document.createElement('div');
                slide.classList.add('swiper-slide');
                slide.innerHTML = `
                <div class="relative group overflow-hidden rounded-lg shadow-lg w-64 h-64 mx-auto">
                    <!-- Icône de cœur -->
                    <button onclick="toggleFavourite('${album._id}', '${token}')"
                            class="absolute top-2 right-2 ${isFavourite ? 'text-red-500' : 'text-gray-500'} hover:text-red-500 focus:outline-none">
                        <i class="bi bi-${isFavourite ? 'heart-fill' : 'heart'} text-3xl" id="heart-${album._id}"></i>
                    </button>
                    <img src="${album.cover_image}" alt="${album.title}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-white/70 to-transparent group-hover:translate-y-full transition-transform duration-500">
                        <div class="absolute bottom-0 p-4 text-left">
                            <h3 class="text-mm font-bold text-gray-800 mb-1">${album.title}</h3>
                            <p class="text-ss text-gray-600">${album.artist_id.name}</p>
                        </div>
                    </div>
                </div>
            `;
                container.appendChild(slide);
            });

            swiper.update();
        } catch (error) {
            console.error('Erreur lors du chargement des albums favoris :', error);
            const messageContainer = document.getElementById('messageContainer');
            messageContainer.textContent = "Une erreur s'est produite.";
            messageContainer.classList.remove('hidden');
        }
    }

    async function toggleFavourite(albumId, token) {
        try {
            if (!token) {
                alert("Vous devez être connecté pour ajouter des albums aux favoris.");
                return;
            }

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

    document.addEventListener('DOMContentLoaded', loadLikedAlbums);
</script>
</body>
</html>
