<?php
// Dynamic MP3 file
$featured_music = "https://canadian-gamer.com/assets/audio/featured.mp3";

// Directory for profile pictures
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
<title>CG-Gallery</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="themes/blue.css">
<link rel="stylesheet" href="style.css">
</head>
<script type="module" src="/components/footer.js"></script>
<body>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">

  <!-- Sidebar -->
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
          Hey I like gaming, creating content, and sharing my interests in new tech and my other hobbies ...
        </p>
        <a href="about.php" class="text-blue-600 underline text-sm">View More</a>
      </div>

      <!-- Music Player -->
      <div>
        <h3 class="font-bold mb-2 border-b pb-1">Featured Music</h3>
        <div class="overflow-hidden relative w-full">
          <h4 class="scrolling-text whitespace-nowrap inline-block">
            Lil Tecca - Dark Thoughts - Dopamine
          </h4>
        </div>
        <audio controls loop class="w-full mt-2">
          <source src="<?= $featured_music ?>" type="audio/mp3">
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
  <main class="md:col-span-2 p-6">
    <!-- Search Bar -->
    <div class="mb-6">
      <input type="text" placeholder="Search gallery..." class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" id="gallery-search">
    </div>

    <!-- Section Header -->
    <h1 class="text-2xl font-bold mb-4">Gallery</h1>

    <!-- Gallery Grid -->
    <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
  </main>

</div>

<!-- Modal for enlarged image -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-80 flex flex-col items-center justify-center hidden z-50 p-4 overflow-auto">
  <span id="close-modal" class="absolute top-4 right-6 text-white text-3xl cursor-pointer">&times;</span>
  <img id="modal-img" class="max-h-[70vh] max-w-full rounded-lg shadow-lg mb-4">
  <h2 id="modal-title" class="text-white text-xl font-bold mb-2"></h2>
  <p id="modal-desc" class="text-gray-300 text-sm text-center max-w-2xl"></p>
</div>

<script>
async function loadGallery() {
  try {
    const res = await fetch('view_posts.php');
    const posts = await res.json();

    const galleryGrid = document.getElementById('gallery-grid');
    galleryGrid.innerHTML = '';

    posts.forEach(post => {
      const card = document.createElement('div');
      card.className = 'border rounded-lg overflow-hidden shadow-sm cursor-pointer';

      const img = document.createElement('img');
      img.src = 'assets/images/' + post.image || 'assets/images/placeholder.jpg';
      img.alt = post.title;
      img.className = 'w-full aspect-square object-cover';
      card.appendChild(img);

      const info = document.createElement('div');
      info.className = 'p-2';

      const title = document.createElement('h3');
      title.className = 'font-semibold';
      title.textContent = post.title;
      info.appendChild(title);

      const desc = document.createElement('p');
      desc.className = 'text-sm text-gray-600';
      desc.textContent = post.description;
      info.appendChild(desc);

      card.appendChild(info);
      galleryGrid.appendChild(card);

      // Click to enlarge modal with text
      card.addEventListener('click', () => {
        const modal = document.getElementById('image-modal');
        document.getElementById('modal-img').src = img.src;
        document.getElementById('modal-title').textContent = post.title;
        document.getElementById('modal-desc').textContent = post.description;
        modal.classList.remove('hidden');
      });
    });
  } catch (err) {
    console.error('Failed to load gallery:', err);
  }
}

// Close modal
document.getElementById('close-modal').addEventListener('click', () => {
  document.getElementById('image-modal').classList.add('hidden');
});

// Close modal when clicking outside image/text
document.getElementById('image-modal').addEventListener('click', e => {
  if (e.target.id === 'image-modal') {
    e.currentTarget.classList.add('hidden');
  }
});

// Filter gallery
document.getElementById('gallery-search').addEventListener('input', e => {
  const query = e.target.value.toLowerCase();
  document.querySelectorAll('#gallery-grid > div').forEach(card => {
    const title = card.querySelector('h3').textContent.toLowerCase();
    card.classList.toggle('hidden', !title.includes(query));
  });
});

// Load gallery on page load
window.addEventListener('DOMContentLoaded', loadGallery);
</script>

<my-footer></my-footer>
</body>
</html>
