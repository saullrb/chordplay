<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'info',
        validator: (value) =>
            ['success', 'error', 'warning', 'info'].includes(value),
    },
    duration: {
        type: Number,
        default: 3000,
    },
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:show']);

const visible = ref(false);

watch(
    () => props.show,
    (newShow) => {
        visible.value = newShow;
        if (newShow) {
            setTimeout(() => {
                visible.value = false;
                emit('update:show', false);
            }, props.duration);
        }
    },
    { immediate: true },
);

function close() {
    visible.value = false;
    emit('update:show', false);
}
</script>

<template>
    <Transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-if="visible"
            dusk="toast-message"
            role="alert"
            class="toast toast-top toast-center z-50"
        >
            <div
                class="alert relative"
                :class="{
                    'alert-success': type === 'success',
                    'alert-error': type === 'error',
                    'alert-warning': type === 'warning',
                    'alert-info': type === 'info',
                }"
            >
                <span>{{ message }}</span>
                <button
                    class="btn btn-xs btn-ghost absolute top-0 right-0 flex items-center justify-center"
                    :class="{
                        'btn-success': type === 'success',
                        'btn-error': type === 'error',
                        'btn-warning': type === 'warning',
                        'btn-info': type === 'info',
                    }"
                    aria-label="Dismiss alert"
                    @click="close"
                >
                    ✕
                </button>
            </div>
        </div>
    </Transition>
</template>
