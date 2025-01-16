<!-- Navbar -->
<header class="bg-white shadow-md py-1.5 px-6 flex items-center justify-between">
    <!-- Logo et titre -->
    <div class="flex items-center space-x-3">
        <img src="/public/images/logo.png" alt="Logo" class="w-14 h-14 cursor-pointer" onclick="window.location = '/'">
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
            <div id="iconSearchGroup" class="flex items-center space-x-2 relative">
                <!-- Search Icon -->
                <i id="searchIcon" onclick="toggleSearchBar()"
                   class="bi bi-search text-gray-700 hover:text-synthwave-dark cursor-pointer text-2xl active transition-all duration-300 ease-in-out"></i>
                <!-- Search Input -->
                <input id="searchInput" type="search" placeholder="Votre musique" aria-label="Votre musique"
                       style="color: #6b7280 !important;"
                       class="hidden transition-all duration-300 ease-in-out rounded-lg bg-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-synthwave-dark h-8 px-2"
                       oninput="searchTracks(this.value)" />
                <!-- Cross Icon -->
                <i id="crossIcon" onclick="toggleSearchBar()"
                   class="bi bi-x-lg text-gray-700 hover:text-synthwave-dark cursor-pointer text-2xl hidden"></i>
            </div>
            <!-- Liste dynamique des résultats -->
            <ul id="searchResults" class="absolute bg-white shadow-md rounded-lg mt-2 w-full hidden z-50">
                <!-- Résultats insérés dynamiquement ici -->
            </ul>
        </div>
        <?php if (isset($_SESSION['token'])) { ?>
            <div class="relative" onmouseenter="showMenu()" onmouseleave="hideMenu()">
                <i class="bi bi-person-circle text-2xl text-gray-700 hover:text-synthwave-dark cursor-pointer"></i>
                <!-- Menu déroulant -->
                <ul id="profileMenu"
                    class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg hidden transition-opacity duration-300">
                    <li class="px-4 py-2 hover:bg-gray-100">
                        <a href="/createPlaylist">Créer une playlist</a>
                    </li>
                    <li class="px-4 py-2 hover:bg-gray-100">
                        <a href="/addAlbum">Ajouter un album</a>
                    </li>
                    <li class="px-4 py-2 hover:bg-gray-100">
                        <a href="/logout">Déconnexion</a>
                    </li>
                </ul>
            </div>
        <?php } else { ?>
            <div class="relative">
                <a href="/connect"
                   class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-2 rounded-full hover:shadow-synthwave transition-all" style="margin-right: 10px;">
                    Se connecter
                </a>
                <a href="/connect?form=register"
                   class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-2 rounded-full hover:shadow-synthwave transition-all">
                    Créer un compte
                </a>
            </div>
        <?php }?>
    </div>
</header>

<script>
    async function searchTracks(query) {
        const resultsContainer = document.getElementById('searchResults');
        resultsContainer.innerHTML = '';

        if (query.trim() === '') {
            resultsContainer.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`http://localhost:3000/api/searchTracks?search=${query}`);
            if (!response.ok) {
                console.error("Erreur lors de la recherche des tracks.");
                return;
            }

            const tracks = await response.json();
            if (tracks.length === 0) {
                resultsContainer.innerHTML = '<li class="px-4 py-2 text-gray-500">Aucun résultat trouvé</li>';
                resultsContainer.classList.remove('hidden');
                return;
            }

            tracks.forEach(track => {
                const trackElement = document.createElement('li');
                trackElement.classList.add('px-4', 'py-2', 'flex', 'items-center', 'hover:bg-gray-100', 'cursor-pointer');

                const trackImage = document.createElement('img');
                trackImage.src = track.album_id?.cover_image || '';
                trackImage.alt = `${track.title} Cover`;
                trackImage.classList.add('w-10', 'h-10', 'object-cover', 'rounded', 'mr-3');

                const trackTitle = document.createElement('span');
                trackTitle.textContent = track.title;
                trackTitle.style.flexGrow = '1';

                trackElement.appendChild(trackImage);
                trackElement.appendChild(trackTitle);

                trackElement.onclick = () => window.location.href = `/album/${track.album_id?._id || ''}`;

                resultsContainer.appendChild(trackElement);
            });

            resultsContainer.classList.remove('hidden');
        } catch (error) {
            console.error("Erreur lors du chargement des résultats de recherche :", error);
        }
    }

    let menuTimeout;

    function showMenu() {
        clearTimeout(menuTimeout);
        const menu = document.getElementById('profileMenu');
        menu.classList.remove('hidden');
        menu.classList.add('opacity-100');
    }

    function hideMenu() {
        const menu = document.getElementById('profileMenu');
        menuTimeout = setTimeout(() => {
            menu.classList.add('hidden');
            menu.classList.remove('opacity-100');
        }, 100);
    }


    function toggleSearchBar() {
        const searchBar = document.getElementById('searchIcon');
        const crossBar = document.getElementById('crossIcon');
        const searchInput = document.getElementById('searchInput');
        const resultsContainer = document.getElementById('searchResults');

        if (searchBar.classList.contains('active')) {
            searchBar.classList.remove('active');
            searchBar.classList.add('hidden');
            crossBar.classList.add('active');
            crossBar.classList.remove('hidden');
            searchInput.classList.add('active');
            searchInput.classList.remove('hidden');
        } else {
            searchBar.classList.remove('hidden');
            searchBar.classList.add('active');
            crossBar.classList.add('hidden');
            crossBar.classList.remove('active');
            searchInput.classList.add('hidden');
            searchInput.classList.remove('active');
            resultsContainer.classList.add('hidden');
        }
    }
</script>
