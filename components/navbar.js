class MyNavbar extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <header class="flex justify-between items-center p-4 bg-primaryLight dark:bg-primaryDark text-textLight dark:text-textDark shadow-md">
        <div class="font-black text-lg">EM</div>
        <nav class="space-x-4">
          <a href="/" data-path="/" class="nav-link text-current hover:text-blue-700 dark:hover:text-blue-400 transition-colors duration-200">Home</a>
          <a href="/about.html" data-path="/about.html" class="nav-link text-current hover:text-blue-700 dark:hover:text-blue-400 transition-colors duration-200">About</a>
          <a href="/video.html" data-path="/video.html" class="nav-link text-current hover:text-blue-700 dark:hover:text-blue-400 transition-colors duration-200">Videos</a>
          <a href="/live.html" data-path="/live.html" class="nav-link text-current hover:text-blue-700 dark:hover:text-blue-400 transition-colors duration-200">Live</a>
        </nav>
      </header>
    `;

    const currentPath = window.location.pathname;

    // Mark the matching link as active
    const links = this.querySelectorAll('.nav-link');
    links.forEach(link => {
      const path = link.getAttribute('data-path');
      if (path === currentPath) {
        link.classList.add('border-b-2', 'border-blue-500', 'font-bold');
      }
    });
  }
}

customElements.define('my-navbar', MyNavbar);
