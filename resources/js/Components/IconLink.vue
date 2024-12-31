<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link
        class="flex size-8 items-center justify-center rounded-full text-gray-900 transition-colors hover:bg-gray-300 dark:text-white dark:hover:bg-gray-800"
    >
        <slot />
    </Link>
</template>
