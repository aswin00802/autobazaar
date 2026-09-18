/**
 * Image gallery on the vehicle detail page: main image, up to five thumbnails
 * with a "+N photos" tile, keyboard arrows, share and a wishlist heart.
 *
 * Wishlist is UI-only for now — there is no favourites table for catalogue
 * models yet (the existing favourites API is for used-auto posts).
 */
const THUMBS = 5;

export default (config = {}) => ({
    images: config.images ?? [],
    name: config.name ?? '',
    url: config.url ?? window.location.href,
    active: 0,
    copied: false,
    wished: false,

    get current() {
        return this.images[this.active] ?? this.images[0] ?? '';
    },

    get thumbs() {
        return this.images.slice(0, THUMBS);
    },

    /** Photos beyond the thumbnail strip — shown as "+N" on the last tile. */
    get extra() {
        return Math.max(0, this.images.length - THUMBS);
    },

    isLastThumb(index) {
        return this.extra > 0 && index === THUMBS - 1;
    },

    show(index) {
        if (!this.images.length) return;
        this.active = (index + this.images.length) % this.images.length;
    },

    prev() {
        this.show(this.active - 1);
    },

    next() {
        this.show(this.active + 1);
    },

    async share() {
        const data = { title: this.name, text: `${this.name} — on-road price, EMI and offers on AutoBazaar`, url: this.url };

        try {
            if (navigator.share) {
                await navigator.share(data);
                return;
            }
            await navigator.clipboard.writeText(this.url);
            this.flashCopied();
        } catch (e) {
            // User dismissed the share sheet, or clipboard is blocked — fall back to a plain prompt.
            if (e?.name === 'AbortError') return;
            try {
                await navigator.clipboard.writeText(this.url);
                this.flashCopied();
            } catch {
                window.prompt('Copy this link', this.url);
            }
        }
    },

    flashCopied() {
        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
    },

    toggleWish() {
        this.wished = !this.wished;
    },
});
