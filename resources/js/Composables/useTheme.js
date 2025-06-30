import { onMounted, ref } from 'vue';

const availableThemes = [
    { displayName: 'Light', name: 'light' },
    { displayName: 'Dark', name: 'dark' },
    { displayName: 'Night', name: 'night' },
];
const currentTheme = ref('light');
const systemTheme = ref(getSystemTheme());

export function useTheme() {
    onMounted(() => {
        currentTheme.value = getLocalStoreTheme() ?? systemTheme.value;
    });

    function persistTheme(value) {
        currentTheme.value = value;
        localStorage.setItem('theme', value);
        applyTheme(value);
    }

    return { persistTheme, availableThemes, currentTheme };
}

export function initializeTheme() {
    const savedTheme = getLocalStoreTheme();
    applyTheme(savedTheme);

    window
        .matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', handleSystemThemeChange);
}

function handleSystemThemeChange() {
    systemTheme.value = getSystemTheme();
    const savedTheme = getLocalStoreTheme();

    if (!savedTheme) {
        currentTheme.value = systemTheme.value;
        applyTheme(savedTheme);
    }
}

function getLocalStoreTheme() {
    return localStorage.getItem('theme');
}

function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
}

function applyTheme(value) {
    document.documentElement.setAttribute(
        'data-theme',
        value ?? getSystemTheme(),
    );
}
