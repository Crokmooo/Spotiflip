const express = require('express');
const Artist = require('../models/Artist');
const Album = require('../models/Album');

const router = express.Router();

router.get('/artists', async (req, res) => {
    try {
        const artists = await Artist.find().select('name');
        res.status(200).json(artists);
    } catch (error) {
        console.error('Erreur lors de la récupération des artistes :', error);
        res.status(500).json({ error: 'Erreur lors de la récupération des artistes.' });
    }
});

router.get('/artist/:id', async (req, res) => {
    try {
        const artistId = req.params.id;

        const artist = await Artist.findById(artistId).populate('albums');

        if (!artist) {
            return res.status(404).json({ error: 'Artiste non trouvé.' });
        }

        res.status(200).json(artist);
    } catch (error) {
        console.error('Erreur lors de la récupération de l\'artiste :', error);
        res.status(500).json({ error: 'Erreur serveur lors de la récupération de l\'artiste.' });
    }
});

router.get('/artist/:id/albums', async (req, res) => {
    try {
        const artistId = req.params.id;

        const albums = await Album.find({ artist_id: artistId }).sort({ release_date: -1 });

        res.status(200).json(albums);
    } catch (error) {
        console.error('Erreur lors de la récupération des albums de l\'artiste :', error);
        res.status(500).json({ error: 'Erreur serveur lors de la récupération des albums.' });
    }
});

router.post('/artist/:id/update-image', async (req, res) => {
    try {
        const artistId = req.params.id;
        const { image_url } = req.body;

        if (!image_url) {
            return res.status(400).json({ error: 'Le lien de l\'image est requis.' });
        }

        const artist = await Artist.findByIdAndUpdate(
            artistId,
            { picture : image_url },
            { new: true, runValidators: true }
        );

        if (!artist) {
            return res.status(404).json({ error: 'Artiste non trouvé.' });
        }

        res.status(200).json({ message: 'Image mise à jour avec succès.', artist });
    } catch (error) {
        console.error('Erreur lors de la mise à jour de l\'image de l\'artiste :', error);
        res.status(500).json({ error: 'Erreur serveur lors de la mise à jour de l\'image.' });
    }
});

module.exports = router;
