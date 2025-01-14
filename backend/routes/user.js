const Utilisateur = require('../models/Utilisateur');
const express = require("express"); // Assurez-vous que le modèle est correctement importé
const router = express.Router();

router.post('/favourite-album', async (req, res) => {
    const token = req.headers.authorization?.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Accès non autorisé. Token manquant.' });
    }

    try {
        // Trouver l'utilisateur avec le token
        const user = await Utilisateur.findOne({ session_token: token });

        if (!user) {
            return res.status(404).json({ error: 'Utilisateur introuvable.' });
        }

        const { albumId } = req.body;

        if (!albumId) {
            return res.status(400).json({ error: 'ID de l’album requis.' });
        }

        // Vérifier si l'album est déjà dans les favoris
        const index = user.favourite_albums.indexOf(albumId);

        if (index > -1) {
            user.favourite_albums.splice(index, 1); // Supprimer
            await user.save();
            return res.status(200).json({ message: 'Album retiré des favoris.' });
        } else {
            user.favourite_albums.push(albumId); // Ajouter
            await user.save();
            return res.status(200).json({ message: 'Album ajouté aux favoris.' });
        }
    } catch (error) {
        console.error('Erreur lors de la mise à jour des favoris :', error);
        res.status(500).json({ error: 'Erreur interne du serveur.' });
    }
});

router.get('/favourites', async (req, res) => {
    const token = req.headers.authorization?.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Accès non autorisé. Token manquant.' });
    }

    try {
        const user = await Utilisateur.findOne({ session_token: token }).populate('favourite_albums');

        if (!user) {
            return res.status(404).json({ error: 'Utilisateur introuvable.' });
        }

        res.status(200).json(user.favourite_albums.map(album => album._id));
    } catch (error) {
        console.error('Erreur lors de la récupération des favoris :', error);
        res.status(500).json({ error: 'Erreur interne du serveur.' });
    }
});


module.exports = router;