{{--
    Show/hide button for secret fields. Usage: <input type="password" data-secret ...>
    inside a position-relative wrapper (.form-floating already is one).
    Include once per page:  @include('admin.include.secret-toggle')
--}}
@once
@push('css')
<style>
    .secret-toggle {
        position: absolute;
        top: 50%;
        right: .5rem;
        transform: translateY(-50%);
        z-index: 5;
        border: 0;
        background: transparent;
        padding: .25rem .4rem;
        line-height: 1;
        color: var(--bs-secondary-color);
        cursor: pointer;
        border-radius: .375rem;
    }
    .secret-toggle:hover,
    .secret-toggle:focus-visible { color: var(--bs-primary); }
    input[data-secret] { padding-right: 2.75rem !important; }
</style>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[data-secret]').forEach(function (input) {
            var holder = input.parentElement;
            if (!holder || holder.querySelector('.secret-toggle')) { return; }
            if (getComputedStyle(holder).position === 'static') { holder.style.position = 'relative'; }

            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'secret-toggle';
            button.setAttribute('aria-label', 'Show value');
            button.innerHTML = '<i class="icon-base ri ri-eye-line icon-18px"></i>';

            button.addEventListener('click', function () {
                var reveal = input.type === 'password';
                input.type = reveal ? 'text' : 'password';
                button.setAttribute('aria-label', reveal ? 'Hide value' : 'Show value');
                button.innerHTML = '<i class="icon-base ri ' + (reveal ? 'ri-eye-off-line' : 'ri-eye-line') + ' icon-18px"></i>';
            });

            holder.appendChild(button);
        });
    });
</script>
@endpush
@endonce
