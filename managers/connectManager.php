<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] == null ? '' : $_POST['action'];
    $response = ['status' => 'error', 'message' => 'Action inconnue'];

    if ($action === 'register') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'Tous les champs sont obligatoires.';
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Email invalide.';
            } else {
                $response = [
                    'status' => 'success',
                    'message' => 'Données validées.',
                    'data' => [
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                    ]
                ];
            }
        }
    } elseif ($action === 'login') {
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
