document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-with-loader').forEach(button => {
        button.addEventListener('click', function(event) {
            // Prevent the default form submission
            event.preventDefault();

            // Show the loader and hide the button text
            this.classList.add('loading');
            this.disabled = true;

            // Submit the form
            this.closest('form').submit();
        });
    });
});
