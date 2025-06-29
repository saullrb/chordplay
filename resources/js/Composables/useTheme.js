import { onMounted, ref } from 'vue';

const available_themes = [
    { display_name: 'Light', name: 'light' },
    { display_name: 'Dark', name: 'dark' },
    { display_name: 'Night', name: 'night' },
];
const current_theme = ref('light');
const user_system_theme = ref(getSystemTheme());

export function useTheme() {
    onMounted(() => {
        current_theme.value = getLocalStoreTheme() ?? user_system_theme.value;
    });

    function persistTheme(value) {
        current_theme.value = value;
        localStorage.setItem('theme', value);
        applyTheme(value);
    }

    return { persistTheme, available_themes, current_theme };
}

export function initializeTheme() {
    const saved_theme = getLocalStoreTheme();
    applyTheme(saved_theme);

    window
        .matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', handleSystemThemeChange);
}

function handleSystemThemeChange() {
    user_system_theme.value = getSystemTheme();
    const saved_theme = getLocalStoreTheme();

    if (!saved_theme) {
        current_theme.value = user_system_theme.value;
        applyTheme(saved_theme);
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
