class AdminPostForm extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="border p-4 rounded-md">
        <h2 class="font-semibold mb-2">Add a New Post</h2>
        <input id="post-text" type="text" placeholder="Write something..." class="w-full border px-2 py-1 rounded mb-2">
        <input id="post-image" type="file" accept="image/*" class="w-full border px-2 py-1 rounded mb-2">
        <button id="post-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add Post</button>
      </div>
    `;

    this.querySelector("#post-btn").addEventListener("click", () => this.addPost());
  }

  async addPost() {
    const text = this.querySelector("#post-text").value;
    const file = this.querySelector("#post-image").files[0];

    if (!text) {
      alert("Please enter some text for the post.");
      return;
    }

    let imageUrl = "";

    // WebDAV settings
    const WEBDAV_BASE = "https://nextcloud.canadian-gamer.com/remote.php/webdav/Posts/";
    const USER = "CanadianGamer";
    const PASSWORD = "NCBeta2023!";

    if (file) {
      try {
        const uploadRes = await fetch(WEBDAV_BASE + encodeURIComponent(file.name), {
          method: "PUT",
          headers: {
            "Authorization": "Basic " + btoa(USER + ":" + PASSWORD),
          },
          body: file
        });

        if (!uploadRes.ok) {
          alert("Failed to upload image: " + uploadRes.status);
          return;
        }

        // Set the public URL to the uploaded file
        imageUrl = `https://nextcloud.canadian-gamer.com/remote.php/webdav/Posts/${encodeURIComponent(file.name)}`;
      } catch (err) {
        console.error(err);
        alert("Error uploading image. See console.");
        return;
      }
    }

    // Fetch current posts
    const postsUrl = WEBDAV_BASE + "posts.json";
    let posts = [];
    try {
      const res = await fetch(postsUrl, {
        headers: { "Authorization": "Basic " + btoa(USER + ":" + PASSWORD) }
      });
      if (res.ok) posts = await res.json();
    } catch {}

    // Add new post
    posts.push({
      text,
      image: imageUrl,
      timestamp: new Date().toISOString()
    });

    // Save updated posts
    try {
      const putRes = await fetch(postsUrl, {
        method: "PUT",
        headers: {
          "Authorization": "Basic " + btoa(USER + ":" + PASSWORD),
          "Content-Type": "application/json"
        },
        body: JSON.stringify(posts, null, 2)
      });

      if (putRes.ok) {
        alert("Post added!");
        this.querySelector("#post-text").value = "";
        this.querySelector("#post-image").value = "";
      } else {
        alert("Failed to add post: " + putRes.status);
      }
    } catch (err) {
      console.error(err);
      alert("Error saving post. See console.");
    }
  }
}

customElements.define("admin-post-form", AdminPostForm);
