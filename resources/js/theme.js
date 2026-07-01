const STORAGE_KEY = 'theme';

function systemPrefersDark() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function resolveIsDark(mode) {
    if (mode === 'dark') {
        return true;
    }
    if (mode === 'light') {
        return false;
    }

    return systemPrefersDark();
}

export function getTheme() {
    const stored = localStorage.getItem(STORAGE_KEY);

    return stored === 'light' || stored === 'dark' || stored === 'system' ? stored : 'system';
}

export function applyTheme(mode = getTheme()) {
    document.documentElement.classList.toggle('dark', resolveIsDark(mode));
    document.documentElement.dataset.theme = mode;
}

export function setTheme(mode) {
    localStorage.setItem(STORAGE_KEY, mode);
    applyTheme(mode);
}

export function cycleTheme() {
    const order = ['light', 'dark', 'system'];
    const next = order[(order.indexOf(getTheme()) + 1) % order.length];
    setTheme(next);

    return next;
}

applyTheme();

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (getTheme() === 'system') {
        applyTheme('system');
    }
});

window.theme = { getTheme, setTheme, cycleTheme, applyTheme };

document.addEventListener('alpine:init', () => {
    window.Alpine.data('themeToggle', () => ({
        mode: getTheme(),
        init() {
            this.mode = getTheme();
        },
        cycle() {
            this.mode = cycleTheme();
        },
        label() {
            return { light: 'Light mode', dark: 'Dark mode', system: 'System theme' }[this.mode];
        },
    }));
});
