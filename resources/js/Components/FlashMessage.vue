<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);

const message = computed(() => page.props.flash?.message);
const type = computed(() => page.props.flash?.type);

watch(
    () => page.props.flash?.message,
    (newMessage) => {
        show.value = !!newMessage;
        if (newMessage) {
            // flash messages are shown for 3 seconds
            setTimeout(() => {
                show.value = false;
            }, 3000);
        }
    },
    { immediate: true },
);

function close() {
    show.value = false;
}
</script>

<template>
    {{ console.log(message) }}
    <Transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
    >
        <div
            v-if="show"
            role="alert"
            class="fixed top-4 left-1/2 z-50 max-w-md -translate-x-1/2 rounded-lg p-4 text-sm shadow-lg"
            :class="{
                'border border-green-200 bg-green-50 text-green-800':
                    type === 'success',
                'border border-red-200 bg-red-50 text-red-800':
                    type === 'error',
                'border border-blue-200 bg-blue-50 text-blue-800':
                    type === 'info',
                'border border-yellow-200 bg-yellow-50 text-yellow-800':
                    type === 'warning',
            }"
        >
            <span class="px-3">{{ message }}</span>
            <button
                @click="close"
                class="absolute top-1 right-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                aria-label="Dismiss alert"
            >
                <span class="sr-only">Dismiss popup</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </Transition>
</template>
