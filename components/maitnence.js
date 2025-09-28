class Maintenance extends HTMLElement {
  connectedCallback() {
    this.checkState();
  }

  checkState() {
    const enabled = localStorage.getItem("maintenance") === "true";
    this.render(enabled);

    // Show/hide <main>
    const mainContent = document.querySelector("main");
    if (mainContent) {
      mainContent.style.display = enabled ? "none" : "block";
    }
  }

  render(enabled) {
    if (enabled) {
      this.innerHTML = `
        <header id="maintenance" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded">
          <h1 class="font-bold text-lg">🚧 Maintenance Mode</h1>
          <p>We are currently updating the site. Please check back later.</p>
        </header>
      `;
    } else {
      this.innerHTML = ""; // Hide banner
    }
  }
}

customElements.define("maintenance-mode", Maintenance);

// Global function to toggle mode
window.toggleMaintenance = function () {
  const current = localStorage.getItem("maintenance") === "true";
  const newValue = !current;
  localStorage.setItem("maintenance", newValue);

  // Re-render all <maintenance-mode> elements
  document.querySelectorAll("maintenance-mode").forEach((el) =>
    el.checkState()
  );

  // Optional: update status text if exists
  const status = document.getElementById("maintenance-status");
  if (status) {
    status.textContent = newValue ? "ON" : "OFF";
  }
};
