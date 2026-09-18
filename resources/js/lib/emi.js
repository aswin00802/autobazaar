/**
 * Reducing-balance EMI, shared by every calculator on the site.
 *
 *   EMI = P x r x (1+r)^n / ((1+r)^n - 1)
 *
 * Mirrors App\Services\VehicleCatalogService::emi() exactly, so the "EMI from"
 * figure rendered by PHP and the live figure in the browser never disagree.
 */
export function calcEmi(principal, ratePct, months) {
    const p = toNumber(principal);
    const n = toNumber(months);
    const r = toNumber(ratePct) / 12 / 100;

    if (p <= 0 || n <= 0) return 0;
    if (r === 0) return Math.round(p / n);

    const growth = Math.pow(1 + r, n);
    return Math.round((p * r * growth) / (growth - 1));
}

/** { emi, totalInterest, totalPayment } for one loan. */
export function emiBreakdown(principal, ratePct, months) {
    const p = toNumber(principal);
    const n = toNumber(months);
    const emi = calcEmi(p, ratePct, n);
    const totalPayment = emi * n;

    return {
        emi,
        totalPayment,
        totalInterest: Math.max(0, totalPayment - p),
    };
}

export function toNumber(value) {
    const parsed = parseFloat(value);
    return Number.isFinite(parsed) ? parsed : 0;
}
