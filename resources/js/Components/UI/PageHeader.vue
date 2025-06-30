<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <h1 class="text-2xl font-bold">
        {{ title }}
    </h1>
</template>
