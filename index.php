<?php
// Dynamic MP3 file (you can change this however you like)
$featured_music = "https://www.canadian-gamer.com/assets/audio/featured.mp3";

// Directory where profile pictures are stored
$pfpDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/';

// Possible extensions
$extensions = ['png', 'jpg', 'jpeg', 'gif'];

// Default PFP URL if none found
$pfpUrl = 'https://www.canadian-gamer.com/assets/images/default-pfp.png';

// Find the first matching PFP file
foreach ($extensions as $ext) {
    $files = glob($pfpDir . 'pfp.' . $ext);
    if (!empty($files)) {
        // Use the first match
        $pfpFile = basename($files[0]);
        $pfpUrl = 'https://www.canadian-gamer.com/assets/images/' . $pfpFile;
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
  <title>CG-Home</title>
  <!-- Tailwind CSS (CDN for now) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="themes/blue.css">
  <link rel="stylesheet" href="style.css">

</head>
<script type="module" src= "/components/footer.js"> </script>

<body>

  <!-- Wrapper grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">
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


    <!-- Right column (main content) -->
    <main class="md:col-span-2 p-6 ">
      <h1 class="text-2xl font-bold mb-4">Welcome to My Page</h1>
      <p>This is where your main content will go — videos, blog posts, updates, etc.</p>
    </main>

  </div>


</body>
<my-footer></my-footer>
</html>
