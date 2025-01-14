const mongoose = require('mongoose');

const albumSchema = new mongoose.Schema({
    title: { type: String, required: true },
    artist_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Artist', required: true },
    release_date: { type: Date },
    track_list: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Track' }],
    cover_image: { type: String },
});
module.exports = mongoose.model('Album', albumSchema);
