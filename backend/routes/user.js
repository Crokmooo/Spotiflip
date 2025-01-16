const Utilisateur = require('../models/Utilisateur');
const Playlist = require('../models/Playlist');
const express = require("express");
const router = express.Router();

router.post('/favourite-album', async (req, res) => {
    const token = req.headers.authorization?.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Accès non autorisé. Token manquant.' });
    }

    try {
        const user = await Utilisateur.findOne({ session_token: token });

        if (!user) {
            return res.status(404).json({ error: 'Utilisateur introuvable.' });
        }

        const { albumId } = req.body;

        if (!albumId) {
            return res.status(400).json({ error: 'ID de l’album requis.' });
        }

        const index = user.favourite_albums.indexOf(albumId);

        if (index > -1) {
            user.favourite_albums.splice(index, 1);
            await user.save();
            return res.status(200).json({ message: 'Album retiré des favoris.' });
        } else {
            user.favourite_albums.push(albumId);
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

router.get('/user/playlists', async (req, res) => {
    try {
        const token = req.headers.authorization?.split(' ')[1];

        if (!token) {
            return res.status(401).json({ error: "Token d'authentification manquant." });
        }

        const user = await Utilisateur.findOne({ session_token: token });

        if (!user) {
            return res.status(404).json({ error: "Utilisateur introuvable." });
        }

        const playlists = await Playlist.find({ creator: user._id })
            .populate('tracks', 'title audio_url')
            .select('name cover_image description tracks visibility likes');

        res.status(200).json(playlists);
    } catch (error) {
        console.error('Erreur lors de la récupération des playlists :', error);
        res.status(500).json({ error: "Erreur serveur lors de la récupération des playlists." });
    }
});

router.post('/favourite-playlist', async (req, res) => {
    const token = req.headers.authorization?.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Accès non autorisé. Token manquant.' });
    }

    try {
        const user = await Utilisateur.findOne({ session_token: token });

        if (!user) {
            return res.status(404).json({ error: 'Utilisateur introuvable.' });
        }

        const { playlistId } = req.body;

        if (!playlistId) {
            return res.status(400).json({ error: 'ID de la playlist requis.' });
        }

        const isFavourite = user.favourite_playlists.includes(playlistId);

        if (isFavourite) {
            user.favourite_playlists = user.favourite_playlists.filter(id => id.toString() !== playlistId);
            await user.save();
            return res.status(200).json({ message: 'Playlist retirée des favoris.' });
        } else {
            user.favourite_playlists.push(playlistId);
            await user.save();
            return res.status(200).json({ message: 'Playlist ajoutée aux favoris.' });
        }
    } catch (error) {
        console.error('Erreur lors de la gestion des favoris :', error);
        res.status(500).json({ error: 'Erreur interne du serveur.' });
    }
});

router.get('/favourites/playlists', async (req, res) => {
    const token = req.headers.authorization?.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Accès non autorisé. Token manquant.' });
    }

    try {
        const user = await Utilisateur.findOne({ session_token: token }).populate('favourite_playlists');

        if (!user) {
            return res.status(404).json({ error: 'Utilisateur introuvable.' });
        }

        res.status(200).json(user.favourite_playlists);
    } catch (error) {
        console.error('Erreur lors de la récupération des playlists favorites :', error);
        res.status(500).json({ error: 'Erreur interne du serveur.' });
    }
});

module.exports = router;