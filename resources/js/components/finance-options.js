import { formatINR } from '../lib/format';
import { calcEmi, emiBreakdown, toNumber } from '../lib/emi';

/**
 * Finance Options card on the vehicle detail page.
 *
 * One tab per lender. The rate is the lender's own (from finance_lender_rates),
 * never typed by the visitor — they only choose the loan amount and tenure.
 * The comparison table recomputes for every tenure the lender offers.
 *
 * "Apply for Loan" does not post anything itself: it raises `lead-open`, which
 * the shared lead modal listens for, pre-filled with the chosen loan amount.
 */
export default (config = {}) => ({
    /** [{ id, name, logo, rate, tenures[], max_loan_pct, max_loan_pct_no_cibil, documents[] }] */
    lenders: config.lenders ?? [],
    onRoad: toNumber(config.onRoad),
    lenderIndex: 0,
    loanAmount: toNumber(config.loanAmount),
    tenure: toNumber(config.tenure) || 36,
    docsOpen: false,
    logoFailed: {},

    init() {
        this.normaliseTenure();
    },

    /* ------------------------------------------------------------ lender */

    get lender() {
        return this.lenders[this.lenderIndex] ?? null;
    },

    get rate() {
        return toNumber(this.lender?.rate);
    },

    get tenures() {
        const list = this.lender?.tenures ?? [];
        return list.length ? list : [36];
    },

    selectLender(index) {
        this.lenderIndex = index;
        this.normaliseTenure();
    },

    /** Keep the tenure valid when the new lender does not offer the old one. */
    normaliseTenure() {
        const current = toNumber(this.tenure);
        if (this.tenures.includes(current)) return;
        this.tenure = this.tenures.includes(36) ? 36 : this.tenures[0];
    },

    /* ------------------------------------------------------------ limits */

    get maxLoan() {
        const pct = toNumber(this.lender?.max_loan_pct) || 100;
        return Math.round((this.onRoad * pct) / 100);
    },

    get maxLoanNoCibil() {
        const pct = toNumber(this.lender?.max_loan_pct_no_cibil);
        return pct > 0 ? Math.round((this.onRoad * pct) / 100) : 0;
    },

    get overLimit() {
        return this.onRoad > 0 && toNumber(this.loanAmount) > this.maxLoan;
    },

    /* ------------------------------------------------------------ maths */

    get emi() {
        return calcEmi(this.loanAmount, this.rate, this.tenure);
    },

    get totalPayment() {
        return this.emi * toNumber(this.tenure);
    },

    get totalInterest() {
        return Math.max(0, this.totalPayment - toNumber(this.loanAmount));
    },

    /** One row per tenure the lender offers — the selected one is highlighted in the markup. */
    get table() {
        return this.tenures.map((months) => ({
            months,
            label: this.tenureLabel(months),
            ...emiBreakdown(this.loanAmount, this.rate, months),
        }));
    },

    /* ------------------------------------------------------------ helpers */

    tenureLabel(months) {
        const years = months / 12;
        if (Number.isInteger(years)) return `${years} ${years === 1 ? 'Year' : 'Years'}`;
        return `${months} Months`;
    },

    isSelected(months) {
        return toNumber(this.tenure) === months;
    },

    logoOk(index) {
        return !this.logoFailed[index];
    },

    markLogoFailed(index) {
        this.logoFailed = { ...this.logoFailed, [index]: true };
    },

    applyLoan() {
        window.dispatchEvent(
            new CustomEvent('lead-open', {
                detail: {
                    source: 'loan',
                    loan_amount: Math.round(toNumber(this.loanAmount)),
                    message: this.lender
                        ? `Loan enquiry via ${this.lender.name} — ${this.tenureLabel(toNumber(this.tenure))} @ ${this.rate}% p.a.`
                        : '',
                },
            }),
        );
    },

    money(value) {
        return formatINR(value);
    },

    num(value) {
        return toNumber(value);
    },
});
