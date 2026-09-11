import { formatINR } from '../lib/format';

/**
 * Standard reducing-balance EMI.
 *
 *   EMI = P x r x (1+r)^n / ((1+r)^n - 1)
 *
 * Verified against the worked example printed on new_autos.jpeg:
 *   price 252000, down payment 50000, rate 9.5%, tenure 36
 *   -> EMI 6,469  total interest 30,891  total payment 232,891
 *   (the mockup rounds these to 6,470 / 31,000 / 2,33,000)
 */
export default (config = {}) => ({
    price: config.price ?? 252000,
    downPayment: config.downPayment ?? 50000,
    rate: config.rate ?? 9.5,
    tenure: config.tenure ?? 36,

    tenures: [12, 24, 36, 48, 60],

    get loanAmount() {
        return Math.max(0, this.num(this.price) - this.num(this.downPayment));
    },

    get monthlyEmi() {
        const p = this.loanAmount;
        const n = this.num(this.tenure);
        const r = this.num(this.rate) / 12 / 100;

        if (p <= 0 || n <= 0) return 0;
        if (r === 0) return Math.round(p / n);

        const growth = Math.pow(1 + r, n);
        return Math.round((p * r * growth) / (growth - 1));
    },

    get totalPayment() {
        return this.monthlyEmi * this.num(this.tenure);
    },

    get totalInterest() {
        return Math.max(0, this.totalPayment - this.loanAmount);
    },

    /** Down payment as a share of price — drives the summary copy. */
    get downPaymentPercent() {
        const price = this.num(this.price);
        if (price <= 0) return 0;
        return Math.round((this.num(this.downPayment) / price) * 100);
    },

    money(value) {
        return formatINR(value);
    },

    num(value) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    },
});
