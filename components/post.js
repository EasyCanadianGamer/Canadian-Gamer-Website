class AdminContentManager extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="space-y-6">

        <!-- Add Post Section -->
        <div class="border p-4 rounded-md">
          <h2 class="font-semibold mb-2">Add a New Post</h2>
          <input id="post-title" type="text" placeholder="Image Title" class="w-full border px-2 py-1 rounded mb-2">
          <textarea id="post-description" placeholder="Short description" class="w-full border px-2 py-1 rounded mb-2"></textarea>
          <input id="post-image" type="file" accept="image/*" class="w-full border px-2 py-1 rounded mb-2">
          <button id="post-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add Post</button>
        </div>

        <!-- Existing Posts -->
        <div>
          <h2 class="font-semibold mb-2">Manage Posts</h2>
          <div id="admin-posts" class="space-y-4"></div>
        </div>

        <!-- Featured Audio Section -->
        <div class="border p-4 rounded-md">
          <h2 class="font-semibold mb-2">Update Featured Music</h2>
          <input id="audio-file" type="file" accept="audio/*" class="w-full border px-2 py-1 rounded mb-2">
          <button id="audio-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Upload New Audio</button>
        </div>

      </div>
    `;

    this.querySelector("#post-btn").addEventListener("click", () => this.addPost());
    this.querySelector("#audio-btn").addEventListener("click", () => this.uploadAudio());

    this.loadPosts();
  }

  async loadPosts() {
    const container = this.querySelector("#admin-posts");
    container.innerHTML = '';
    try {
      const res = await fetch("admin-post.php");
      const posts = await res.json();

      posts.forEach(post => {
        const card = document.createElement("div");
        card.className = "border rounded-lg p-2 relative shadow-sm";

        // Image
        const img = document.createElement("img");
        img.src = "assets/images/" + (post.image || "placeholder.jpg");
        img.alt = post.title;
        img.className = "w-full h-48 object-cover mb-2";
        card.appendChild(img);

        // New image input for edit
        const imgInput = document.createElement("input");
        imgInput.type = "file";
        imgInput.accept = "image/*";
        imgInput.className = "w-full border px-2 py-1 mb-1";
        card.appendChild(imgInput);

        // Title input
        const titleInput = document.createElement("input");
        titleInput.type = "text";
        titleInput.value = post.title;
        titleInput.className = "w-full border px-2 py-1 mb-1";
        card.appendChild(titleInput);

        // Description textarea
        const descInput = document.createElement("textarea");
        descInput.value = post.description;
        descInput.className = "w-full border px-2 py-1 mb-1";
        card.appendChild(descInput);

        // Save button
        const saveBtn = document.createElement("button");
        saveBtn.textContent = "Save";
        saveBtn.className = "bg-green-500 text-white px-2 py-1 mr-2 rounded";
        card.appendChild(saveBtn);

        // Delete button
        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.className = "bg-red-500 text-white px-2 py-1 rounded";
        card.appendChild(deleteBtn);

        // --- SAVE ---
        saveBtn.addEventListener("click", async () => {
          const formData = new FormData();
          formData.append("type", "edit");
          formData.append("id", post.id);
          formData.append("title", titleInput.value);
          formData.append("description", descInput.value);

          if (imgInput.files[0]) formData.append("image", imgInput.files[0]);

          try {
            const res = await fetch("admin-post.php", { method: "POST", body: formData });
            const data = await res.json();
            if (data.status === "success") {
              alert("Post updated!");
              this.loadPosts(); // reload to show new image
            } else {
              alert("Failed to update post: " + (data.debug?.error || "Unknown error"));
            }
          } catch (err) {
            alert("Failed to update post: " + err.message);
          }
        });

        // --- DELETE ---
        deleteBtn.addEventListener("click", async () => {
          if (!confirm("Are you sure you want to delete this post?")) return;
          const formData = new FormData();
          formData.append("type", "delete");
          formData.append("id", post.id);

          try {
            const res = await fetch("admin-post.php", { method: "POST", body: formData });
            const data = await res.json();
            if (data.status === "success") {
              card.remove();
              alert("Post deleted!");
            } else {
              alert("Failed to delete post: " + (data.debug?.error || "Unknown error"));
            }
          } catch (err) {
            alert("Failed to delete post: " + err.message);
          }
        });

        container.appendChild(card);
      });
    } catch (err) {
      console.error("Failed to load posts:", err);
    }
  }

  async addPost() {
    const title = this.querySelector("#post-title").value.trim();
    const description = this.querySelector("#post-description").value.trim();
    const file = this.querySelector("#post-image").files[0];

    if (!title || !description) {
      alert("Please enter a title and description for the post.");
      return;
    }

    const formData = new FormData();
    formData.append("type", "post");
    formData.append("title", title);
    formData.append("description", description);
    if (file) formData.append("image", file);

    try {
      const res = await fetch("admin-post.php", { method: "POST", body: formData });
      const data = await res.json();
      if (data.status === "success") {
        alert("Post added!");
        this.querySelector("#post-title").value = "";
        this.querySelector("#post-description").value = "";
        this.querySelector("#post-image").value = "";
        this.loadPosts(); // refresh list
      } else {
        alert("Failed to add post: " + (data.debug?.error || "Unknown error"));
      }
    } catch (err) {
      alert("Failed to add post: " + err.message);
    }
  }










  
  async uploadAudio() {
    const file = this.querySelector("#audio-file").files[0];
    if (!file) {
      alert("Please select an audio file.");
      return;
    }

    const formData = new FormData();
    formData.append("audio", file);

    try {
      const res = await fetch('audio.php', { method: "POST", body: formData });
      const data = await res.json();
      if (data.status === "success") {
        alert("Featured audio updated!");
        this.querySelector("#audio-file").value = "";
      } else {
        alert("Failed to upload audio: " + (data.message || JSON.stringify(data)));
      }
    } catch (err) {
      alert("Upload failed: " + err.message);
    }
  }
}

customElements.define("admin-content-manager", AdminContentManager);
