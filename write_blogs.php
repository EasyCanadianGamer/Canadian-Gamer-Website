<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Write Blog</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

  <link rel="stylesheet" href="themes/blue.css">
  <link rel="stylesheet" href="style.css">
  <link rel="alternate" type="application/rss+xml" 
      title="Canadian Gamer RSS Feed" 
      href="https://www.canadian-gamer.com/rss.php">

</head>

<script type="module" src="/components/footer.js"></script>
<script type="module" src="/components/login.js"></script>

<body class="bg-gray-100 text-gray-900">

  <!-- Wrapper grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 max-w-6xl mx-auto">
    
    <!-- Sidebar -->
    <aside class="md:col-span-1">
      <div id="sidebar" class="border-2 p-4 space-y-6 rounded bg-white">
        <h2 class="font-bold text-lg">Admin Panel</h2>
        <p>Welcome, <span class="font-semibold"><?= htmlspecialchars($_SESSION['username']); ?></span></p>
        <ul class="space-y-1">
          <li><a href="index.php" class="text-blue-600 underline">Home</a></li>
          <li><a href="blogs.php" class="text-blue-600 underline">View Blogs</a></li>
        </ul>
      </div>
    </aside>
<!-- Main content -->
<main class="md:col-span-2 p-6 bg-white border-2 rounded">
  <h1 class="text-2xl font-bold mb-4">Write a New Blog</h1>

  <form id="blog-form" class="space-y-4">
    <div>
      <label class="block mb-1 font-medium">Title</label>
      <input type="text" name="title" required class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block mb-1 font-medium">Content</label>
      <!-- Replace textarea with Quill editor -->
      <div id="editor" class="h-64 border rounded bg-white"></div>
      <!-- Hidden field to submit HTML -->
      <input type="hidden" name="content" id="content-input">
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
      Publish Blog
    </button>
  </form>

  <p id="blog-msg" class="mt-4 text-center text-sm hidden"></p>



    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
    <a href="edit_blogs.php" class="text-blue-600 underline">Edit/Manage Blogs</a>    </button>
  
</main>
  </div>

  <my-footer></my-footer>
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
  // Init editor
  const quill = new Quill('#editor', {
    theme: 'snow',
    placeholder: 'Write your blog here...',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'header': [1, 2, 3, false] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['link', 'image'],
        ['clean']
      ]
    }
  });

  // On form submit, copy HTML content into hidden input
  document.getElementById('blog-form').addEventListener('submit', function(e) {
    document.getElementById('content-input').value = quill.root.innerHTML;
  });
</script>

  <script>
    const form = document.getElementById("blog-form");
    const msg = document.getElementById("blog-msg");

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      try {
        const res = await fetch("save_blog.php", {
          method: "POST",
          body: formData,
          credentials: "include"
        });
        const data = await res.json();

        if (data.status === "success") {
          msg.textContent = "✅ Blog published!";
          msg.className = "mt-4 text-center text-green-600";
          form.reset();
        } else {
          msg.textContent = data.message || "❌ Error saving blog.";
          msg.className = "mt-4 text-center text-red-600";
        }
        msg.classList.remove("hidden");
      } catch (err) {
        msg.textContent = "⚠️ Network error.";
        msg.className = "mt-4 text-center text-red-600";
        msg.classList.remove("hidden");
      }
    });
  </script>
</body>
</html>
