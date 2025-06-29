<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <p class="text-error text-xs">
            {{ message }}
        </p>
    </div>
</template>
