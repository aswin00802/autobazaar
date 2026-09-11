/**
 * Left filter rail shared by the vehicle grids, /compare and the accessories
 * shop. Filtering runs client-side against the fixtures the page ships, so the
 * prototype responds instantly with no backend.
 */
export default (config = {}) => ({
    /** Every filterable record, as rendered by the server. */
    items: config.items ?? [],

    /** { group: [selected values] } */
    selected: {},

    minPrice: config.minPrice ?? 0,
    maxPrice: config.maxPrice ?? 500000,
    priceFloor: config.minPrice ?? 0,
    priceCeiling: config.maxPrice ?? 500000,

    sort: config.sort ?? 'popularity',
    mobileOpen: false,

    init() {
        (config.groups ?? []).forEach((group) => {
            this.selected[group] = [];
        });

        // Pre-tick whatever the mockup shows as already checked.
        Object.entries(config.preselect ?? {}).forEach(([group, values]) => {
            this.selected[group] = [...values];
        });
    },

    isChecked(group, value) {
        return (this.selected[group] ?? []).includes(value);
    },

    toggle(group, value) {
        this.selected[group] ??= [];

        const index = this.selected[group].indexOf(value);
        index === -1
            ? this.selected[group].push(value)
            : this.selected[group].splice(index, 1);
    },

    clearAll() {
        Object.keys(this.selected).forEach((group) => {
            this.selected[group] = [];
        });

        this.minPrice = this.priceFloor;
        this.maxPrice = this.priceCeiling;
    },

    get activeCount() {
        const chosen = Object.values(this.selected).reduce((sum, v) => sum + v.length, 0);
        const priceTouched =
            this.minPrice !== this.priceFloor || this.maxPrice !== this.priceCeiling;

        return chosen + (priceTouched ? 1 : 0);
    },

    matches(item) {
        const price = Number(item.price ?? 0);
        if (price < this.minPrice || price > this.maxPrice) return false;

        return Object.entries(this.selected).every(([group, values]) => {
            if (values.length === 0) return true;

            const field = item[group];
            return Array.isArray(field)
                ? field.some((v) => values.includes(v))
                : values.includes(field);
        });
    },

    get results() {
        const filtered = this.items.filter((item) => this.matches(item));

        const comparators = {
            'price-asc': (a, b) => a.price - b.price,
            'price-desc': (a, b) => b.price - a.price,
            rating: (a, b) => (b.rating ?? 0) - (a.rating ?? 0),
            popularity: (a, b) => (b.popularity ?? 0) - (a.popularity ?? 0),
        };

        return filtered.sort(comparators[this.sort] ?? comparators.popularity);
    },

    get resultCount() {
        return this.results.length;
    },

    /** Keep the two range thumbs from crossing over. */
    onMinChange() {
        if (Number(this.minPrice) > Number(this.maxPrice)) this.minPrice = this.maxPrice;
    },

    onMaxChange() {
        if (Number(this.maxPrice) < Number(this.minPrice)) this.maxPrice = this.minPrice;
    },
});
