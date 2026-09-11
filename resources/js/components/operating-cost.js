import { formatINR } from '../lib/format';

/**
 * Running-cost estimator.
 *
 * Rounding follows the worked example on new_autos.jpeg exactly:
 *   fuel 102/litre, mileage 32 km/l, 100 km/day
 *   -> per km 3.19, daily 319, monthly 9,570, yearly 1,14,840
 *
 * i.e. the daily figure is rounded first, then multiplied by 30 (month)
 * and 360 (year) — not by 365.
 */
const DAYS_PER_MONTH = 30;
const DAYS_PER_YEAR = 360;

export default (config = {}) => ({
    /** [{ key, label, price, mileage, unit }] */
    fuels: config.fuels ?? [],
    active: config.active ?? (config.fuels?.[0]?.key ?? 'petrol'),

    fuelPrice: 0,
    mileage: 0,
    dailyKm: config.dailyKm ?? 100,

    init() {
        this.selectFuel(this.active);
    },

    selectFuel(key) {
        const fuel = this.fuels.find((f) => f.key === key) ?? this.fuels[0];
        if (!fuel) return;

        this.active = fuel.key;
        this.fuelPrice = fuel.price;
        this.mileage = fuel.mileage;
    },

    get activeFuel() {
        return this.fuels.find((f) => f.key === this.active) ?? this.fuels[0] ?? {};
    },

    get costPerKm() {
        const mileage = this.num(this.mileage);
        if (mileage <= 0) return 0;
        return this.num(this.fuelPrice) / mileage;
    },

    get dailyCost() {
        return Math.round(this.costPerKm * this.num(this.dailyKm));
    },

    get monthlyCost() {
        return this.dailyCost * DAYS_PER_MONTH;
    },

    get yearlyCost() {
        return this.dailyCost * DAYS_PER_YEAR;
    },

    /** Two decimals — this is a per-kilometre figure, whole rupees would hide it. */
    perKmLabel() {
        return `₹${this.costPerKm.toFixed(2)}`;
    },

    money(value) {
        return formatINR(value);
    },

    num(value) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    },
});
