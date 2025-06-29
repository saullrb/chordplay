<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const show = ref(false);

const message = computed(() => page.props.flash?.message);
const type = computed(() => page.props.flash?.type);
const duration = computed(() => page.props.flash?.duration);

watch(
    () => page.props.flash?.message,
    (newMessage) => {
        show.value = !!newMessage;
        if (newMessage) {
            // flash messages are shown for 3 seconds
            setTimeout(() => {
                show.value = false;
            }, duration.value);
        }
    },
    { immediate: true },
);

function close() {
    show.value = false;
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
            v-if="show"
            dusk="flash-message"
            role="alert"
            class="toast toast-top toast-center"
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
                    @click="close"
                    class="btn btn-xs btn-ghost absolute top-0 right-0 flex items-center justify-center"
                    :class="{
                        'btn-success': type === 'success',
                        'btn-error': type === 'error',
                        'btn-warning': type === 'warning',
                        'btn-info': type === 'info',
                    }"
                    aria-label="Dismiss alert"
                >
                    ✕
                </button>
            </div>
        </div>
    </Transition>
</template>
