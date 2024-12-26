const mongoose = require('mongoose');

mongoose.connect('mongodb+srv://***REMOVED***', {})
    .then(() => console.log('Connecté à MongoDB'))
    .catch(err => console.log('Erreur de connexion à mongoDB', err));

// Définition des schémas
const utilisateurSchema = new mongoose.Schema({
    username: { type: String, required: true },
    password: { type: String, required: true },
    email: { type: String, required: true },
    created_at: { type: Date, default: Date.now },
    subscription: { type: String },
    genre: { type: [String] },
    default_playlist_id: { type: String },
    playlists: { type: [String] },
    liked_tracks: { type: [String] }
});

const trackSchema = new mongoose.Schema({
    title: { type: String, required: true },
    artist_id: { type: String, required: true },
    duration: { type: Number, default: 0 },
    genres: { type: [String] },
    album_id: { type: String },
    audio_url: { type: String }
});

const artistSchema = new mongoose.Schema({
    name: { type: String, required: true },
    genres: { type: [String] },
    albums: { type: [String] },
    listens: { type: Number, default: 0 }
});

const albumSchema = new mongoose.Schema({
    title: { type: String, required: true },
    artist_id: { type: String, required: true },
    release_date: { type: Date },
    track_list: { type: [String] },
    cover_image: { type: String }
});

const playlistSchema = new mongoose.Schema({
    name: { type: String, required: true },
    user_id: { type: String, required: true },
    tracks: { type: [String] },
    created_date: { type: Date, default: Date.now },
    updated_date: { type: Date, default: Date.now }
});

const Utilisateur = mongoose.model('Utilisateur', utilisateurSchema);
const Track = mongoose.model('Track', trackSchema);
const Artist = mongoose.model('Artist', artistSchema);
const Album = mongoose.model('Album', albumSchema);
const Playlist = mongoose.model('Playlist', playlistSchema);

async function initDb() {
    try {
        // Suppression des collections existantes
        await Utilisateur.deleteMany({});
        await Track.deleteMany({});
        await Artist.deleteMany({});
        await Album.deleteMany({});
        await Playlist.deleteMany({});

        // Insertion des données initiales
        await Utilisateur.create({
            username: 'exampleUser ',
            password: 'examplePassword',
            email: 'example@example.com',
            created_at: new Date(),
            subscription: 'free',
            genre: [],
            default_playlist_id: '',
            playlists: [],
            liked_tracks: []
        });

        await Track.create({
            title: 'exampleTrack',
            artist_id: 'exampleArtistId',
            duration: 180,
            genres: ['pop'],
            album_id: 'exampleAlbumId',
            audio_url: 'http://example.com/audio'
        });

        await Artist.create({
            name: 'exampleArtist',
            genres: ['pop'],
            albums: [],
            listens: 0
        });

        await Album.create({
            title: 'exampleAlbum',
            artist_id: 'exampleArtistId',
            release_date: new Date(),
            track_list: [],
            cover_image: 'http://example.com/cover.jpg'
        });

        await Playlist.create({
            name: 'examplePlaylist',
            user_id: 'exampleUser Id',
            tracks: [],
            created_date: new Date(),
            updated_date: new Date()
        });

        console.log('Collections créées et données initiales insérées avec succès !');
    } catch (err) {
        console.error('Erreur lors de l\'initialisation de la base de données :', err);
    } finally {
        await mongoose.connection.close();
    }
}

initDb();