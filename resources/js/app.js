const themePreference = 'clearwash-theme';

const updateThemeButtons = () => {
    const isDark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
        button.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        button.querySelectorAll('[data-theme-label]').forEach((label) => {
            label.textContent = isDark ? 'Light mode' : 'Dark mode';
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const navigation = document.querySelector('#mobile-navigation');
    const navigationTrigger = document.querySelector('#mobile-nav-trigger');
    if (navigation && navigationTrigger) {
        navigation.querySelectorAll('nav a.text-white').forEach((link) => {
            link.setAttribute('aria-current', 'page');
        });
        const desktop = window.matchMedia('(min-width: 1024px)');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let closeTimer;
        let previouslyFocused;
        const closeNavigation = (immediate = false) => {
            clearTimeout(closeTimer);
            navigation.classList.remove('is-visible');
            navigationTrigger.setAttribute('aria-expanded', 'false');
            const finish = () => {
                navigation.close();
                document.documentElement.classList.remove('cw-navigation-open');
                if (!desktop.matches) {
                    previouslyFocused?.focus({ preventScroll: true });
                }
            };
            if (immediate || reducedMotion.matches) {
                finish();
            } else {
                closeTimer = setTimeout(finish, 250);
            }
        };
        navigationTrigger.addEventListener('click', () => {
            if (desktop.matches || navigation.open) { return; }
            previouslyFocused = document.activeElement;
            document.documentElement.classList.add('cw-navigation-open');
            navigation.showModal();
            navigationTrigger.setAttribute('aria-expanded', 'true');
            requestAnimationFrame(() => requestAnimationFrame(() => {
                if (navigation.open) { navigation.classList.add('is-visible'); }
            }));
        });
        navigation.querySelector('[data-nav-close]').addEventListener('click', () => closeNavigation());
        navigation.addEventListener('click', (event) => {
            if (event.target === navigation) { closeNavigation(); }
        });
        navigation.addEventListener('cancel', (event) => {
            event.preventDefault();
            closeNavigation();
        });
        navigation.addEventListener('close', () => {
            clearTimeout(closeTimer);
            navigation.classList.remove('is-visible');
            navigationTrigger.setAttribute('aria-expanded', 'false');
            document.documentElement.classList.remove('cw-navigation-open');
        });
        desktop.addEventListener('change', () => {
            if (desktop.matches && navigation.open) { closeNavigation(true); }
        });
    }

    updateThemeButtons();
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            try {
                localStorage.setItem(themePreference, isDark ? 'dark' : 'light');
            } catch {
                // The theme still changes for this page when browser storage is unavailable.
            }
            updateThemeButtons();
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const passwordField = document.getElementById(button.getAttribute('aria-controls'));
        if (!passwordField) { return; }

        button.addEventListener('click', () => {
            const isVisible = passwordField.type === 'password';
            passwordField.type = isVisible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(isVisible));
            button.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
            button.querySelector('[data-password-show-icon]').hidden = isVisible;
            button.querySelector('[data-password-hide-icon]').hidden = !isVisible;
        });
    });

    document.querySelectorAll('[data-price-preview]').forEach((form) => {
        const categorySelect = form.querySelector('[data-price-category]');
        const memberSelect = form.querySelector('[data-price-member]');
        const levelSelect = form.querySelector('[data-price-level]');
        const breakdown = form.querySelector('[data-price-breakdown]');
        const money = (cents) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(cents / 100);
        const update = () => {
            const categoryOption = categorySelect?.selectedOptions[0] ?? memberSelect?.selectedOptions[0];
            const levelOption = levelSelect?.selectedOptions[0];
            const ready = Boolean(categoryOption?.value && levelOption?.value && levelOption.dataset.fee !== '');
            const values = ready
                ? (() => {
                    const feeCents = Math.round(Number(levelOption.dataset.fee) * 100);
                    const discountBasisPoints = Math.round(Number(categoryOption.dataset.discount || 0) * 100);
                    const discountCents = Math.round(feeCents * discountBasisPoints / 10000);
                    return [money(feeCents), `${(discountBasisPoints / 100).toFixed(2)}%`, money(discountCents), money(feeCents - discountCents)];
                })()
                : ['—', '—', '—', '—'];
            ['original', 'percent', 'discount', 'due'].forEach((field, index) => {
                breakdown.querySelector(`[data-price-${field}]`).textContent = values[index];
            });
        };
        categorySelect?.addEventListener('change', update);
        memberSelect?.addEventListener('change', update);
        levelSelect?.addEventListener('change', update);
        update();
    });
});
