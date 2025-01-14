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

    async function loadLikedAlbums() {
        try {
            const token = "<?php echo isset($_SESSION['token']) ? $_SESSION['token'] : ''; ?>";
            const messageContainer = document.getElementById('messageContainer');
            const container = document.getElementById('albumContainer');
            const swiperContainer = document.querySelector('.swiper');

            if (!token) {
                messageContainer.textContent = "Vous devez être connecté pour voir vos favoris.";
                messageContainer.classList.remove('hidden');
                return;
            }

            // Récupération des favoris
            const favouritesResponse = await fetch('http://localhost:3000/api/favourites', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!favouritesResponse.ok) {
                messageContainer.textContent = "Erreur lors du chargement de vos favoris.";
                messageContainer.classList.remove('hidden');
                return;
            }

            const albumIds = await favouritesResponse.json();
            if (albumIds.length === 0) {
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
                body: JSON.stringify({ albumIds }),
            });

            if (!detailsResponse.ok) {
                messageContainer.textContent = "Erreur lors du chargement des détails des albums.";
                messageContainer.classList.remove('hidden');
                return;
            }

            const albums = await detailsResponse.json();
            swiperContainer.classList.remove('hidden');
            messageContainer.classList.add('hidden');

            albums.forEach(album => {
                const albumElement = createAlbumElement(album, true, token);
                container.appendChild(albumElement);
            });

            swiper.update();
        } catch (error) {
            console.error('Erreur lors du chargement des albums favoris :', error);
            const messageContainer = document.getElementById('messageContainer');
            messageContainer.textContent = "Une erreur s'est produite.";
            messageContainer.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', loadLikedAlbums);
</script>
</body>
</html>
