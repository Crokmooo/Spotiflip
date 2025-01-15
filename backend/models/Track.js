const mongoose = require('mongoose');

const trackSchema = new mongoose.Schema({
    title: { type: String, required: true },
    artist_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Artist', required: true },
    album_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Album' },
    audio_url: { type: String },
});
module.exports = mongoose.model('Track', trackSchema);
