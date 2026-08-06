import './stimulus_bootstrap.js';
import { StreamActions } from "@hotwired/turbo";

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

StreamActions.show_dialog = function () {
    const dialog = this.targetElements[0]

    dialog.dataset.type = this.getAttribute('type') ?? 'info'

    dialog.replaceChildren(this.templateContent.cloneNode(true))

    if (dialog instanceof HTMLDialogElement && !dialog.open) {
        dialog.showModal()
    }
}
