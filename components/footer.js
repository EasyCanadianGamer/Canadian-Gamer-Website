class Footer extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <footer class="bg-blue-100 border-2 border-blue-300 mx-auto my-10 p-5 rounded-md text-center w-full max-w-xl shadow-sm">
        <p class="font-bold text-gray-800">© 2025 Canadian Gamer</p>
        <p class="mt-1">
          <a href="https://bsky.app/profile/canadian-gamer.com" target="_blank" class="text-blue-500 hover:text-blue-400">
            Follow me on BlueSky
          </a>
        </p>
        <p class="mt-2 text-gray-600">
          <marquee scrollamount="3">Thanks for visiting my page! Enjoy the music and content!</marquee>
        </p>
      </footer>
    `;
  }
}

customElements.define('my-footer', Footer);
