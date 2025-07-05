import { ref } from 'vue';

export function useToast() {
    const toastShow = ref(false);
    const toastMessage = ref('');
    const toastType = ref('info');
    const toastDuration = ref(3000);

    function showToast(message, type = 'info', duration = 3000) {
        toastMessage.value = message;
        toastType.value = type;
        toastDuration.value = duration;
        toastShow.value = true;
    }

    function clearToast() {
        toastShow.value = false;
        toastMessage.value = '';
        toastType.value = 'info';
        toastDuration.value = 3000;
    }

    return {
        toastShow,
        toastMessage,
        toastType,
        toastDuration,
        showToast,
        clearToast,
    };
}
