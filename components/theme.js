// components/theme.js
const themes = {
  white: {
    body: "bg-gray-100 text-black",
    main: "bg-white border-gray-400 text-black",
  },
  black: {
    body: "bg-gray-900 text-white",
    main: "bg-gray-800 border-gray-600 text-white",
  },
  blue: {
    body: "bg-blue-100 text-blue-900",
    main: "bg-blue-50 border-blue-400 text-blue-900",
  },
};

function applyTheme(theme) {
  const config = themes[theme];
  if (!config) return;

  // Apply theme classes
  document.body.className = config.body + " font-sans";
  document.querySelector("main")?.setAttribute(
    "class",
    "md:col-span-2 p-6 border-2 rounded " + config.main
  );

  // Save preference
  localStorage.setItem("theme", theme);

  // Update admin status if present
  const status = document.getElementById("theme-status");
  if (status) {
    status.textContent = theme.charAt(0).toUpperCase() + theme.slice(1);
  }
}

function initTheme() {
  const saved = localStorage.getItem("theme") || "white";
  applyTheme(saved); // Apply theme
}

// Expose globally so admin buttons can call it
window.setTheme = applyTheme;

// Initialize on page load
window.addEventListener("DOMContentLoaded", initTheme);
