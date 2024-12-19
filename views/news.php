<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <title>Nouveautés - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-4 md:p-8">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Découvrez nos albums</h2>

    <!-- Conteneur Swiper -->
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <?php
            for ($i = 0; $i < 10; $i++) {
                echo('
            <div class="swiper-slide">
                <div class="relative group overflow-hidden rounded-lg shadow-lg w-64 h-64 mx-auto">
                    <img src="public/images/stupeflip.jpg" alt="Album 1" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-white/70 to-transparent group-hover:translate-y-full transition-transform duration-500">
                        <div class="absolute bottom-0 p-4 text-center">
                            <h3 class="text-sm font-bold text-gray-800">Album ' . $i . '</h3>
                            <p class="text-ss text-gray-600">Synthwave Star</p>
                        </div>
                    </div>
                </div>
            </div>
            ');
            } ?>
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
        slidesPerView: 2, // Nombre d'albums visibles
        spaceBetween: 24, // Même gap que le `gap-6` de Tailwind
        breakpoints: {
            640: {slidesPerView: 2}, // 2 albums sur petits écrans
            768: {slidesPerView: 3}, // 3 albums sur écrans moyens
            1024: {slidesPerView: 5}, // 5 albums sur grands écrans
        },
        // Désactivation de la pagination et des flèches
        navigation: false,
        pagination: false,
    });
</script>
</body>
</html>
