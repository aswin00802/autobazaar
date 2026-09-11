/**
 * Indian-numbering helpers. Every price on the reference screens uses the
 * lakh/crore grouping (₹2,52,000 — not ₹252,000), so this is shared by every
 * component rather than reimplemented per screen.
 */

const inr = new Intl.NumberFormat('en-IN', {
    maximumFractionDigits: 0,
});

/** 252000 -> "₹2,52,000" */
export function formatINR(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) return '₹0';
    return `₹${inr.format(Math.round(num))}`;
}

/** 252000 -> "2,52,000" (no symbol) */
export function formatNumber(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) return '0';
    return inr.format(Math.round(num));
}

/** 252000 -> "₹2.52 Lakh" — used on cards where space is tight. */
export function formatLakh(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) return '₹0';

    if (num >= 10000000) return `₹${(num / 10000000).toFixed(2)} Cr`;
    if (num >= 100000) return `₹${(num / 100000).toFixed(2)} Lakh`;
    return formatINR(num);
}
