<script setup>
import ToastMessage from '@/Components/UI/ToastMessage.vue';
import { useToast } from '@/Composables/useToast';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

const page = usePage();
const { toastShow, toastMessage, toastType, toastDuration, showToast } =
    useToast();

watch(
    () => page.props.flash?.message,
    (newMessage) => {
        if (newMessage) {
            showToast(
                newMessage,
                page.props.flash?.type || 'info',
                page.props.flash?.duration || 3000,
            );
        }
    },
    { immediate: true },
);
</script>

<template>
    <ToastMessage
        v-model:show="toastShow"
        :message="toastMessage"
        :type="toastType"
        :duration="toastDuration"
        dusk="flash-message"
    />
</template>
