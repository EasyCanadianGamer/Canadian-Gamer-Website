<?php
// Start output buffering to catch any accidental HTML/warnings
ob_start();

$dbFile = 'cgsite.db';
$db = new SQLite3($dbFile);

// Enable full error reporting (remove ini_set('display_errors',1) in production later)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

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
$response = ['status' => 'error', 'message' => '', 'debug' => []];

// --- HANDLE POST REQUESTS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'post';

    // --- ADD NEW POST ---
    if ($type === 'post') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $imagePath = null;

        $response['debug']['post_type'] = $type;
        $response['debug']['title_length'] = strlen($title);
        $response['debug']['description_length'] = strlen($description);

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            // First: Check for PHP upload errors
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE   => 'File too large (exceeds upload_max_filesize in php.ini)',
                    UPLOAD_ERR_FORM_SIZE  => 'File too large (exceeds MAX_FILE_SIZE in form)',
                    UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload'
                ];
                $response['message'] = $uploadErrors[$_FILES['image']['error']] ?? 'Unknown upload error';
                $response['debug']['upload_error_code'] = $_FILES['image']['error'];
                $response['debug']['file_size'] = $_FILES['image']['size'] ?? 'N/A';
                ob_end_clean();
                echo json_encode($response);
                exit;
            }

            $targetDir = 'assets/images/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Generate unique filename to prevent overwrites
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $uniqueName = 'img_' . uniqid() . '.' . $ext;
            $targetFile = $targetDir . $uniqueName;

            $response['debug']['original_name'] = $_FILES['image']['name'];
            $response['debug']['saved_as'] = $uniqueName;
            $response['debug']['target_file'] = $targetFile;
            $response['debug']['target_dir_writable'] = is_writable($targetDir) ? 'yes' : 'no';
            $response['debug']['tmp_file_exists'] = file_exists($_FILES['image']['tmp_name']) ? 'yes' : 'no';

            if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
                $response['message'] = 'Security check failed: not an uploaded file';
                ob_end_clean();
                echo json_encode($response);
                exit;
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $response['message'] = 'Failed to move uploaded file (check folder permissions)';
                $response['debug']['php_user'] = get_current_user();
                $response['debug']['dir_perms'] = substr(sprintf('%o', fileperms($targetDir)), -4);
                ob_end_clean();
                echo json_encode($response);
                exit;
            }

            // Success: save only the filename
            $imagePath = $uniqueName;
        }

        // Insert into database
        $stmt = $db->prepare('INSERT INTO posts (title, description, image) VALUES (:title, :description, :image)');
        if (!$stmt) {
            $response['message'] = 'Database prepare failed: ' . $db->lastErrorMsg();
            ob_end_clean();
            echo json_encode($response);
            exit;
        }

        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
        $stmt->bindValue(':image', $imagePath, $imagePath ? SQLITE3_TEXT : SQLITE3_NULL);

        if (!$stmt->execute()) {
            $response['message'] = 'Database insert failed: ' . $db->lastErrorMsg();
            ob_end_clean();
            echo json_encode($response);
            exit;
        }

        $response['status'] = 'success';
        $response['message'] = 'Post added successfully!';
        $response['imagePath'] = $imagePath;

        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    // --- EDIT POST (unchanged, but with clean output) ---
    if ($type === 'edit') {
        // ... (your existing edit code)
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

     // --- DELETE POST ---
     if ($type === 'delete') {
        $id = intval($_POST['id'] ?? 0);

        $response['debug']['received_id'] = $id;
        $response['debug']['table_exists'] = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='posts'") !== null;
        $response['debug']['db_file'] = realpath($dbFile);
        $response['debug']['db_writable'] = is_writable($dbFile) ? 'yes' : 'no';

        if ($id <= 0) {
            $response['message'] = 'Invalid post ID';
            ob_end_clean();
            echo json_encode($response);
            exit;
        }

        if (!$response['debug']['table_exists']) {
            $response['message'] = 'Posts table does not exist';
            ob_end_clean();
            echo json_encode($response);
            exit;
        }

        if (!$response['debug']['db_writable']) {
            $response['message'] = 'Database file is not writable';
            ob_end_clean();
            echo json_encode($response);
            exit;
        }

        // Prepare and execute delete
        $stmt = $db->prepare('DELETE FROM posts WHERE id = :id');
        if (!$stmt) {
            $response['message'] = 'Failed to prepare DELETE statement: ' . $db->lastErrorMsg();
            ob_end_clean();
            echo json_encode($response);
            exit;
        }

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            // Check if any row was actually deleted
            if ($db->changes() === 0) {
                $response['message'] = 'No post found with that ID (already deleted?)';
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Post deleted successfully';
            }
        } else {
            $response['message'] = 'Failed to delete post: ' . $db->lastErrorMsg();
        }

        ob_end_clean();
        echo json_encode($response);
        exit;
    }
}

// --- GET ALL POSTS ---
$results = $db->query('SELECT * FROM posts ORDER BY timestamp DESC');
$posts = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $row;
}

// Clean any output before sending JSON
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($posts);