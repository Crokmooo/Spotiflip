<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title>Connexion / Inscription - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-4 md:p-8">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">Bienvenue sur Spotiflip</h2>

        <div class="flex justify-center space-x-4 mb-6">
            <!-- Bouton Se connecter -->
            <button onclick="showForm('loginForm')"
                    class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-4 rounded-full hover:shadow-synthwave transition-all">
                Se connecter
            </button>
            <!-- Bouton Créer un compte -->
            <button onclick="showForm('registerForm')"
                    class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-4 rounded-full hover:shadow-synthwave transition-all">
                Créer un compte
            </button>
        </div>

        <!-- Formulaire de connexion -->
        <form id="loginForm" method="POST" onsubmit="handleFormSubmit(event, 'login')">
            <h3 class="text-lg font-medium text-gray-700">Connexion</h3>
            <input type="email" name="email" placeholder="Email"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
            <input type="password" name="password" placeholder="Mot de passe"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
            <button type="submit"
                    class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-4 rounded-full hover:shadow-synthwave transition-all mx-auto block">
                Se connecter
            </button>

        </form>

        <form id="registerForm" method="POST" onsubmit="handleFormSubmit(event, 'register')">
            <h3 class="text-lg font-medium text-gray-700">Créer un compte</h3>
            <input type="text" name="username" placeholder="Nom d'utilisateur"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
            <input type="email" name="email" placeholder="Email"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
            <input type="password" name="password" placeholder="Mot de passe"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
            <button type="submit"
                    class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-4 rounded-full hover:shadow-synthwave transition-all mx-auto block">
                Créer un compte
            </button>
        </form>

    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script src="managers/connectUpdater.js"></script>
<script>
    function showFormFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const form = urlParams.get('form');
        if (form === 'register') {
            showForm('registerForm');
        } else {
            showForm('loginForm');
        }
    }

    showFormFromURL();

    function showForm(formId) {
        document.getElementById('loginForm').style.display = formId === 'loginForm' ? 'block' : 'none';
        document.getElementById('registerForm').style.display = formId === 'registerForm' ? 'block' : 'none';
    }
</script>
</body>
</html>
