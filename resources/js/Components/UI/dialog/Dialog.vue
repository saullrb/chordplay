<script setup>
import { defineExpose, ref } from 'vue';

const dialogRef = ref();

defineExpose({
    show: () => dialogRef.value?.showModal(),
    close: () => dialogRef.value?.close(),
});

defineEmits(['close']);
</script>

<template>
    <dialog ref="dialogRef" class="modal" @close="$emit('close')">
        <div class="modal-box">
            <slot />
            <form method="dialog">
                <button
                    class="btn btn-sm btn-circle btn-ghost absolute top-2 right-2"
                >
                    ✕
                </button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button />
        </form>
    </dialog>
</template>
