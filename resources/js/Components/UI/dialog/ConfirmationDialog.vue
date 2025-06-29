<script setup>
import { ref } from 'vue';
import Dialog from './Dialog.vue';

defineProps({
    title: String,
    message: String,
});

const emit = defineEmits(['confirm', 'cancel']);

const dialog_ref = ref();

function show() {
    dialog_ref.value?.show();
}

function close() {
    dialog_ref.value?.close();
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
    <Dialog ref="dialog_ref" @close="onClose">
        <h3 class="mb-2 text-lg font-bold">{{ title }}</h3>
        <p class="mb-4">{{ message }}</p>
        <div class="flex justify-end gap-2">
            <button class="btn" @click="onCancel">Cancel</button>
            <button
                class="btn btn-primary ms-3 data-[loading]:pointer-events-none data-[loading]:cursor-not-allowed data-[loading]:opacity-50"
                @click="onConfirm"
                dusk="confirm-modal-button"
            >
                Confirm
            </button>
        </div>
    </Dialog>
</template>
