<script setup>
import { ref } from 'vue';
import Dialog from './Dialog.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['confirm', 'cancel']);

const dialogRef = ref();

function show() {
    dialogRef.value?.show();
}

function close() {
    dialogRef.value?.close();
}

function onConfirm() {
    emit('confirm');
    close();
}

function onCancel() {
    emit('cancel');
    close();
}

function onClose() {
    emit('cancel');
}

defineExpose({ show, close });
</script>

<template>
    <Dialog ref="dialogRef" @close="onClose">
        <h3 class="mb-2 text-lg font-bold">
            {{ title }}
        </h3>
        <p class="mb-4">
            {{ message }}
        </p>
        <div class="flex justify-end gap-2">
            <button class="btn" @click="onCancel">Cancel</button>
            <button
                class="btn btn-primary ms-3 data-[loading]:pointer-events-none data-[loading]:cursor-not-allowed data-[loading]:opacity-50"
                dusk="confirm-modal-button"
                @click="onConfirm"
            >
                Confirm
            </button>
        </div>
    </Dialog>
</template>
