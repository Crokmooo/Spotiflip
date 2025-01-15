const express = require('express');
const bcrypt = require('bcrypt');
const Utilisateur = require('../models/Utilisateur');

const router = express.Router();

router.post('/register', async (req, res) => {
    const { username, email, password, subscription, gender } = req.body;

    try {
        // Vérifiez si l'utilisateur existe déjà
        const existingUser = await Utilisateur.findOne({ email });
        if (existingUser) {
            return res.status(400).json({ error: 'Cet email est déjà utilisé.' });
        }

        // Hachage du mot de passe
        const hashedPassword = await bcrypt.hash(password, 10);

        // Création d'un nouvel utilisateur
        const newUser = new Utilisateur({
            username,
            email,
            password: hashedPassword,
            subscription: subscription || 'free',
            genre: gender || [],
        });

        await newUser.save();
        res.status(201).json({ message: 'Utilisateur enregistré avec succès.' });
    } catch (error) {
        console.error('Erreur lors de l\'enregistrement :', error);
        res.status(500).json({ error: 'Erreur lors de l\'enregistrement.' });
    }
});

// Connexion
router.post('/login', async (req, res) => {
    const { email, password } = req.body;

    try {
        // Rechercher l'utilisateur par email
        const user = await Utilisateur.findOne({ email }); // Corrigé pour utiliser Utilisateur
        if (!user) {
            return res.status(400).json({ error: 'Utilisateur introuvable.' });
        }

        // Vérifiez le mot de passe haché
        const isPasswordCorrect = await bcrypt.compare(password, user.password);
        if (!isPasswordCorrect) {
            return res.status(401).json({ error: 'Mot de passe incorrect.' });
        }

        // Génération d'un token de session
        user.session_token = `session_${Math.random().toString(36).substring(2)}`;
        await user.save();

        res.status(200).json({
            message: 'Connexion réussie.',
            user: {
                id: user._id,
                username: user.username,
                email: user.email,
                subscription: user.subscription,
                session_token: user.session_token,
            },
        });
    } catch (error) {
        console.error('Erreur lors de la connexion :', error);
        res.status(500).json({ error: 'Erreur lors de la connexion.' });
    }
});

module.exports = router;
