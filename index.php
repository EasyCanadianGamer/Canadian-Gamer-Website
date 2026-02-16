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
  <link rel="alternate" type="application/rss+xml" 
      title="Canadian Gamer RSS Feed" 
      href="https://www.canadian-gamer.com/rss.php">

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
        <li><a href="blogs.php" class="text-blue-600 underline">Writings</a></li>
        <li><a href = "live.php" class="text-blue-600 underline" > Live Streams </a></li>
      </ul>
    </div>

  </div>
</aside>


    <!-- Right column (main content) -->
    <main class="md:col-span-2 p-6 space-y-6">
      <header>
        <p class="text-sm uppercase tracking-wide text-gray-500">Latest from the blog</p>
        <h1 class="text-3xl font-bold">What I'm writing about right now</h1>
        <p class="text-gray-600 mt-2">Fresh takes, game dev notes, and anything else that has my attention this week.</p>
      </header>

      <section id="home-blog-cards" class="grid gap-4 sm:grid-cols-2">
        <article class="border rounded-lg p-4 bg-white shadow-sm animate-pulse">
          <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
          <div class="space-y-2">
            <div class="h-3 bg-gray-200 rounded"></div>
            <div class="h-3 bg-gray-100 rounded"></div>
            <div class="h-3 w-1/2 bg-gray-100 rounded"></div>
          </div>
        </article>
        <article class="border rounded-lg p-4 bg-white shadow-sm hidden sm:block animate-pulse">
          <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
          <div class="space-y-2">
            <div class="h-3 bg-gray-200 rounded"></div>
            <div class="h-3 bg-gray-100 rounded"></div>
            <div class="h-3 w-1/2 bg-gray-100 rounded"></div>
          </div>
        </article>
      </section>

      <div class="text-right">
        <a href="blogs.php" class="text-blue-600 font-semibold hover:underline">See all posts →</a>
      </div>
    </main>

  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('home-blog-cards');
  if (!container) return;

  fetch('get_blogs.php')
    .then(res => res.json())
    .then(blogs => {
      if (!Array.isArray(blogs) || blogs.length === 0) {
        container.innerHTML = `
          <article class="border rounded-lg p-4 bg-gray-50 shadow-sm">
            <h2 class="text-xl font-semibold mb-2">Nothing posted yet</h2>
            <p class="text-gray-600">I'm still drafting the first few entries. Check back soon or jump over to the full blog page for updates.</p>
          </article>
        `;
        return;
      }

      container.innerHTML = '';
      blogs.slice(0, 4).forEach(blog => {
        const card = document.createElement('article');
        card.className = 'border rounded-lg p-4 bg-white shadow-sm flex flex-col cursor-pointer hover:border-blue-500 transition';
        card.innerHTML = `
          <h2 class="text-xl font-semibold mb-2">${escapeHtml(blog.title)}</h2>
          <p class="text-gray-600 flex-grow">${truncate(blog.content, 140)}</p>
          <p class="text-xs text-gray-500 mt-4">Posted ${formatDate(blog.created_at)}</p>
        `;
        card.addEventListener('click', () => {
          window.location.href = 'blog-template.php?id=' + blog.id;
        });
        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error('Failed to load blogs', err);
      container.innerHTML = `
        <article class="border rounded-lg p-4 bg-red-50 text-red-700">
          <h2 class="text-lg font-semibold mb-1">Something went wrong</h2>
          <p>Couldn't load the latest posts right now. You can still browse everything on the blog page.</p>
        </article>
      `;
    });
});

function truncate(html, maxLength) {
  const div = document.createElement('div');
  div.innerHTML = html;
  const text = div.innerText.trim();
  return text.length > maxLength ? text.slice(0, maxLength) + '…' : text;
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return isNaN(date) ? 'just now' : date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.innerText = str ?? '';
  return div.innerHTML;
}
</script>

</body>
<my-footer></my-footer>
</html>
