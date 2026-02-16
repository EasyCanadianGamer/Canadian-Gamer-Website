<?php
// blog-template.php

// DB connection
$db = new PDO('sqlite:' . __DIR__ . '/blogs.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get blog ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch blog
$stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    die("Blog not found");
}

// Dynamic MP3
$featured_music = "https://www.canadian-gamer.com/assets/audio/featured.mp3";

// Profile picture
$pfpDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/';
$extensions = ['png','jpg','jpeg','gif'];
$pfpUrl = 'https://www.canadian-gamer.com/assets/images/default-pfp.png';
foreach ($extensions as $ext) {
    $files = glob($pfpDir.'pfp.'.$ext);
    if (!empty($files)) {
        $pfpFile = basename($files[0]);
        $pfpUrl = 'https://www.canadian-gamer.com/assets/images/'.$pfpFile;
        break;
    }
}
$pfp = $pfpUrl;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($blog['title']) ?> - CanadianGamer</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="/themes/blue.css">
<link rel="stylesheet" href="style.css">
<link rel="alternate" type="application/rss+xml" 
      title="Canadian Gamer RSS Feed" 
      href="https://www.canadian-gamer.com/rss.php">

<!-- Tailwind Typography Plugin -->
<script>
tailwind.config = {
  plugins: [require('@tailwindcss/typography')],
}
</script>

<style>
/* Style links inside the rendered blog body without relying on Tailwind build tooling */
.blog-content a {
    color: #2563eb;
    text-decoration: underline;
    font-weight: 600;
}
.blog-content a:hover,
.blog-content a:focus {
    color: #1d4ed8;
}
</style>
</head>
<body>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">
  <!-- Sidebar -->
    <!-- Left column (profile/sidebar) -->
    <aside class="md:col-span-1">
  <div id="sidebar" class="border-2 p-4 space-y-6 rounded ">
    
    <!-- Profile -->
    <div class="text-center">
    <img src="<?= $pfp ?>" alt="Profile Picture" class="rounded-2xl w-full mb-2">
    <h2 class="text-xl font-bold">CanadianGamer</h2>
    </div>

    <!-- Bio -->
    <div>
      <h3 class="font-bold mb-2 border-b pb-1">About Me</h3>
      <p class="text-sm">
        Hey I like gaming, creating content, and sharing my interests in new tech
        and my other hobbies ...
      </p>
      <a href="about.php" class="text-blue-600 underline text-sm">View More</a>
    </div>

    <!-- Music Player -->
     
    <div>
      <h3 class="font-bold mb-2 border-b pb-1">Featured Music</h3>
      <!-- Scrolling Text -->
      <div class="overflow-hidden relative w-full">
              <!-- Artist - Song title - Album -->

  <h4 class="scrolling-text whitespace-nowrap inline-block">
    Lil Tecca - Dark Thoughts - Dopamine
  </h4>
</div>
      <!-- audio -->

      <audio controls loop  class="w-full">
    <source src="<?php echo $featured_music; ?>" type="audio/mp3">
    Your browser does not support the audio element.
</audio>

    </div>

    <!-- Links/Nav -->
    <div>
      <h3 class="font-bold mb-2 border-b pb-1">Links</h3>
      <ul class="space-y-1">
        <li><a href="index.php" class="text-blue-600 underline">Home</a></li>
        <li><a href="about.php" class="text-blue-600 underline">About</a></li>
        <li><a href="videos.php" class="text-blue-600 underline">Videos</a></li>
        <li><a href="gallery.php" class="text-blue-600 underline">Gallery</a></li>
      </ul>
    </div>

  </div>
</aside>

  <!-- Main content -->
  <main class="md:col-span-2 p-6 bg-white border-2 rounded">
      <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($blog['title']) ?></h1>
      <p class="text-sm text-gray-500 mb-6">Posted on: <?= date('F j, Y, g:i a', strtotime($blog['created_at'])) ?></p>
      
      <!-- Rich blog content -->
      <div class="blog-content prose max-w-full">
          <?= $blog['content'] ?>
      </div>
  </main>
</div>

<my-footer></my-footer>

</body>
</html>
