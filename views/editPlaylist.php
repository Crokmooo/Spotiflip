<?php
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('#^/playlist/([a-zA-Z0-9]+)/edit$#', $urlPath, $matches)) {
    header('Location: /404');
    exit;
}

$playlistId = $matches[1];

if (!$playlistId) {
    header('Location: /404');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title id="pageTitle">Modifier la Playlist - Spotiflip</title>
</head>
<style>
    .preview-container {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 150px;
        height: 150px;
        border: 2px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #f9f9f9;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }
</style>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-4 md:p-8 relative">
    <div id="editPlaylistFormContainer" class="relative max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <div class="absolute top-4 right-4 w-24 h-24 border-2 border-gray-300 rounded-lg overflow-hidden shadow">
            <img id="coverImagePreview" src="https://www.svgrepo.com/show/508699/landscape-placeholder.svg" alt="Aperçu de l'image" class="w-full h-full object-cover">
        </div>

        <h2 class="text-2xl font-bold text-gray-700 mb-6">Modifier la Playlist</h2>
        <form id="editPlaylistForm">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block font-medium text-gray-700">Titre</label>
                    <input type="text" id="name" name="name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark max-w-xl"
                           required>
                </div>

                <div>
                    <label for="description" class="block font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"></textarea>
                </div>

                <div>
                    <label for="visibility" class="block font-medium text-gray-700">Visibilité</label>
                    <select id="visibility" name="visibility"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
                        <option value="true">Publique</option>
                        <option value="false">Privée</option>
                    </select>
                </div>

                <div>
                    <label for="cover_image" class="block font-medium text-gray-700">Image de couverture</label>
                    <input type="text" id="cover_image" name="cover_image"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                           placeholder="URL de l'image"
                           oninput="updateCoverImagePreview()">
                </div>

                <!-- Gestion des pistes -->
                <div>
                    <label class="block font-medium text-gray-700">Pistes</label>
                    <div id="tracksContainer" class="space-y-2">
                        <!-- Champs pour les pistes ajoutées dynamiquement -->
                    </div>
                    <button type="button" onclick="addTrackField()"
                            class="mt-2 text-synthwave-dark border border-synthwave-mid font-medium py-1 px-4 rounded-full hover:shadow-synthwave transition-all">
                        Ajouter une piste
                    </button>
                </div>

                <button type="button" onclick="submitEditPlaylistForm()"
                        class="mt-2 text-synthwave-dark border border-synthwave-mid font-medium py-1 px-4 rounded-full hover:shadow-synthwave transition-all">
                    Sauvegarder
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script>
    const playlistId = '<?php echo $playlistId; ?>';
    let availableTracks = [];
    let usedTrackIds = [];

    function updateCoverImagePreview() {
        const coverImageInput = document.getElementById('cover_image');
        const previewImage = document.getElementById('coverImagePreview');
        previewImage.src = coverImageInput.value || 'https://www.svgrepo.com/show/508699/landscape-placeholder.svg';
    }

    async function loadAvailableTracks() {
        try {
            const response = await fetch('http://localhost:3000/api/tracks');
            if (!response.ok) throw new Error('Erreur lors du chargement des pistes.');

            availableTracks = await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des pistes disponibles :', error);
        }
    }

    function addTrackField() {
        const tracksContainer = document.getElementById('tracksContainer');

        const trackField = document.createElement('div');
        trackField.classList.add('flex', 'items-center', 'space-x-2', 'mb-2');

        trackField.innerHTML = `
        <select name="tracks[]" class="w-full px-3 py-2 border rounded-lg" onchange="updateUsedTrackIds(this)">
            <option value="" disabled selected>-- Sélectionnez une piste --</option>
            ${availableTracks
            .filter(track => !usedTrackIds.includes(track._id))
            .map(track => `<option value="${track._id}">${track.title}</option>`)
            .join('')}
        </select>
        <button type="button" onclick="removeTrackField(this)" class="text-red-500 hover:text-red-700 font-medium">
            Supprimer
        </button>
    `;

        tracksContainer.appendChild(trackField);

        updateDropdownOptions();
    }

    function updateUsedTrackIds(selectElement) {
        const selectedTrackId = selectElement.value;

        usedTrackIds = Array.from(document.querySelectorAll('#tracksContainer select')).map(select => select.value);

        updateDropdownOptions();
    }

    function updateDropdownOptions() {
        const allSelectElements = document.querySelectorAll('#tracksContainer select');

        allSelectElements.forEach(selectElement => {
            const currentValue = selectElement.value;
            const optionsHtml = `
            <option value="" disabled ${!currentValue ? 'selected' : ''}>-- Sélectionnez une piste --</option>
            ${availableTracks
                .filter(track => !usedTrackIds.includes(track._id) || track._id === currentValue)
                .map(track => `<option value="${track._id}">${track.title}</option>`)
                .join('')}
        `;
            selectElement.innerHTML = optionsHtml;
            if (currentValue) {
                selectElement.value = currentValue;
            }
        });
    }

    function removeTrackField(button) {
        const trackField = button.parentElement;
        const selectElement = trackField.querySelector('select');
        const selectedTrackId = selectElement.value;

        trackField.remove();

        usedTrackIds = Array.from(document.querySelectorAll('#tracksContainer select')).map(select => select.value);

        updateDropdownOptions();
    }

    async function loadPlaylistDetails() {
        try {
            const response = await fetch(`http://localhost:3000/api/playlists/${playlistId}`);
            if (!response.ok) throw new Error('Erreur lors du chargement de la playlist.');

            const playlist = await response.json();
            document.getElementById('name').value = playlist.name;
            document.getElementById('description').value = playlist.description || '';
            document.getElementById('visibility').value = playlist.visibility ? 'true' : 'false';
            document.getElementById('cover_image').value = playlist.cover_image || '';
            updateCoverImagePreview();

            usedTrackIds = playlist.tracks.map(track => track._id);

            const tracksContainer = document.getElementById('tracksContainer');
            playlist.tracks.forEach(track => {
                const trackField = document.createElement('div');
                trackField.classList.add('flex', 'items-center', 'space-x-2', 'mb-2');

                trackField.innerHTML = `
            <select name="tracks[]" class="w-full px-3 py-2 border rounded-lg" onchange="updateUsedTrackIds(this)">
                <option value="${track._id}" selected>${track.title}</option>
                ${availableTracks
                    .filter(t => t._id !== track._id && !usedTrackIds.includes(t._id))
                    .map(t => `<option value="${t._id}">${t.title}</option>`)
                    .join('')}
            </select>
            <button type="button" onclick="removeTrackField(this)" class="text-red-500 hover:text-red-700 font-medium">
                Supprimer
            </button>
        `;

                tracksContainer.appendChild(trackField);
            });

            updateDropdownOptions();
        } catch (error) {
            console.error('Erreur lors du chargement de la playlist :', error);
        }
    }

    async function submitEditPlaylistForm() {
        const form = document.getElementById('editPlaylistForm');
        const formData = new FormData(form);

        const data = Object.fromEntries(formData.entries());

        data.tracks = [];
        const allSelectElements = document.querySelectorAll('#tracksContainer select');
        allSelectElements.forEach(selectElement => {
            const selectedTrackId = selectElement.value;
            if (selectedTrackId) {
                data.tracks.push(selectedTrackId);
            }
        });

        try {
            const response = await fetch(`http://localhost:3000/api/playlists/${playlistId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            if (response.ok) {
                window.location.href = `/playlist/${playlistId}`;
            } else {
                const errorResponse = await response.json();
                alert(`Erreur lors de la mise à jour : ${errorResponse.error || 'Erreur inconnue'}`);
            }
        } catch (error) {
            console.error('Erreur lors de la soumission :', error);
            alert('Une erreur s\'est produite.');
        }
    }

    async function init() {
        await loadAvailableTracks();
        await loadPlaylistDetails();
    }

    document.addEventListener('DOMContentLoaded', init);
</script>

</body>
</html>