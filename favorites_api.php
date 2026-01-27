<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require 'db_connect.php';

// Resolve user id
$userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->bind_param("s", $_SESSION['username']);
$userStmt->execute();
$userStmt->bind_result($userId);
$userStmt->fetch();
$userStmt->close();

if (empty($userId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

function fetchFavorites($conn, $userId)
{
    $favorites = [];
    $stmt = $conn->prepare("SELECT movie_title AS movieTitle, song_title AS songTitle, artist AS songArtist, poster, embed_url AS embed FROM favorites WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }
    $stmt->close();
    return $favorites;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['success' => true, 'favorites' => fetchFavorites($conn, $userId)]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? '';

if (!in_array($action, ['add', 'remove'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

$movieTitle = trim($input['movieTitle'] ?? '');
$songTitle = trim($input['songTitle'] ?? '');
$artist = trim($input['songArtist'] ?? '');
$poster = $input['poster'] ?? '';
$embed = $input['embed'] ?? '';

if (empty($movieTitle) || empty($songTitle)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit();
}

if ($action === 'add') {
    $stmt = $conn->prepare(
        "INSERT INTO favorites (user_id, movie_title, song_title, artist, poster, embed_url)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE artist = VALUES(artist), poster = VALUES(poster), embed_url = VALUES(embed_url)"
    );
    $stmt->bind_param("isssss", $userId, $movieTitle, $songTitle, $artist, $poster, $embed);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'favorites' => fetchFavorites($conn, $userId)]);
    exit();
}

if ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND movie_title = ? AND song_title = ?");
    $stmt->bind_param("iss", $userId, $movieTitle, $songTitle);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'favorites' => fetchFavorites($conn, $userId)]);
    exit();
}

