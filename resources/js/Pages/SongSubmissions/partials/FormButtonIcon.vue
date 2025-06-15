<script setup>
import { usePage } from '@inertiajs/vue3';

defineProps({
    handleSubmit: Function,
});

const page = usePage();
const user = page.props.auth.user;
</script>
<template>
    <form
        v-if="user"
        @submit.prevent="handleSubmit"
        class="flex items-center justify-center rounded-full text-gray-900 transition-colors hover:bg-gray-300 dark:text-white dark:hover:bg-gray-800"
    >
        <button type="submit" class="flex size-8 items-center justify-center">
            <slot />
        </button>
    </form>
</template>
