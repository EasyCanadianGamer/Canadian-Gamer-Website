<?php
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
  <title>Admin Dashboard</title>
  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="themes/black.css">
  <link rel="stylesheet" href="style.css">

</head>

<script type="module" src= "/components/login.js"> </script>
<script type="module" src= "/components/footer.js"> </script>

<body>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">
    
    <!-- Sidebar (same style as public pages) -->
    <aside class="md:col-span-1">
      <div id="sidebar" class=" border-2  p-4 space-y-6">
        <!-- Profile -->
        <div class="text-center">
    <img id="profile-pfp" src="<?= $pfp ?>" alt="Profile Picture" class="rounded-2xl w-full mb-2">
    <h2 class="text-xl font-bold">CanadianGamer</h2>
    <p class="text-sm text-gray-600">Admin Panel</p>

    <!-- Upload button -->
    <form id="upload-pfp-form" class="mt-2" enctype="multipart/form-data">
        <input type="file" name="pfp" accept="image/png, image/jpeg, image/jpg, image/gif" class="hidden" id="pfp-input">
        <button type="button" id="change-pfp-btn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Change Profile Picture
        </button>
    </form>
</div>
        <!-- Quick Links -->
        <div>
          <h3 class="font-bold mb-2 border-b pb-1">Admin Links</h3>
          <ul class="space-y-1">
            <li><a href="index.php" class="text-blue-600 underline">View Site</a></li>
            <li><a href="admin.php" class="text-blue-600 underline">Dashboard</a></li>
            <li><a href="videos.php" class="text-blue-600 underline">Videos</a></li>
            <li><a href="gallery.php" class="text-blue-600 underline">Gallery</a></li>
          </ul>
        </div>
      </div>
    </aside>

    <!-- Main admin content -->
    <main class="md:col-span-2  p-6 ">
      <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

      <div class="space-y-4">
  <admin-content-manager></admin-content-manager>

<!-- Site Customization -->
<!-- <div class="border p-4 rounded-md">
  <h2 class="font-semibold mb-2">Site Theme</h2>
  <div class="flex gap-2">
    <button onclick="setTheme('white')" 
      class="bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 rounded">
      White
    </button>
    <button onclick="setTheme('blue')" 
      class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
      Blue
    </button>
    <button onclick="setTheme('black')" 
      class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
      Black
    </button>
  </div>
  <p class="mt-2 text-sm">Current theme: 
    <span id="theme-status" class="font-bold text-blue-600"></span>
  </p>
</div> -->

      </div>
    </main>
  </div>

<script type="module" src="/components/post.js"></script>
<script>
const btn = document.getElementById('change-pfp-btn');
const input = document.getElementById('pfp-input');
const form = document.getElementById('upload-pfp-form');
const profilePfp = document.getElementById('profile-pfp');

btn.addEventListener('click', () => input.click());

input.addEventListener('change', async () => {
    const file = input.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('pfp', file);

    const response = await fetch('upload_pfp.php', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();
    if (data.status === 'success') {
        // Update the profile image instantly
        profilePfp.src = data.file + '?t=' + new Date().getTime(); // prevent cache
        alert('Profile picture updated!');
    } else {
        alert('Error: ' + data.error);
        console.log(data);
    }
});
</script>
</body>
<my-footer></my-footer>

</html>
