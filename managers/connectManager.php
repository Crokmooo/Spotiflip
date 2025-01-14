<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // La réponse sera en JSON
    $action = $_POST['action'] == null ? '' : $_POST['action'];
    $response = ['status' => 'error', 'message' => 'Action inconnue'];

    if ($action === 'register') {
        // Récupérer les données
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Validation des champs
        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'Tous les champs sont obligatoires.';
        } else {
            // Valider l'email (exemple)
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Email invalide.';
            } else {
                // Validation réussie, transmettre au JS
                $response = [
                    'status' => 'success',
                    'message' => 'Données validées.',
                    'data' => [
                        'username' => $username,
                        'email' => $email,
                        'password' => $password, // Ne pas transmettre directement en production !
                    ]
                ];
            }
        }
    } elseif ($action === 'login') {
        // Exemple de traitement pour la connexion
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($email) || empty($password)) {
            $response['message'] = 'Tous les champs sont obligatoires.';
        } else {
            $response = [
                'status' => 'success',
                'message' => 'Connexion validée.',
                'data' => [
                    'email' => $email,
                    'password' => $password,
                ]
            ];
        }
    }

    echo json_encode($response);
    exit;
}
