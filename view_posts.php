<?php
$dbFile = 'cgsite.db';

$db = new SQLite3($dbFile);

// --- GET ALL POSTS ---
$results = $db->query('SELECT * FROM posts ORDER BY timestamp DESC');
$posts = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}

header('Content-Type: application/json');
echo json_encode($posts);
