const express = require('express');
const Playlist = require('../models/Playlist');
const Track = require('../models/Track');
const Utilisateur = require('../models/Utilisateur');

const router = express.Router();

router.get('/playlists/:id', async (req, res) => {
    try {
        const playlistId = req.params.id;

        const playlist = await Playlist.findById(playlistId)
            .populate('creator')
            .populate({
                path: 'tracks',
                populate: {
                    path: 'album_id',
                },
            });

        if (!playlist) {
            return res.status(404).json({ error: 'Playlist non trouvée.' });
        }

        res.status(200).json({
            name: playlist.name,
            description: playlist.description,
            creator: playlist.creator,
            tracks: playlist.tracks.map(track => ({
                _id: track._id,
                title: track.title,
                audio_url: track.audio_url,
                album_id: track.album_id,
            })),
            visibility: playlist.visibility,
            likes: playlist.likes,
            cover_image: playlist.cover_image,
        });
    } catch (error) {
        console.error('Erreur lors de la récupération de la playlist :', error);
        res.status(500).json({ error: 'Erreur serveur lors de la récupération de la playlist.' });
    }
});

router.get('/playlists', async (req, res) => {
    try {
        // Récupérer toutes les playlists
        const playlists = await Playlist.find()
            .populate('creator', 'username') // Inclut le champ 'username' du créateur
            .populate({
                path: 'tracks',
                populate: {
                    path: 'album_id',
                    select: 'cover_image title',
                },
            }) // Inclut les informations des tracks et des albums
            .select('name cover_image description tracks visibility likes createdAt'); // Limiter les champs retournés

        // Retourner toutes les playlists
        res.status(200).json({ playlists });
    } catch (error) {
        console.error('Erreur lors de la récupération des playlists :', error);
        res.status(500).json({ error: 'Erreur interne du serveur.' });
    }
});

router.post('/playlists', async (req, res) => {
    try {
        const { name, description, session_token, visibility, cover_image } = req.body;
        if (!name || !session_token) {
            return res.status(400).json({ error: 'Le titre et le créateur de la playlist sont requis.' });
        }

        const user = await Utilisateur.findOne({ session_token });
        if (!user) {
            return res.status(404).json({ error: 'Créateur introuvable.' });
        }

        // Création d'une nouvelle playlist
        const newPlaylist = new Playlist({
            name,
            description: description || '',
            creator: user,
            tracks: [],
            visibility: visibility !== undefined ? visibility : true,
            likes: 0,
            cover_image: cover_image || 'https://www.svgrepo.com/show/508699/landscape-placeholder.svg',
        });

        // Enregistrer la playlist dans la base de données
        const savedPlaylist = await newPlaylist.save();

        user.playlists.push(savedPlaylist);

        res.status(201).json({ message: 'Playlist créée avec succès.', playlist: savedPlaylist });
    } catch (error) {
        console.error('Erreur lors de la création de la playlist :', error);
        res.status(500).json({ error: 'Erreur lors de la création de la playlist.' });
    }
});

router.put('/playlists/:id', async (req, res) => {
    try {
        const playlistId = req.params.id;
        const { name, description, visibility, cover_image, tracks } = req.body;

        const playlist = await Playlist.findById(playlistId);
        if (!playlist) {
            return res.status(404).json({ error: 'Playlist non trouvée.' });
        }

        playlist.name = name || playlist.name;
        playlist.description = description || playlist.description;
        playlist.visibility = visibility === 'true';
        playlist.cover_image = cover_image || playlist.cover_image;

        if (tracks && Array.isArray(tracks)) {
            const validTracks = await Track.find({ _id: { $in: tracks } });
            playlist.tracks = validTracks.map(track => track._id);
        }

        await playlist.save();

        return res.status(200).json({ message: 'Playlist mise à jour avec succès.', playlist });
    } catch (error) {
        console.error('Erreur lors de la mise à jour de la playlist :', error);
        return res.status(500).json({ error: 'Erreur lors de la mise à jour de la playlist.' });
    }
});

module.exports = router;
