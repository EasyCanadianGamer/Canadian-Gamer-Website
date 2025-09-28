<?php
$dbFile = 'cgsite.db';
$db = new SQLite3($dbFile);

// --- CREATE TABLE IF NOT EXISTS ---
$db->exec("
CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    description TEXT,
    image TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// --- INIT RESPONSE ---
$response = ['status' => 'error', 'debug' => []];

// --- HANDLE POST REQUESTS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'post';

    // --- ADD NEW POST ---
    if ($type === 'post') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $imagePath = null;

        $response['debug']['post_type'] = $type;
        $response['debug']['title'] = $title;
        $response['debug']['description'] = $description;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $targetDir = 'assets/images/';
            $targetFile = $targetDir . basename($_FILES['image']['name']);

            $response['debug']['tmp_file'] = $_FILES['image']['tmp_name'] ?? '';
            $response['debug']['tmp_file_exists'] = file_exists($_FILES['image']['tmp_name']);
            $response['debug']['tmp_file_perm'] = is_writable($_FILES['image']['tmp_name']) ? 'writable' : 'not writable';
            $response['debug']['target_dir_exists'] = is_dir($targetDir);
            $response['debug']['target_dir_writable'] = is_writable($targetDir);

            if (is_uploaded_file($_FILES['image']['tmp_name']) && is_writable($targetDir)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = basename($targetFile); // save only filename
                } else {
                    $response['debug']['error'] = 'Failed to move uploaded file';
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['debug']['error'] = 'Upload failed: temp file or target dir not writable';
                echo json_encode($response);
                exit;
            }
        }

        // Insert into database
        $stmt = $db->prepare('INSERT INTO posts (title, description, image) VALUES (:title, :description, :image)');
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':image', $imagePath);
        $stmt->execute();

        $response['status'] = 'success';
        $response['imagePath'] = $imagePath;
        echo json_encode($response);
        exit;
    }

    // --- EDIT POST ---
    if ($type === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        $response['debug'] = compact('id', 'title', 'description');

        $stmt = $db->prepare('UPDATE posts SET title=:title, description=:description WHERE id=:id');
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $response['status'] = 'success';
        } else {
            $response['error'] = 'Failed to update post';
        }

        echo json_encode($response);
        exit;
    }

    // --- DELETE POST ---
    if ($type === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $response['debug'] = [
            'post_id' => $id,
            'table_exists' => $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='posts'") ? true : false,
            'db_file' => realpath($dbFile),
            'db_writable' => is_writable($dbFile)
        ];

        if ($id > 0 && $response['debug']['table_exists'] && $response['debug']['db_writable']) {
            $stmt = $db->prepare('DELETE FROM posts WHERE id=:id');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

            if ($stmt->execute()) {
                $response['status'] = 'success';
            } else {
                $response['error'] = 'Failed to execute DELETE statement';
            }
        } else {
            $response['error'] = 'Invalid ID, missing table, or DB not writable';
        }

        echo json_encode($response);
        exit;
    }
}

// --- GET ALL POSTS (for gallery/admin view) ---
$results = $db->query('SELECT * FROM posts ORDER BY timestamp DESC');
$posts = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}
echo json_encode($posts);
