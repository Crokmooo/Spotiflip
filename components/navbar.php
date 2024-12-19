<header class="bg-white shadow-md py-1.5 px-6 flex items-center justify-between">
    <!-- Logo et titre -->
    <div class="flex items-center space-x-3">
        <img src="public/images/img.png" alt="Logo" class="w-14 h-14 cursor-pointer" onclick="window.location = '/'">
        <h1 class="text-xl cursor-pointer font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-500 to-pink-500 animate-gradient-move"
            onclick="window.location = '/'">
            Spotiflip</h1>
        <div class="w-px h-6 bg-gray-300 mx-4"></div>
        <nav>
            <ul class="flex space-x-8">
                <li><a href="/" class="text-gray-700 hover:text-synthwave-dark font-medium">Accueil</a></li>
                <li><a href="/news" class="text-gray-700 hover:text-synthwave-dark font-medium">Nouveautés</a></li>
                <li><a href="/collections" class="text-gray-700 hover:text-synthwave-dark font-medium">Mes
                        Collections</a></li>
            </ul>
        </nav>
    </div>

    <!-- Icônes alignées à droite -->
    <div class="flex items-center space-x-4">
        <!-- Recherche -->
        <div class="relative">
            <!-- Icône de recherche -->
            <div id="iconSearchGroup" class="flex items-center space-x-2">
                <!-- Search Icon -->
                <i id="searchIcon" onclick="toggleSearchBar()"
                   class="bi bi-search text-gray-700 hover:text-synthwave-dark cursor-pointer text-2xl active transition-all duration-300 ease-in-out"></i>
                <!-- Search Input -->
                <input id="searchInput" type="search" placeholder="Votre musique" aria-label="Votre musique"
                       style="color: #6b7280 !important;"
                       class="hidden transition-all duration-300 ease-in-out rounded-lg bg-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-synthwave-dark h-8 px-2"/>
                <!-- Cross Icon -->
                <i id="crossIcon" onclick="toggleSearchBar()"
                   class="bi bi-x-lg text-gray-700 hover:text-synthwave-dark cursor-pointer text-2xl hidden"></i>
            </div>
        </div>
        <!-- Profil -->
        <i class="bi bi-person-circle text-2xl text-gray-700 hover:text-synthwave-dark cursor-pointer"></i>
    </div>
</header>

<style>
    .active {
        display: block;
    }

    .hidden {
        display: none;
    }

    #crossIcon.active {
        display: inline-block;
    }

    #searchInput.hidden {
        width: 0;
        opacity: 0;
    }

    #searchInput.active {
        width: 200px;
    }
</style>
<script>
    function toggleSearchBar() {
        const searchBar = document.getElementById('searchIcon');
        const crossBar = document.getElementById('crossIcon');
        const searchInput = document.getElementById('searchInput');
        if (searchBar.classList.contains('active')) {
            // Masquer la barre de recherche
            searchBar.classList.remove('active');
            searchBar.classList.add('hidden');
            crossBar.classList.add('active');
            crossBar.classList.remove('hidden');
            searchInput.classList.add('active');
            searchInput.classList.remove('hidden');
        } else {
            // Afficher la barre de recherche
            searchBar.classList.remove('hidden');
            searchBar.classList.add('active');
            crossBar.classList.add('hidden');
            crossBar.classList.remove('active');
            searchInput.classList.add('hidden');
            searchInput.classList.remove('active');
        }
    }
</script>