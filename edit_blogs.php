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
<title>Edit Blogs</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        </ul>
      </div>
    </div>
  </aside>

  <!-- Main content -->
  <main class="md:col-span-2 p-6 bg-white border-2 rounded">
    <h1 class="text-2xl font-bold mb-4">Edit Blogs</h1>
    <div id="blogs-container" class="space-y-6">
      <!-- Blogs list will be loaded here -->
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
      <div class="bg-white p-6 rounded w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Edit Blog</h2>
        <form id="edit-form" class="space-y-4">
          <input type="hidden" name="id">
          <div>
            <label class="block mb-1 font-medium">Title</label>
            <input type="text" name="title" required class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block mb-1 font-medium">Content</label>
            <div id="editor" class="h-64 bg-white"></div>
            <input type="hidden" name="content">          </div>
          <div class="flex justify-end gap-2">
            <button type="button" id="close-modal" class="px-4 py-2 border rounded">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
          </div>
        </form>
      </div>
    </div>

  </main>
</div>

<my-footer></my-footer>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
  // Initialize Quill editor
  const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        ['link', 'image'],
        ['clean']
      ]
    }
  });

  // Load blogs and populate the list
  async function loadBlogs() {
    try {
      const res = await fetch('get_blogs.php');
      const blogs = await res.json();
      const container = document.getElementById('blogs-container');
      container.innerHTML = '';

      blogs.forEach(blog => {
        const div = document.createElement('div');
        div.className = 'border p-4 rounded bg-gray-50';
        div.innerHTML = `
          <h2 class="text-xl font-bold mb-2">${blog.title}</h2>
          <div class="text-gray-800 mb-2">${blog.content}</div>
          <p class="text-sm text-gray-500 mb-2">Posted on: ${new Date(blog.created_at).toLocaleString()}</p>
          <div class="flex gap-2">
            <button data-id="${blog.id}" class="edit-btn px-2 py-1 bg-yellow-400 rounded hover:bg-yellow-500">Edit</button>
            <button data-id="${blog.id}" class="delete-btn px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
          </div>
        `;
        container.appendChild(div);
      });

      // Attach event listeners
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => openEditModal(btn.dataset.id, blogs));
      });
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => deleteBlog(btn.dataset.id));
      });

    } catch (err) {
      console.error('Failed to load blogs', err);
    }
  }

  // Open edit modal with Quill editor populated
  function openEditModal(id, blogs) {
    const blog = blogs.find(b => b.id == id);
    const modal = document.getElementById('edit-modal');
    const form = document.getElementById('edit-form');

    form.id.value = blog.id;
    form.title.value = blog.title;
    quill.root.innerHTML = blog.content; // Populate editor with rich text content

    modal.classList.remove('hidden');
  }

  // Close modal
  document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('edit-modal').classList.add('hidden');
  });

  // Save changes from Quill editor
  document.getElementById('edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    form.content.value = quill.root.innerHTML; // Copy rich content to hidden input
    const data = new FormData(form);

    try {
      const res = await fetch('edit_blogs_backend.php', {
        method: 'POST',
        body: data
      });
      const result = await res.json();
      if (result.status === 'success') {
        loadBlogs();
        document.getElementById('edit-modal').classList.add('hidden');
      } else {
        alert(result.message || 'Error saving blog');
      }
    } catch (err) {
      console.error('Save failed', err);
    }
  });

  // Delete blog
  async function deleteBlog(id) {
    if (!confirm('Are you sure you want to delete this blog?')) return;
    try {
      const res = await fetch('edit_blogs_backend.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'delete', id })
      });
      const result = await res.json();
      if (result.status === 'success') {
        loadBlogs();
      } else {
        alert(result.message || 'Error deleting blog');
      }
    } catch (err) {
      console.error('Delete failed', err);
    }
  }

  document.addEventListener('DOMContentLoaded', loadBlogs);
</script>



</body>
</html>
