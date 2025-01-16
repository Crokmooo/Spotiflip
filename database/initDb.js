const mongoose = require('mongoose');

mongoose.connect('mongodb+srv://***REMOVED***', {})
    .then(() => console.log('Connecté à MongoDB'))
    .catch(err => console.log('Erreur de connexion à mongoDB', err));

const utilisateurSchema = new mongoose.Schema({
    username: { type: String, required: true },
    password: { type: String, required: true },
    email: { type: String, required: true, unique: true },
    created_at: { type: Date, default: Date.now },
    subscription: { type: String },
    genre: { type: String },
    playlists: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Playlist' }],
    session_token: { type: String },
    favourite_albums: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Album' }],
    favourite_playlists: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Playlist' }],
});


const trackSchema = new mongoose.Schema({
    title: { type: String, required: true },
    artist_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Artist', required: true },
    album_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Album' },
    audio_url: { type: String },
});


const artistSchema = new mongoose.Schema({
    name: { type: String, required: true, unique: true },
    genres: { type: [String] },
    albums: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Album' }],
    listens: { type: Number, default: 0 },
    picture: { type: String }
});


const albumSchema = new mongoose.Schema({
    title: { type: String, required: true },
    artist_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Artist', required: true },
    release_date: { type: Date },
    track_list: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Track' }],
    cover_image: { type: String },
});

const playlistSchema = new mongoose.Schema({
    name: { type: String, required: true },
    description: { type: String },
    creator: { type: mongoose.Schema.Types.ObjectId, ref: 'Utilisateur', required: true },
    tracks: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Track' }],
    visibility: { type: Boolean, default: true },
    likes: { type: Number, default: 0 },
    cover_image: { type: String }
}, { timestamps: true });

const Utilisateur = mongoose.model('Utilisateur', utilisateurSchema);
const Track = mongoose.model('Track', trackSchema);
const Artist = mongoose.model('Artist', artistSchema);
const Album = mongoose.model('Album', albumSchema);
const Playlist = mongoose.model('Playlist', playlistSchema);

async function initDb() {
    try {
        await Utilisateur.deleteMany({});
        await Track.deleteMany({});
        await Artist.deleteMany({});
        await Album.deleteMany({});
        await Playlist.deleteMany({});

        console.log('Collections créées et données initiales insérées avec succès !');
    } catch (err) {
        console.error('Erreur lors de l\'initialisation de la base de données :', err);
    } finally {
        await mongoose.connection.close();
    }
}

initDb();