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
  <title>CG-About Me</title>
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
      <h1 class="text-2xl font-bold mb-4">About Me!</h1>
      <p>Sup, my name is <strong>Canadian Gamer</strong>, or you can call me <strong>CG</strong>, <strong>Canadian</strong>, or <strong>Yadi</strong>. I do content on twitch, youtube, and a computer science nerd. (crazy indeed).
      Currently studying Computer Science and always wanted to make a portfolio. Soo I made this which isn't a portfolio but more like a mini social media instead. My favorite color is <strong class = "text-blue-600"> BLUE</strong>!!! 
      </p>
      <h3 class = "text-lg font-bold mb-2"> Why would I build this abomination of website?</h3>
      <p> Well gald you asked. Well over the years I've notice I've been doom scrolling on insta wayy too much and I started to take a liking at customized personal websites and such sooo  
        I pretty much took what I knew about web development to make a simple blog/social media. So please enjoy and stay of what ever shit I post on here! And slowly leave instagram reels cause
        doom scrolling is really killing me ( other then today, I mostly focused on this which led to many other mini side quests and such). Alright, have a great day, afternoon, or night CYA!!! 
      </p>

    </main>

  </div>


</body>
<my-footer></my-footer>

</html>
