const mongoose = require('mongoose');

const playlistSchema = new mongoose.Schema({
    name: { type: String, required: true },
    description: { type: String },
    creator: { type: mongoose.Schema.Types.ObjectId, ref: 'Utilisateur', required: true },
    tracks: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Track' }],
    visibility: { type: Boolean, default: true },
    likes: { type: Number, default: 0 },
    cover_image: { type: String }
}, { timestamps: true });

module.exports = mongoose.model('Playlist', playlistSchema);
