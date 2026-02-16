<?php
session_start();
header("Content-Type: application/json");


try {
    $db = new PDO("sqlite:blogs.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure table exists
    $db->exec("CREATE TABLE IF NOT EXISTS blogs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert new blog
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            echo json_encode(["status" => "error", "message" => "Missing title or content"]);
            exit();
        }

        $stmt = $db->prepare("INSERT INTO blogs (title, content) VALUES (:title, :content)");
        $stmt->execute([
            ":title" => $title,
            ":content" => $content
        ]);

        echo json_encode(["status" => "success", "message" => "Blog saved"]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
