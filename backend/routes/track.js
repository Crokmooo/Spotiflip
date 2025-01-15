const mongoose = require('mongoose');
const express = require('express');
const Track = require('../models/Track');

const router = express.Router();
router.get('/tracks', async (req, res) => {
    try {
        const tracks = await Track.find({}, '_id title audio_url');
        res.status(200).json(tracks);
    } catch (error) {
        console.error('Erreur lors de la récupération des pistes :', error);
        res.status(500).json({ error: 'Erreur serveur lors de la récupération des pistes.' });
    }
});

module.exports = router;