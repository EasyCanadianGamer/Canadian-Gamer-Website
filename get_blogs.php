<?php
header("Content-Type: application/json");

try {
    $db = new PDO("sqlite:blogs.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Make sure table exists
    $db->exec("CREATE TABLE IF NOT EXISTS blogs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $db->query("SELECT id, title, content, created_at FROM blogs ORDER BY created_at DESC");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($blogs);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
