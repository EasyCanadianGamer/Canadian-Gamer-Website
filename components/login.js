export default class AdminLogin extends HTMLElement {
    connectedCallback() {
      this.innerHTML = `
        <div id="login-wrapper" class="fixed inset-0 flex items-center justify-center bg-black/70 z-50">
          <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-black">
            <h2 class="text-xl font-bold mb-4 text-center" id="form-title">Admin Login</h2>
  
            <form id="login-form" class="space-y-4">
              <div>
                <label class="block mb-1 font-medium">Username</label>
                <input type="text" name="username" required class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block mb-1 font-medium">PIN</label>
                <input type="password" name="pin" required minlength="4" maxlength="6" inputmode="numeric" class="w-full border rounded px-3 py-2">
              </div>
              <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700" id="submit-btn">
                Login
              </button>
            </form>
  
            <p id="login-msg" class="text-red-600 text-sm mt-2 text-center hidden"></p>
  
            <p class="text-center mt-4 text-sm">
              <span id="toggle-text">Don’t have an account?</span>
              <button id="toggle-btn" type="button" class="text-blue-600 hover:underline">Register</button>
            </p>
          </div>
        </div>
      `;
  
      // explicitly grab elements
      this.form = this.querySelector("#login-form");
      this.msgEl = this.querySelector("#login-msg");
      this.titleEl = this.querySelector("#form-title");
      this.submitBtn = this.querySelector("#submit-btn");
      this.toggleBtn = this.querySelector("#toggle-btn");
      this.toggleText = this.querySelector("#toggle-text");
  
      this.mode = "login";
  
      // submit handler
      this.form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(this.form);
        formData.append("action", this.mode);
  
        try {
          const res = await fetch("login.php", {
            method: "POST",
            body: formData,
            credentials: "include"
          });
          const data = await res.json();
  
          if (data.status === "success") {
            if (this.mode === "register") {
              this.msgEl.textContent = "Account created. You can log in now.";
              this.msgEl.classList.remove("hidden");
              this.switchMode("login");
            } else {
              this.remove();
              location.reload();
            }
          } else {
            this.msgEl.textContent = data.message || "Error.";
            this.msgEl.classList.remove("hidden");
          }
        } catch (err) {
          this.msgEl.textContent = "Network error. Try again.";
          this.msgEl.classList.remove("hidden");
        }
      });
  
      // toggle mode
      this.toggleBtn.addEventListener("click", () => {
        this.switchMode(this.mode === "login" ? "register" : "login");
      });
    }
  
    switchMode(mode) {
      this.mode = mode;
      if (mode === "register") {
        this.titleEl.textContent = "Register Admin";
        this.submitBtn.textContent = "Register";
        this.toggleText.textContent = "Already have an account?";
        this.toggleBtn.textContent = "Login";
      } else {
        this.titleEl.textContent = "Admin Login";
        this.submitBtn.textContent = "Login";
        this.toggleText.textContent = "Don’t have an account?";
        this.toggleBtn.textContent = "Register";
      }
      this.msgEl.classList.add("hidden");
    }
  }
  
  customElements.define("admin-login", AdminLogin);
  
  // check login status
  document.addEventListener("DOMContentLoaded", async () => {
    try {
      const res = await fetch("login.php?action=status", { credentials: "include" });
      const data = await res.json();
      if (!data.logged_in) {
        document.body.insertAdjacentHTML("beforeend", `<admin-login></admin-login>`);
      }
    } catch (err) {
      console.error("Login check failed", err);
      document.body.insertAdjacentHTML("beforeend", `<admin-login></admin-login>`);
    }
  });
  