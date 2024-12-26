<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once "components/head.php"?>
    <title>Page non trouvée - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat h-screen flex flex-col">
<?php require_once "components/navbar.php"?>

<main class="flex-grow flex flex-col items-center justify-center text-center">
    <div class="flex items-center justify-center">
        <img src="public/images/logo.png" alt="Logo" class="w-64 h-64">
    </div>

    <h2 class="text-2xl font-bold text-gray-700 mt-4">Page non trouvée</h2>
    <p class="mt-4 text-gray-600">Oups ! La page que vous cherchez n'existe pas.</p>
    <a href="/" class="mt-6 inline-block text-synthwave-dark font-bold">Retour à l'accueil</a>
</main>

<footer class="bg-white py-4 border-t border-gray-300 text-center w-full">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>
</body>
</html>
