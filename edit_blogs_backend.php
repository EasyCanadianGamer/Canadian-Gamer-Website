<?php
header('Content-Type: application/json');

try {
    $db = new PDO("sqlite:blogs.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS blogs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        echo json_encode(['status' => 'success']);
    } else {
        // Update
        $stmt = $db->prepare("UPDATE blogs SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$_POST['title'], $_POST['content'], $_POST['id']]);
        echo json_encode(['status' => 'success']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
