/**
 * Header location switcher + the /buying-options serviceability gate.
 *
 * Three tiers, straight from enquiry2.jpeg:
 *   direct_purchase   Chennai, Tiruvallur, Kanchipuram, Chengalpattu
 *   buying_assistance any other Tamil Nadu district
 *   dealer_connect    any other state
 */
const KEY = 'ab.location';

export default (config = {}) => ({
    states: config.states ?? [],
    directPurchaseDistricts: config.directPurchaseDistricts ?? [],

    state: config.state ?? 'Tamil Nadu',
    district: config.district ?? 'Chennai',
    city: config.city ?? 'Chennai',
    pincode: config.pincode ?? '',

    open: false,
    resolved: null,

    init() {
        const saved = this.read();
        if (saved) Object.assign(this, saved);
    },

    get districts() {
        return this.states.find((s) => s.name === this.state)?.districts ?? [];
    },

    get cities() {
        return this.districts.find((d) => d.name === this.district)?.cities ?? [];
    },

    onStateChange() {
        this.district = this.districts[0]?.name ?? '';
        this.onDistrictChange();
    },

    onDistrictChange() {
        this.city = this.cities[0]?.name ?? '';
    },

    get tier() {
        if (this.state !== 'Tamil Nadu') return 'dealer_connect';
        return this.directPurchaseDistricts.includes(this.district)
            ? 'direct_purchase'
            : 'buying_assistance';
    },

    get tierLabel() {
        return {
            direct_purchase: 'Direct Purchase Available',
            buying_assistance: 'Buying Assistance',
            dealer_connect: 'Dealer Connect',
        }[this.tier];
    },

    get label() {
        return this.city || this.district || this.state;
    },

    apply() {
        this.resolved = this.tier;
        this.write();
        this.open = false;

        // Let the page react (price panel, buying-options cards).
        this.$dispatch('location-changed', {
            state: this.state,
            district: this.district,
            city: this.city,
            pincode: this.pincode,
            tier: this.tier,
        });
    },

    read() {
        try {
            const raw = window.localStorage.getItem(KEY);
            return raw ? JSON.parse(raw) : null;
        } catch {
            return null;
        }
    },

    write() {
        try {
            window.localStorage.setItem(
                KEY,
                JSON.stringify({
                    state: this.state,
                    district: this.district,
                    city: this.city,
                    pincode: this.pincode,
                }),
            );
        } catch {
            /* Storage unavailable — the choice still applies to this page view. */
        }
    },
});
