const mongoose = require('mongoose');

const utilisateurSchema = new mongoose.Schema({
    username: { type: String, required: true },
    password: { type: String, required: true },
    email: { type: String, required: true, unique: true },
    created_at: { type: Date, default: Date.now },
    subscription: { type: String },
    genre: { type: String },
    playlists: { type: [String] },
    session_token: { type: String },
    favourite_albums: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Album' }],
});

module.exports = mongoose.model('Utilisateur', utilisateurSchema);
