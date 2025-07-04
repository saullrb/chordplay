export function debounce(fn, delay = 300) {
    let timeout = null;

    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, args), delay);
    };
}
