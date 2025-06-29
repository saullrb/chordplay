<script setup>
import { defineExpose, ref } from 'vue';

const dialog_ref = ref();

defineExpose({
    show: () => dialog_ref.value?.showModal(),
    close: () => dialog_ref.value?.close(),
});
</script>

<template>
    <dialog ref="dialog_ref" class="modal" @close="$emit('close')">
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
