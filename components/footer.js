class Footer extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <footer class="bg-primaryLight dark:bg-primaryDark text-center p-4 text-sm">
        <p class="text-sm">
          © 2025 Canadian Gamer |
          <a
            target="_blank"
            rel="noopener noreferrer"
            href="https://bsky.app/profile/canadian-gamer.com"
            class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
            aria-label="Follow Canadian Gamer on BlueSky"
          >
            Follow me on BlueSky
          </a>
        </p>
      </footer>
    `;
  }
}

customElements.define('my-footer', Footer);
