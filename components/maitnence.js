class Maintenance extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <section id="maintenance" aria-live="polite" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 max-w-xl mx-auto mt-10 rounded-lg shadow">
        <h1 class="text-2xl font-bold flex items-center gap-2">
          <span role="img" aria-label="construction sign">🚧</span> Maintenance Mode
        </h1>
        <p class="mt-2 text-base">
          We are currently updating the site. Please check back later.
        </p>
      </section>
    `;
  }
}

customElements.define('maintenance-mode', Maintenance);
