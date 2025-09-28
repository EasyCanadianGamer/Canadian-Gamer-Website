class AdminContentManager extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="space-y-6">

        <!-- Add Post Section -->
        <div class="border p-4 rounded-md">
          <h2 class="font-semibold mb-2">Add a New Post</h2>
          <input id="post-text" type="text" placeholder="Write something..." class="w-full border px-2 py-1 rounded mb-2">
          <input id="post-image" type="file" accept="image/*" class="w-full border px-2 py-1 rounded mb-2">
          <button id="post-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add Post</button>
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
  }

  // --- POSTS ---
  async addPost() {
    const text = this.querySelector("#post-text").value;
    const file = this.querySelector("#post-image").files[0];

    if (!text) {
      alert("Please enter some text for the post.");
      return;
    }

    let imageUrl = "";

    if (file) {
      // For simplicity, you can use PHP backend like before for storing post images
      const formData = new FormData();
      formData.append("image", file);
      formData.append("text", text);

      const res = await fetch("upload_post.php", { method: "POST", body: formData });
      const data = await res.json();
      if (data.status === "success") {
        imageUrl = data.imageUrl;
      } else {
        alert("Failed to upload image: " + data.message);
        return;
      }
    }

    // Here you can also store posts.json via PHP or DB
    alert("Post added!"); 
    this.querySelector("#post-text").value = "";
    this.querySelector("#post-image").value = "";
  }

  // --- FEATURED AUDIO ---
  async uploadAudio() {
    const file = this.querySelector("#audio-file").files[0];
    if (!file) {
      alert("Please select an audio file.");
      return;
    }

    const formData = new FormData();
    formData.append("audio", file);

    const res = await fetch('audio.php', { method: "POST", body: formData });
    const data = await res.json();
    if (data.status === "success") {
      alert("Featured audio updated!");
      this.querySelector("#audio-file").value = "";
    } else {
      alert("Failed to upload audio: " + data.message);
    }
  }
}

customElements.define("admin-content-manager", AdminContentManager);
