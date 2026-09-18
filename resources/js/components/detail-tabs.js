/**
 * Sticky section tabs on the vehicle detail page.
 *
 * Tabs scroll to their section (they never show/hide content — everything is
 * on the page for search engines and for printing). The active tab follows
 * the scroll position: the last section whose top has crossed the line just
 * below the tab bar wins.
 *
 * The bar sits under the sticky site header, whose height differs between
 * mobile (one row) and desktop (nav row too), so the offset is measured
 * rather than hard-coded.
 */
export default (config = {}) => ({
    /** [{ id, label, icon }] — ids are section element ids */
    tabs: config.tabs ?? [],
    active: config.tabs?.[0]?.id ?? '',
    top: 0,
    offset: 0,
    ticking: false,

    init() {
        this.measure();
        window.addEventListener('resize', () => this.measure(), { passive: true });
        window.addEventListener('scroll', () => this.onScroll(), { passive: true });
        // Fonts/images shift layout after first paint — measure again once they settle.
        window.addEventListener('load', () => this.measure());
        this.onScroll();
    },

    measure() {
        const header = document.querySelector('header');
        this.top = header ? Math.round(header.getBoundingClientRect().height) : 0;
        this.offset = this.top + this.$el.offsetHeight + 12;

        // Native anchor jumps and scrollIntoView both respect scroll-margin-top.
        this.tabs.forEach((tab) => {
            const section = document.getElementById(tab.id);
            if (section) section.style.scrollMarginTop = `${this.offset}px`;
        });
    },

    onScroll() {
        if (this.ticking) return;
        this.ticking = true;

        requestAnimationFrame(() => {
            this.ticking = false;

            const line = this.offset + 4;
            const positioned = this.tabs
                .map((tab) => {
                    const el = document.getElementById(tab.id);
                    return el ? { id: tab.id, top: el.getBoundingClientRect().top } : null;
                })
                .filter(Boolean)
                .sort((a, b) => a.top - b.top);

            if (!positioned.length) return;

            // At the very bottom the last section may never reach the line.
            const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2;
            if (atBottom) {
                this.active = positioned[positioned.length - 1].id;
                return;
            }

            let current = positioned[0].id;
            for (const section of positioned) {
                if (section.top <= line) current = section.id;
            }
            this.active = current;
        });
    },

    go(id) {
        const section = document.getElementById(id);
        if (!section) return;
        this.active = id;
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    isActive(id) {
        return this.active === id;
    },
});
