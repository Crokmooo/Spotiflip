const mongoose = require('mongoose');

const artistSchema = new mongoose.Schema({
    name: { type: String, required: true, unique: true },
    genres: { type: [String] },
    albums: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Album' }],
    listens: { type: Number, default: 0 },
});
module.exports = mongoose.model('Artist', artistSchema);
