<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['token'])) {
    $_SESSION['token'] = $data['token'];
    echo json_encode(['message' => 'Token stocké dans la session PHP.']);
} else {
    echo json_encode(['error' => 'Aucun token reçu.']);
}
?>
