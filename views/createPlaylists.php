<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'components/head.php'; ?>
    <title>Créer une Playlist - Spotiflip</title>
</head>
<body class="bg-white text-gray-800 font-montserrat">

<?php require_once 'components/navbar.php'; ?>

<main class="p-4 md:p-8">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-700 mb-6 text-center">Créer une Playlist</h2>

        <form id="createPlaylistForm" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block font-medium text-gray-700">Titre de la Playlist</label>
                    <input type="text" id="name" name="name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                           placeholder="Nom de la playlist" required>
                </div>

                <div>
                    <label for="description" class="block font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                              placeholder="Décrivez votre playlist" rows="4"></textarea>
                </div>

                <div>
                    <label for="cover_image" class="block font-medium text-gray-700">URL de l'image de couverture</label>
                    <input type="text" id="cover_image" name="cover_image"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark"
                           placeholder="URL de l'image">
                </div>

                <div>
                    <label for="visibility" class="block font-medium text-gray-700">Visibilité</label>
                    <select id="visibility" name="visibility"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-synthwave-dark">
                        <option value="true">Publique</option>
                        <option value="false">Privée</option>
                    </select>
                </div>

                <button type="button" onclick="submitPlaylistForm()"
                        class="text-synthwave-dark border border-synthwave-mid font-medium py-1.5 px-4 rounded-full hover:shadow-synthwave transition-all mx-auto block">
                    Créer la Playlist
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="bg-white mt-8 py-4 border-t border-gray-300 text-center">
    <p class="text-gray-500">© 2024 Spotiflip - Tous droits réservés</p>
</footer>

<script>
    async function submitPlaylistForm() {
        const form = document.getElementById('createPlaylistForm');
        const formData = new FormData(form);

        const data = Object.fromEntries(formData.entries());
        data.visibility = data.visibility === "true";
        data.session_token = '<?php echo $_SESSION['token']?>'
        console.log(data);
        try {
            const response = await fetch('http://localhost:3000/api/playlists', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            if (response.ok) {
                alert('Playlist créée avec succès !');
                form.reset();
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
