/**
 * Header cart badge.
 *
 * A tiny store rather than per-component state so every "Add to Cart" button
 * on the page updates the same badge in the header.
 */
export default {
    count: 0,

    init() {
        // Seeded server-side on first paint; this is only a fallback.
        this.count = Number(document.body.dataset.cartCount ?? 0);
    },

    set(n) {
        this.count = Number(n) || 0;
    },
};
