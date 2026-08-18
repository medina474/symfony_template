import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["overlay", "panel", "frame"]

  connect() {
    this.panelTarget.classList.add("translate-x-full");
    // Appel à l'aide d'un turbo stream action, qui redirige l'action ici app.js
    window.addEventListener("drawer:close", () => this.close());
  }

  open() {
    this.#toggle(true)
  }

  close() {
    this.#toggle(false)
  }

  loading(evt) {

    if (!this.hasFrameTarget) {
        console.log("Il n'y a pas de target !")
        return;
    }

    if (evt.target.id !== 'drawer') {
        return;
    }

    this.frameTarget.innerHTML = `
      <div class="flex h-screen items-center justify-center p-6 text-center text-gray-500 dark:text-gray-300">
        <div>
          <div class="mx-auto mb-3 h-16 w-16 animate-spin rounded-full border-4 border-gray-200 border-t-green-700 dark:border-gray-700 dark:border-t-green-400"></div>
          <p>Chargement...</p>
        </div>
      </div>
    `
  }

  closeOnKeydown({ key }) {
    if (key === "Escape") this.close()
  }

  #toggle(open) {
    this.panelTarget.classList.toggle("translate-x-full", !open)
    this.overlayTarget.classList.toggle("opacity-0", !open)
    this.overlayTarget.classList.toggle("pointer-events-none", !open)
    document.body.style.overflow = open ? "hidden" : ""
  }
}
