const express = require('express');
const Artist = require('../models/Artist'); // Modèle des artistes

const router = express.Router();

// Récupérer tous les artistes
router.get('/artists', async (req, res) => {
    try {
        const artists = await Artist.find().select('name'); // Récupère uniquement les noms
        res.status(200).json(artists);
    } catch (error) {
        console.error('Erreur lors de la récupération des artistes :', error);
        res.status(500).json({ error: 'Erreur lors de la récupération des artistes.' });
    }
});

module.exports = router;
