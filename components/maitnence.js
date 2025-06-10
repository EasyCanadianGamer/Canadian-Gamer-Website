class Maintenance extends HTMLElement {
    connectedCallback() {
      this.innerHTML = `
      
         <header id = "maintenance ">
        <h1 class = "lato-black">🚧 Maintenance Mode</h1>
        <p class = "lato-regular">We are currently updating the site. Please check back later.</p>
      </header>
      `;
    }
  }
  
  customElements.define('maintenance-mode', Maintenance);
  