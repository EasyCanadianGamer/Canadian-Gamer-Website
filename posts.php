<?php
$db = new SQLite3('cgsite.db');

// IMAGE POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'post';

    if ($type === 'post') {
        $text = $_POST['text'] ?? '';
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $imagePath = 'assets/images/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }
        $stmt = $db->prepare('INSERT INTO posts (text, image) VALUES (:text, :image)');
        $stmt->bindValue(':text', $text);
        $stmt->bindValue(':image', $imagePath);
        $stmt->execute();
        echo json_encode(['status'=>'success','type'=>'post']);
        exit;
    }
}

// GET all posts
$results = $db->query('SELECT * FROM posts ORDER BY timestamp DESC');
$posts = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}
header('Content-Type: application/json');
echo json_encode($posts);
