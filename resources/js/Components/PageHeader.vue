<script setup>
defineProps({
    title: String,
});
</script>

<template>
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        {{ title }}
    </h1>
</template>
