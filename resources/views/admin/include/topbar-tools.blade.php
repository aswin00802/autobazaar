{{-- Behaviour for the top-bar search box and the light/dark switch. Loaded once from the admin layout. --}}
<style>
    .admin-search { position: relative; max-width: 520px; }
    .admin-search-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color); pointer-events: none; }
    .admin-search-input { padding-left: 2.5rem; border-radius: 50rem; background: var(--bs-body-bg); }
    .admin-search-input::-webkit-search-cancel-button { cursor: pointer; }
    .admin-search-results {
        position: absolute; top: calc(100% + .5rem); left: 0; right: 0; z-index: 1090;
        max-height: 70vh; overflow-y: auto;
        background: var(--bs-paper-bg, var(--bs-body-bg)); border: 1px solid var(--bs-border-color); border-radius: .625rem; padding: .5rem 0;
    }
    .admin-search-group { padding: .5rem 1rem .25rem; font-size: .75rem; letter-spacing: .06em; text-transform: uppercase; color: var(--bs-secondary-color); }
    .admin-search-item { display: block; padding: .5rem 1rem; color: inherit; text-decoration: none; }
    .admin-search-item small { display: block; color: var(--bs-secondary-color); text-transform: capitalize; }
    .admin-search-item:hover, .admin-search-item.is-active { background: rgba(var(--bs-primary-rgb), .08); color: inherit; }
    .admin-search-note { padding: .75rem 1rem; color: var(--bs-secondary-color); }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        /* ---------- Light / dark ---------- */
        var toggle = document.getElementById('themeToggle');
        var icon = document.getElementById('themeToggleIcon');

        function paintIcon() {
            if (!icon) { return; }
            var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            icon.className = 'icon-base ri icon-22px ' + (dark ? 'ri-sun-line' : 'ri-moon-clear-line');
        }

        if (toggle) {
            paintIcon();
            toggle.addEventListener('click', function () {
                var next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', next);
                try { localStorage.setItem('adminTheme', next); } catch (e) {}
                paintIcon();
            });
        }

        /* ---------- Global search ---------- */
        var box = document.getElementById('adminSearch');
        if (!box || !window.fetch) { return; }

        var input = document.getElementById('adminSearchInput');
        var panel = document.getElementById('adminSearchResults');
        var timer = null, lastTerm = '', requestNo = 0, active = -1;

        function escapeHtml(value) {
            var div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        function open(html) {
            panel.innerHTML = html;
            panel.hidden = false;
            input.setAttribute('aria-expanded', 'true');
            active = -1;
        }

        function close() {
            panel.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            active = -1;
        }

        function items() { return panel.querySelectorAll('.admin-search-item'); }

        function highlight(index) {
            var list = items();
            if (!list.length) { return; }
            active = (index + list.length) % list.length;
            list.forEach(function (node, i) { node.classList.toggle('is-active', i === active); });
            list[active].scrollIntoView({ block: 'nearest' });
        }

        function search(term) {
            var mine = ++requestNo;
            open('<div class="admin-search-note">Searching…</div>');

            fetch(box.dataset.url + '?q=' + encodeURIComponent(term), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function (response) { if (!response.ok) { throw new Error(response.status); } return response.json(); })
                .then(function (data) {
                    if (mine !== requestNo) { return; } // a newer keystroke already replaced this one
                    if (!data.groups.length) {
                        open('<div class="admin-search-note">No matches for “' + escapeHtml(term) + '”.</div>');
                        return;
                    }
                    open(data.groups.map(function (group) {
                        return '<div class="admin-search-group"><i class="icon-base ri ' + escapeHtml(group.icon) + ' icon-14px me-1"></i>' + escapeHtml(group.label) + '</div>' +
                            group.items.map(function (item) {
                                return '<a class="admin-search-item" role="option" href="' + escapeHtml(item.url) + '">' + escapeHtml(item.title) + '<small>' + escapeHtml(item.meta) + '</small></a>';
                            }).join('');
                    }).join(''));
                })
                .catch(function () {
                    if (mine === requestNo) { open('<div class="admin-search-note">Search is unavailable right now. Please try again.</div>'); }
                });
        }

        input.addEventListener('input', function () {
            var term = input.value.trim();
            clearTimeout(timer);
            if (term.length < 2) { lastTerm = ''; close(); return; }
            if (term === lastTerm) { return; }
            timer = setTimeout(function () { lastTerm = term; search(term); }, 250);
        });

        input.addEventListener('focus', function () { if (panel.innerHTML && input.value.trim().length >= 2) { panel.hidden = false; } });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') { event.preventDefault(); highlight(active + 1); }
            else if (event.key === 'ArrowUp') { event.preventDefault(); highlight(active - 1); }
            else if (event.key === 'Enter') {
                var list = items();
                if (list.length) { event.preventDefault(); (list[active >= 0 ? active : 0]).click(); }
            }
            else if (event.key === 'Escape') { close(); input.blur(); }
        });

        document.addEventListener('click', function (event) { if (!box.contains(event.target)) { close(); } });

        // "/" jumps to search from anywhere, unless the admin is already typing somewhere.
        document.addEventListener('keydown', function (event) {
            if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) { return; }
            var tag = (event.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea' || tag === 'select' || event.target.isContentEditable) { return; }
            event.preventDefault();
            input.focus();
        });
    });
</script>
