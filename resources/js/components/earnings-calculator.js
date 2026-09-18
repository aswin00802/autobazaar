import { formatINR } from '../lib/format';
import { toNumber } from '../lib/emi';

/**
 * "Calculate Your Earnings" on the vehicle detail page.
 *
 *   gross/day = fare per km x km per day
 *   fuel/day  = running cost per km x km per day   (from VehicleCatalogService)
 *   net/day   = gross - fuel - other daily expenses
 *
 * Month = 30 days, year = 360 days — the same convention the running-cost
 * estimate uses, so the two cards agree with each other.
 */
const DAYS_PER_MONTH = 30;
const DAYS_PER_YEAR = 360;

export default (config = {}) => ({
    open: config.open ?? false,
    farePerKm: toNumber(config.farePerKm) || 12,
    kmPerDay: toNumber(config.kmPerDay) || 150,
    costPerKm: toNumber(config.costPerKm),
    otherDaily: toNumber(config.otherDaily),

    get dailyGross() {
        return Math.round(toNumber(this.farePerKm) * toNumber(this.kmPerDay));
    },

    get dailyFuel() {
        return Math.round(toNumber(this.costPerKm) * toNumber(this.kmPerDay));
    },

    get dailyNet() {
        return this.dailyGross - this.dailyFuel - Math.round(toNumber(this.otherDaily));
    },

    get monthlyNet() {
        return this.dailyNet * DAYS_PER_MONTH;
    },

    get annualNet() {
        return this.dailyNet * DAYS_PER_YEAR;
    },

    get monthlyGross() {
        return this.dailyGross * DAYS_PER_MONTH;
    },

    get monthlyFuel() {
        return this.dailyFuel * DAYS_PER_MONTH;
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => this.$refs.panel?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
        }
    },

    money(value) {
        return formatINR(value);
    },
});
