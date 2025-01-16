const express = require('express');
const mongoose = require('mongoose');
const Album = require('../models/Album');
const Artist = require('../models/Artist');
const Track = require('../models/Track');
const Utilisateur = require('../models/Utilisateur');

const router = express.Router();

router.post('/add-album', async (req, res) => {
    try {
        console.log('Données reçues :', req.body);
        const { title, artist_id, new_artist_name, release_date, tracks, cover_image, genres } = req.body;

        const existingAlbum = await Album.findOne({ title });
        if (existingAlbum) {
            return res.status(400).json({ error: 'Un album avec ce titre existe déjà.' });
        }

        let artistId = artist_id;
        if (artist_id === 'new') {
            const genresList = [];
            if (genres) {
                const genresArray = Array.isArray(genres) ? genres : [genres];
                for (const genre of genresArray) {
                    genresList.push(genre);
                }
            }
            if (!new_artist_name) {
                return res.status(400).json({ error: 'Le nom du nouvel artiste est requis.' });
            }
            const newArtist = new Artist({
                name: new_artist_name,
                genres: genresList,
                albums: []
            });
            await newArtist.save();
            artistId = newArtist._id;
        }

        const newAlbum = new Album({
            title,
            artist_id: artistId,
            release_date: release_date ? new Date(release_date) : undefined,
            track_list: [],
            cover_image,
        });
        await newAlbum.save();
        const albumId = newAlbum._id;

        const trackIds = [];
        if (tracks) {
            const trackArray = Array.isArray(tracks) ? tracks : [tracks];
            for (const track of trackArray) {
                const newTrack = new Track({
                    title: track.name,
                    artist_id: artistId,
                    audio_url: track.url,
                    album_id: albumId,
                });
                await newTrack.save();
                trackIds.push(newTrack._id);
            }
        }


        const album = await Album.findById(albumId);
        if (album) {
            console.log(album);
            for (const trackId of trackIds) {
                album.track_list.push(trackId);
            }
            await album.save();
        }
        const artist = await Artist.findById(artistId);
        if (artist) {
            artist.albums.push(newAlbum._id);
            await artist.save();
        }


        res.status(201).json({ message: 'Album ajouté avec succès.', album: newAlbum });
    } catch (error) {
        console.error('Erreur lors de l\'ajout de l\'album :', error);
        res.status(500).json({ error: 'Erreur lors de l\'ajout de l\'album.' });
    }
});

router.get('/albums', async (req, res) => {
    try {
        const albums = await Album.find().populate('artist_id', 'name');
        res.status(200).json(albums);
    } catch (error) {
        console.error('Erreur lors de la récupération des albums :', error);
        res.status(500).json({ error: 'Erreur lors de la récupération des albums.' });
    }
});

router.get('/albums/recent', async (req, res) => {
    try {
        const token = req.headers.authorization?.split(' ')[1];
        let favouriteAlbums = new Set();

        if (token) {
            const user = await Utilisateur.findOne({ session_token: token });
            if (user) {
                favouriteAlbums = new Set(user.favourite_albums || []);
            }
        }

        const albums = await Album.find().sort({ release_date: -1 }).limit(10).populate('artist_id', 'name');

        const albumsWithFavouriteFlag = albums.map(album => ({
            ...album.toObject(),
            isFavourite: favouriteAlbums.has(album._id.toString()),
        }));

        res.status(200).json(albumsWithFavouriteFlag);
    } catch (error) {
        console.error('Erreur lors de la récupération des albums récents :', error);
        res.status(500).json({ error: 'Erreur serveur' });
    }
});

router.post('/album-details', async (req, res) => {
    try {
        const { albumIds } = req.body;
        if (!albumIds || !Array.isArray(albumIds)) {
            return res.status(400).json({ error: 'Liste d\'IDs invalide' });
        }

        const albums = await Album.find({ _id: { $in: albumIds } }).populate('artist_id');
        res.status(200).json(albums);
    } catch (error) {
        console.error('Erreur lors de la récupération des détails des albums :', error);
        res.status(500).json({ error: 'Erreur serveur lors de la récupération des albums.' });
    }
});

router.get('/albums/:id', async (req, res) => {
    try {
        const album = await Album.findById(req.params.id).populate('artist_id').populate('track_list');
        if (!album) {
            return res.status(404).json({ error: "Album non trouvé." });
        }
        res.status(200).json(album);
    } catch (error) {
        console.error('Erreur lors de la récupération de l\'album :', error);
        res.status(500).json({ error: "Erreur serveur lors de la récupération de l'album." });
    }
});


module.exports = router;
