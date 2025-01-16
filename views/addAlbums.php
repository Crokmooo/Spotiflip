<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title>Ajouter un Album - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-4 md:p-8">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">Ajouter un Album</h2>

        <form id="addAlbumForm" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="title" class="block font-medium text-gray-700">Titre de l'album</label>
                    <input type="text" id="title" name="title"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                           placeholder="Nom de l'album" required>
                </div>

                <div>
                    <label for="artist_id" class="block font-medium text-gray-700">Artiste</label>
                    <select id="artist_id" name="artist_id" onchange="toggleNewArtistField()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
                        <option value="" selected disabled>-- Sélectionnez un artiste --</option>
                        <!-- Ces options seront générées dynamiquement -->
                        <option value="new">Créer un nouvel artiste</option>
                    </select>
                </div>

                <div id="newArtistField" style="display: none;">
                    <label for="new_artist_name" class="block font-medium text-gray-700">Nom du nouvel artiste</label>
                    <input type="text" id="new_artist_name" name="new_artist_name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                           placeholder="Nom de l'artiste">

                    <div>
                        <label for="genre" class="block font-medium text-gray-700 mt-1">Genre(s) de l'artiste</label>
                        <input type="text" id="genre" name="genre"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                               placeholder="Exemple : Pop, Rock, R&B, ...">
                    </div>
                </div>

                <div>
                    <label for="release_date" class="block font-medium text-gray-700">Date de sortie</label>
                    <input type="date" id="release_date" name="release_date"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Pistes</label>
                    <div id="tracksContainer" class="space-y-4">
                        <!-- Les champs pour les pistes seront ajoutés ici dynamiquement -->
                    </div>
                    <button type="button" onclick="addTrackField()"
                            class="mt-2 text-synthwave-dark border border-synthwave-mid font-medium py-1 px-4 rounded-full hover:shadow-synthwave transition-all">
                        Ajouter une piste
                    </button>
                </div>

                <div>
                    <label for="cover_image" class="block font-medium text-gray-700">URL de l'image de couverture</label>
                    <input type="text" id="cover_image" name="cover_image"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                           placeholder="URL de l'image">
                </div>

                <button type="button" onclick="submitAlbumForm()"
                        class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-4 rounded-full hover:shadow-synthwave transition-all mx-auto block">
                    Ajouter l'album
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await loadArtists();
    });

    async function loadArtists() {
        try {
            const response = await fetch('http://localhost:3000/api/artists');
            const artists = await response.json();

            const artistSelect = document.getElementById('artist_id');
            artists.forEach(artist => {
                const option = document.createElement('option');
                option.value = artist._id;
                option.textContent = artist.name;
                artistSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Erreur lors du chargement des artistes :', error);
        }
    }

    let trackIndex = 0;

    function addTrackField() {
        const container = document.getElementById('tracksContainer');
        const trackField = document.createElement('div');
        trackField.classList.add('flex', 'space-x-2', 'items-center');
        trackField.innerHTML = `
            <input type="text" name="tracks[${trackIndex}][name]" placeholder="Nom de la piste"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark" required>
            <input type="text" name="tracks[${trackIndex}][url]" placeholder="Lien de la piste"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark" required>
            <button type="button" onclick="removeTrackField(this)"
                    class="text-red-500 hover:text-red-700 font-medium">
                Supprimer
            </button>
        `;
        container.appendChild(trackField);
        trackIndex++;
    }

    function removeTrackField(button) {
        const container = document.getElementById('tracksContainer');
        container.removeChild(button.parentElement);
    }

    function toggleNewArtistField() {
        const artistSelect = document.getElementById('artist_id');
        const newArtistField = document.getElementById('newArtistField');

        if (artistSelect.value === 'new') {
            newArtistField.style.display = 'block';
        } else {
            newArtistField.style.display = 'none';
        }
    }

    async function submitAlbumForm() {
        const form = document.getElementById('addAlbumForm');
        const formData = new FormData(form);

        const data = Object.fromEntries(formData.entries());
        data.tracks = [];

        document.querySelectorAll('#tracksContainer div').forEach(trackDiv => {
            const name = trackDiv.querySelector('input[name$="[name]"]').value;
            const url = trackDiv.querySelector('input[name$="[url]"]').value;
            if (name && url) {
                data.tracks.push({ name, url });
            }
        });

        if (data.genre) {
            data.genres = data.genre.split(',').map(genre => genre.trim());
            delete data.genre;
        } else {
            data.genres = [];
        }

        try {
            const response = await fetch('http://localhost:3000/api/add-album', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            if (response.ok) {
                alert('Album ajouté avec succès !');
                form.reset();
                document.getElementById('tracksContainer').innerHTML = '';
            } else {
                alert(result.error || 'Une erreur est survenue.');
            }
        } catch (error) {
            console.error('Erreur :', error);
            alert('Erreur de communication avec le serveur.');
        }
    }

</script>
</body>
</html>
