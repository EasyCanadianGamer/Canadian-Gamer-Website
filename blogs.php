<?php
// Dynamic MP3 file
$featured_music = "https://canadian-gamer.com/assets/audio/featured.mp3";

// Directory where profile pictures are stored
$pfpDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/';
$extensions = ['png', 'jpg', 'jpeg', 'gif'];
$pfpUrl = 'https://canadian-gamer.com/assets/images/default-pfp.png';
foreach ($extensions as $ext) {
    $files = glob($pfpDir . 'pfp.' . $ext);
    if (!empty($files)) {
        $pfpFile = basename($files[0]);
        $pfpUrl = 'https://canadian-gamer.com/assets/images/' . $pfpFile;
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
  <title>CG-Blogs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/themes/blue.css">
  <link rel="alternate" type="application/rss+xml" 
      title="Canadian Gamer RSS Feed" 
      href="https://canadian-gamer.com/rss.php">

</head>

<body>
  <!-- Wrapper grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">

    <!-- Sidebar -->
    <aside class="md:col-span-1">
      <div id="sidebar" class="border-2 p-4 space-y-6 rounded">
        <div class="text-center">
          <img src="<?= $pfp ?>" alt="Profile Picture" class="rounded-2xl w-full mb-2">
          <h2 class="text-xl font-bold">CanadianGamer</h2>
        </div>

        <div>
          <h3 class="font-bold mb-2 border-b pb-1">About Me</h3>
          <p class="text-sm">Hey I like gaming, creating content, and sharing my interests in new tech and hobbies...</p>
          <a href="about.php" class="text-blue-600 underline text-sm">View More</a>
        </div>

        <div>
          <h3 class="font-bold mb-2 border-b pb-1">Featured Music</h3>
          <div class="overflow-hidden relative w-full">
            <h4 class="scrolling-text whitespace-nowrap inline-block">
              Lil Tecca - Dark Thoughts - Dopamine
            </h4>
          </div>
          <audio controls loop class="w-full">
            <source src="<?= $featured_music ?>" type="audio/mp3">
            Your browser does not support the audio element.
          </audio>
        </div>

        <div>
          <h3 class="font-bold mb-2 border-b pb-1">Links</h3>
          <ul class="space-y-1">
          <li><a href="index.php" class="text-blue-600 underline">Home</a></li>
        <li><a href="about.php" class="text-blue-600 underline">About</a></li>
        <li><a href="videos.php" class="text-blue-600 underline">Videos</a></li>
        <li><a href="gallery.php" class="text-blue-600 underline">Gallery</a></li>
        <li><a href="blogs.php" class="text-blue-600 underline">Writings</a></li>
        <li><a href = "live.php" class="text-blue-600 underline" > Live Streams </a></li>
          </ul>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="md:col-span-2 p-6 bg-white border-2 rounded">
      <h1 class="text-2xl font-bold mb-4">Blogs</h1>
      <div id="blogs-container" class="space-y-6">
        <!-- Blogs will be loaded here -->
      </div>
    </main>
  </div>

  <my-footer></my-footer>
  

<script>
async function loadBlogs() {
  try {
    const res = await fetch('get_blogs.php');
    const blogs = await res.json();
    const container = document.getElementById('blogs-container');
    container.innerHTML = '';

    blogs.forEach(blog => {
      const div = document.createElement('div');
      div.className = 'blog-card border p-4 rounded bg-gray-50';
      div.innerHTML = `
        <h2 class="text-xl font-bold mb-2">${blog.title}</h2>
        <div class="text-gray-800 mb-2">${truncate(blog.content, 120)}</div>
        <p class="text-sm text-gray-500">Posted on: ${new Date(blog.created_at).toLocaleString()}</p>
      `;
      div.addEventListener('click', () => {
        window.location.href = 'blog-template.php?id=' + blog.id;
      });
      container.appendChild(div);
    });
  } catch (err) {
    console.error('Failed to load blogs', err);
  }
}

// Truncate text without breaking HTML
function truncate(html, maxLength) {
  const div = document.createElement('div');
  div.innerHTML = html;
  let text = div.innerText;
  return text.length > maxLength ? text.slice(0, maxLength) + '...' : text;
}

document.addEventListener('DOMContentLoaded', loadBlogs);
</script>


</body>
</html>
