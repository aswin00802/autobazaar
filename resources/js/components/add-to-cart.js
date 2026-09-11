/**
 * Add to Cart button.
 *
 * Posts to the real cart endpoint and updates the header badge in place, so a
 * visitor can keep shopping without a page reload. Guests are allowed — the
 * cart lives against their session and is merged into their account when they
 * sign in at checkout.
 */
export default (config = {}) => ({
    productModelId: config.productModelId,
    qty: config.qty ?? 1,
    state: 'idle', // idle | saving | added | error
    message: '',

    get label() {
        return {
            idle: 'Add to Cart',
            saving: 'Adding…',
            added: 'Added',
            error: 'Try again',
        }[this.state];
    },

    async add() {
        if (this.state === 'saving') return;

        this.state = 'saving';
        this.message = '';

        try {
            const response = await fetch(config.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    product_model_id: this.productModelId,
                    qty: this.qty,
                }),
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();

            this.state = 'added';
            this.$store.cart.count = data.count ?? this.$store.cart.count + this.qty;

            // Revert the label so the button can be used again.
            setTimeout(() => {
                if (this.state === 'added') this.state = 'idle';
            }, 1800);
        } catch (e) {
            this.state = 'error';
            this.message = 'Could not add that item.';
            setTimeout(() => {
                if (this.state === 'error') this.state = 'idle';
            }, 2500);
        }
    },
});
