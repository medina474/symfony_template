import { Controller } from '@hotwired/stimulus'

export default class extends Controller {

    static targets = [
        'sidebar',
        'content',
        'label',
        'submenu',
        'chevron'
    ]

    connect() {

        this.collapsed = false

        const saved = localStorage.getItem('sidebar-collapsed')

        if (saved === 'true') {
            this.collapse()
        }
    }

    toggle() {

        this.collapsed
            ? this.expand()
            : this.collapse()

        localStorage.setItem(
            'sidebar-collapsed',
            this.collapsed
        )
    }

    collapse() {

        this.sidebarTarget.classList.remove('w-60')
        this.sidebarTarget.classList.add('w-16')

        this.contentTarget.classList.remove('ml-60')
        this.contentTarget.classList.add('ml-16')

        this.labelTargets.forEach(label => {
            label.classList.add('hidden')
        })

        this.collapsed = true
    }

    expand() {

        this.sidebarTarget.classList.remove('w-16')
        this.sidebarTarget.classList.add('w-60')

        this.contentTarget.classList.remove('ml-16')
        this.contentTarget.classList.add('ml-60')

        this.labelTargets.forEach(label => {
            label.classList.remove('hidden')
        })

        this.collapsed = false
    }

    toggleGroup(event) {
        const trigger = event.currentTarget
        const group = trigger.closest("[data-group]")
        const isOpen = group.classList.contains("sidebar__group--open")

        // Ferme tous les groupes ouverts
        this.element.querySelectorAll("[data-group]").forEach(g => {
            g.classList.remove("sidebar__group--open")
        });

        if (!isOpen) {
            this._openGroup(group)
            sessionStorage.setItem("sidebar-open-group", group.dataset.group)
        } else {
            sessionStorage.removeItem("sidebar-open-group")
        }
    }

    _openGroup(group) {
        group.classList.add("sidebar__group--open");
    }
}
