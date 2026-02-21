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
    <title>CG-Videos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="themes/blue.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">

    <!-- Sidebar -->
    <aside class="md:col-span-1">
        <div id="sidebar" class="border-2 p-4 space-y-6 rounded">

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
                <audio controls loop class="w-full">
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
                    <li><a href="blogs.php" class="text-blue-600 underline">Writings</a></li>
                </ul>
            </div>

        </div>
    </aside>

    <!-- Main content -->
    <main class="md:col-span-2 p-6">

        <!-- Search Bar -->
        <div class="mb-6">
            <input type="text" placeholder="Search Videos..." id="video-search"
                   class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-4">
                <button class="tab-btn border-b-2 border-blue-600 text-blue-600 px-3 py-2 font-medium text-sm"
                        data-tab="canadiangamer">CanadianGamer
                </button>
                <button class="tab-btn border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-3 py-2 font-medium text-sm"
                        data-tab="eyaduddin">Eyad Uddin
                </button>
            </nav>
        </div>

        <!-- Tab content -->
        <div id="tab-content">
            <div class="tab-panel" data-tab="canadiangamer">
                <div id="canadiangamer-gallery"
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>
            <div class="tab-panel hidden" data-tab="eyaduddin">
                <div id="eyaduddin-gallery"
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>
        </div>

    </main>
</div>

<script>
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');
    const loadedTabs = {};

    function activateTab(target) {
        tabs.forEach(tab => {
            if (tab.dataset.tab === target) {
                tab.classList.remove('border-transparent', 'text-gray-500');
                tab.classList.add('border-blue-600', 'text-blue-600');
            } else {
                tab.classList.remove('border-blue-600', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            }
        });

        panels.forEach(panel => {
            if (panel.dataset.tab === target) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            activateTab(target);
            if (!loadedTabs[target]) {
                loadVideos(target);
                loadedTabs[target] = true;
            }
        });
    });

    async function loadVideos(channel) {
        const galleryId = channel === 'canadiangamer' ? 'canadiangamer-gallery' : 'eyaduddin-gallery';
        const gallery = document.getElementById(galleryId);
        gallery.innerHTML = '';

        try {
            const res = await fetch(`get_videos.php?channel=${channel}`);
            const videos = await res.json();

            videos.forEach(video => {
                const card = document.createElement('div');
                card.className = 'border rounded-lg overflow-hidden shadow-sm';

                const link = document.createElement('a');
                link.href = `https://www.youtube.com/watch?v=${video.videoId}`;
                link.target = '_blank';

                const thumb = document.createElement('img');
                thumb.src = video.thumbnail;
                thumb.alt = video.title;
                thumb.className = 'w-full h-48 object-cover';
                link.appendChild(thumb);
                card.appendChild(link);

                const info = document.createElement('div');
                info.className = 'p-2';

                const title = document.createElement('h3');
                title.className = 'font-semibold';
                title.textContent = video.title;
                info.appendChild(title);

                card.appendChild(info);
                gallery.appendChild(card);
            });
        } catch (err) {
            console.error(`Failed to load ${channel} videos`, err);
        }
    }

    // Load first tab by default
    window.addEventListener('DOMContentLoaded', () => {
        activateTab('canadiangamer');
        loadVideos('canadiangamer');
        loadedTabs['canadiangamer'] = true;
    });
</script>

<script>
const searchInput = document.getElementById('video-search');

searchInput.addEventListener('input', () => {
  const query = searchInput.value.toLowerCase();

  // Filter both galleries (you can also filter only the active one)
  document.querySelectorAll('.tab-panel').forEach(panel => {
    panel.querySelectorAll('h3').forEach(titleEl => {
      const card = titleEl.closest('.border');
      if (titleEl.textContent.toLowerCase().includes(query)) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  });
});
</script>

<my-footer></my-footer>
</body>
</html>
