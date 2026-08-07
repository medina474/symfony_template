import { Controller } from '@hotwired/stimulus'
import * as Turbo from '@hotwired/turbo'

export default class extends Controller {

    static targets = [
        'sentinel'
    ]

    connect() {

        this.loading = false

        this.observer = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadMore()
                    }
                });
            },
            {
                root: this.element,
                rootMargin: '0px 0px 600px 0px'
            }
        );

        if (this.hasSentinelTarget) {
            this.observer.observe(
                this.sentinelTarget
            )
        }
    }

    disconnect() {
        if (this.observer && this.sentinelTarget) {
            this.observer.unobserve(this.sentinelTarget)
        }
    }

    async loadMore() {

        if (this.loading) {
            console.warn("chargement en cours")
            return
        }

        const link = this.sentinelTarget.querySelector('a')
        
        if (!link || !link.href) {
            console.log("plus de page suivante : on arrête d'observer")
            //this.observer.unobserve(this.sentinelTarget)
            this.loading = false
            return
        }

        this.loading = true

        const response = await fetch(
            link.href,
            {
                headers: {
                    Accept:
                        'text/vnd.turbo-stream.html'
                }
            }
        )

        const html = await response.text()
        Turbo.renderStreamMessage(html);

        this.loading = false;

        // Force une nouvelle vérification d'intersection
        this.observer.unobserve(this.sentinelTarget)
        this.observer.observe(this.sentinelTarget)
    }
}
