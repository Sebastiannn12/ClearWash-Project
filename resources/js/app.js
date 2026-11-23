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
