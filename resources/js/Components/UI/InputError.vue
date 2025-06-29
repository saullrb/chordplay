<script setup>
defineProps({
    message: {
        type: [String, undefined],
        default: undefined,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-error text-xs">
            {{ message }}
        </p>
    </div>
</template>
