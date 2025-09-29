// components/theme.js

const themes = {
  white: {
    body: "bg-gray-100 text-black",
    main: "bg-white border-gray-400 text-black",
    aside: "bg-white border-gray-400 text-black",
    link: "text-black"
  },
  black: {
    body: "bg-gray-900 text-white",
    main: "bg-gray-800 border-gray-600 text-white",
    aside: "bg-gray-800 border-gray-600 text-white",
    link: "text-white"
  },
  blue: {
    body: "bg-blue-100 text-blue-900",
    main: "bg-blue-50 border-blue-400 text-blue-900",
    aside: "bg-blue-50 border-blue-400 text-blue-900",
    link: "text-blue-900"
  },
};

// --- Cookie helpers ---
function setCookie(name, value, days) {
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + days*24*60*60*1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(name) {
  const nameEQ = name + "=";
  const ca = document.cookie.split(';');
  for(let i=0;i < ca.length;i++) {
    let c = ca[i];
    while(c.charAt(0)==' ') c = c.substring(1,c.length);
    if(c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

// --- Apply theme ---
function applyTheme(theme) {
  const config = themes[theme];
  if (!config) return;

  // Body
  document.body.className = config.body + " font-sans";

  // Main content
  const mainEl = document.querySelector("main");
  if(mainEl) {
    mainEl.className = "md:col-span-2 p-6 border-2 rounded " + config.main;
  }

  // Sidebar
  const sidebarEl = document.getElementById("sidebar");
  if(sidebarEl) {
    sidebarEl.className = "rounded p-4 space-y-6 border-2 " + config.aside;
    // Make links readable
    sidebarEl.querySelectorAll("a").forEach(a => {
      a.className = config.link + " underline";
    });
  }

  // Save in cookie for 30 days
  setCookie("theme", theme, 30);

  // Update any theme indicator
  const status = document.getElementById("theme-status");
  if(status) status.textContent = theme.charAt(0).toUpperCase() + theme.slice(1);
}

// --- Init theme ---
function initTheme() {
  const saved = getCookie("theme") || "white";
  applyTheme(saved);
}

// Expose globally
window.setTheme = applyTheme;

window.addEventListener("DOMContentLoaded", initTheme);
