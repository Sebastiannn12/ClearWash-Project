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
});
