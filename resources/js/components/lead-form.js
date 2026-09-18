/**
 * Lead + review forms on the vehicle detail page.
 *
 * One component serves both the modal (Enquire / Quotation / Test drive /
 * Loan — `modal: true`) and the inline "Write a Review" form (`modal: false`).
 * Submits as JSON with the CSRF token, shows Laravel's 422 errors inline and
 * the enquiry number on success. Any element can open the modal by raising
 * `lead-open` on window with { source, loan_amount?, message? }.
 */
const TITLES = {
    enquiry: { title: 'Enquire Now', lede: 'Share your details and our team will call you back.' },
    quotation: { title: 'Get Free Quotation', lede: 'We will send the on-road price breakup with current offers.' },
    test_drive: { title: 'Book Test Drive', lede: 'Pick a date and slot — we will confirm by phone.' },
    loan: { title: 'Apply for Loan / Check Eligibility', lede: 'A finance executive will call with your eligibility and documents list.' },
};

const FOCUSABLE = 'a[href], button:not([disabled]), input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

export default (config = {}) => ({
    url: config.url,
    csrf: config.csrf ?? document.querySelector('meta[name="csrf-token"]')?.content ?? '',
    modal: config.modal ?? true,
    source: config.source ?? 'enquiry',

    open: false,
    state: 'idle', // idle | saving | success | error
    message: '',
    enquiryNo: '',
    errors: {},
    lastFocus: null,

    fields: blankFields(config.defaults),

    init() {
        if (this.modal) {
            window.addEventListener('lead-open', (e) => this.openWith(e.detail ?? {}));
        }
    },

    /* ------------------------------------------------------------- modal */

    get title() {
        return (TITLES[this.source] ?? TITLES.enquiry).title;
    },

    get lede() {
        return (TITLES[this.source] ?? TITLES.enquiry).lede;
    },

    openWith(detail = {}) {
        this.source = detail.source && TITLES[detail.source] ? detail.source : 'enquiry';
        if (detail.loan_amount) this.fields.loan_amount = detail.loan_amount;
        if (detail.message) this.fields.message = detail.message;
        if (detail.variant_id) this.fields.vehicle_variant_id = detail.variant_id;

        this.errors = {};
        this.state = 'idle';
        this.message = '';
        this.lastFocus = document.activeElement;
        this.open = true;
        document.body.style.overflow = 'hidden';

        this.$nextTick(() => this.$refs.panel?.querySelector(FOCUSABLE)?.focus());
    },

    close() {
        if (!this.open) return;
        this.open = false;
        document.body.style.overflow = '';
        if (this.lastFocus && typeof this.lastFocus.focus === 'function') this.lastFocus.focus();
        // Successful submissions start fresh next time; abandoned ones keep what was typed.
        if (this.state === 'success') this.reset();
    },

    /** Keep Tab / Shift+Tab inside the dialog while it is open. */
    trap(event) {
        if (!this.open || event.key !== 'Tab' || !this.$refs.panel) return;
        const items = [...this.$refs.panel.querySelectorAll(FOCUSABLE)].filter((el) => el.offsetParent !== null);
        if (!items.length) return;

        const first = items[0];
        const last = items[items.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    },

    /* ------------------------------------------------------------- submit */

    async submit() {
        if (this.state === 'saving') return;

        this.state = 'saving';
        this.errors = {};
        this.message = '';

        const body = { ...this.fields };
        if (this.modal) body.source = this.source;

        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });

            const data = await response.json().catch(() => ({}));

            if (response.status === 422) {
                this.errors = data.errors ?? {};
                this.message = data.message ?? 'Please check the highlighted fields.';
                this.state = 'idle';
                this.$nextTick(() => this.$root.querySelector('[aria-invalid="true"]')?.focus());
                return;
            }

            if (response.status === 429) {
                throw new Error('Too many requests — please wait a minute and try again.');
            }

            if (!response.ok || data.success === false) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            this.state = 'success';
            this.message = data.message ?? 'Thank you!';
            this.enquiryNo = data.enquiry_no ?? '';
        } catch (e) {
            this.state = 'error';
            this.message = e.message && !e.message.startsWith('HTTP')
                ? e.message
                : 'Could not send right now. Please try again or call us.';
        }
    },

    error(field) {
        const list = this.errors?.[field];
        return Array.isArray(list) ? list[0] : list ?? '';
    },

    hasError(field) {
        return Boolean(this.error(field));
    },

    reset() {
        this.fields = blankFields(config.defaults);
        this.errors = {};
        this.state = 'idle';
        this.message = '';
        this.enquiryNo = '';
    },
});

function blankFields(defaults = {}) {
    return {
        name: '',
        mobile: '',
        email: '',
        city: '',
        pincode: '',
        vehicle_variant_id: '',
        preferred_at: '',
        time_slot: '',
        loan_amount: '',
        message: '',
        rating: 5,
        title: '',
        body: '',
        website: '', // honeypot — stays empty for humans
        ...(defaults ?? {}),
    };
}
