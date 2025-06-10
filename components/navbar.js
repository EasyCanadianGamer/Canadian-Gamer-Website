class MyNavbar extends HTMLElement {
    connectedCallback() {
      this.innerHTML = `
  
        <header class="header-bg navbar">
          <div class="logo lato-black">EM</div>
          <div class="nav-links lato-regular">
            <sl-button variant="text" href="/" data-path="/">Home</sl-button>
            <sl-button variant="text" href="/about.html" data-path="/about.html">About</sl-button>
            <sl-button variant="text" href="/video.html" data-path="/video.html">Videos</sl-button>
            <sl-button variant="text" href="/live.html" data-path="/live.html">Live</sl-button>

          </div>
        </header>
      `;
  
      const currentPath = window.location.pathname;
  
      // Mark the matching link as active
      const links = this.querySelectorAll('sl-button');
      links.forEach(link => {
        const path = link.getAttribute('data-path');
        if (path === currentPath) {
          link.classList.add('active');
        }
      });
    }
  }
  
  customElements.define('my-navbar', MyNavbar);
  