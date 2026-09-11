/**
 * Shared comparison state.
 *
 * This is a *store*, not an Alpine.data component, because three separate
 * places on a page need the same list: the "Add to Compare" button on every
 * vehicle card, the sticky tray at the foot of the page, and the WhatsApp
 * button that has to move out of the tray's way. Per-component state would
 * leave them out of sync — a card could write to localStorage without the
 * tray ever knowing.
 *
 * localStorage can throw outright (private mode, blocked site data), so every
 * read and write is guarded and the store degrades to in-memory state.
 */
const KEY = 'ab.compare';
const MAX = 4;

export default {
    items: [],
    max: MAX,
    baseUrl: '/compare',

    init() {
        this.items = this.read();
    },

    has(slug) {
        return this.items.some((item) => item.slug === slug);
    },

    get count() {
        return this.items.length;
    },

    get isFull() {
        return this.items.length >= MAX;
    },

    toggle(model) {
        this.has(model.slug) ? this.remove(model.slug) : this.add(model);
    },

    add(model) {
        if (this.has(model.slug) || this.isFull) return;

        this.items.push(model);
        this.write();
    },

    remove(slug) {
        this.items = this.items.filter((item) => item.slug !== slug);
        this.write();
    },

    clear() {
        this.items = [];
        this.write();
    },

    /** Empty slots remaining, so the tray can show the "up to 4" limit. */
    get emptySlots() {
        return Math.max(0, MAX - this.items.length);
    },

    /** /compare/tvs-king-deluxe-vs-bajaj-re — null until 2 are picked. */
    get compareUrl() {
        if (this.items.length < 2) return null;
        return `${this.baseUrl}/${this.items.map((i) => i.slug).join('-vs-')}`;
    },

    read() {
        try {
            const raw = window.localStorage.getItem(KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed.slice(0, MAX) : [];
        } catch {
            return [];
        }
    },

    write() {
        try {
            window.localStorage.setItem(KEY, JSON.stringify(this.items));
        } catch {
            /* Storage unavailable — the tray still works for this page view. */
        }
    },
};
