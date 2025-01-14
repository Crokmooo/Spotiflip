<?php
session_start(); // Démarrer la session

// Lire les données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si le token est présent
if (isset($data['token'])) {
    $_SESSION['token'] = $data['token']; // Stocker le token dans la session
    echo json_encode(['message' => 'Token stocké dans la session PHP.']);
} else {
    echo json_encode(['error' => 'Aucun token reçu.']);
}
?>
