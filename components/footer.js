class Footer extends HTMLElement {
    connectedCallback() {
      this.innerHTML = `
    <footer>
        <p>© 2025 Canadian Gamer | Follow me on <a target="_blank" rel="noopener noreferrer" href="https://bsky.app/profile/canadian-gamer.com">BlueSky</a></p>
    </footer>
      `;
    }
  }
  
  customElements.define('my-footer', Footer);
  