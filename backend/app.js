const express = require('express');
const bodyParser = require('body-parser');
const mongoose = require('mongoose');

const authRoutes = require('./routes/auth');
const albumRoutes = require('./routes/album');
const artistRoutes = require('./routes/artist');
const userRoutes = require('./routes/user');
const playlistRoutes = require('./routes/playlist');
const trackRoutes = require('./routes/track');

const app = express();
const cors = require('cors');

const corsOptions = {
    origin: '*',
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization'],
};

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(cors(corsOptions));

mongoose.connect('mongodb+srv://***REMOVED***', {
    useNewUrlParser: true,
    useUnifiedTopology: true,
})
    .then(() => console.log('Connecté à MongoDB'))
    .catch(err => console.error('Erreur de connexion à MongoDB', err));

app.use('/api', artistRoutes);
app.use('/api', albumRoutes);
app.use('/auth', authRoutes);
app.use('/api', userRoutes);
app.use('/api', playlistRoutes);
app.use('/api', trackRoutes);

// Démarrage du serveur
const PORT = 3000;
app.listen(PORT, () => console.log(`Serveur en cours d'exécution sur http://localhost:${PORT}`));
