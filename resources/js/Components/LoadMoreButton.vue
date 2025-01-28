<script setup>
defineProps({
    loading: Boolean,
    onLoadMore: Function,
});
</script>

<template>
    <button
        :disabled="loading"
        @click="onLoadMore"
        class="rounded-md border border-gray-500 bg-transparent px-5 py-2 text-gray-900 duration-300 hover:bg-gray-500 hover:text-white disabled:cursor-wait dark:border-gray-800 dark:text-white dark:hover:bg-gray-800"
    >
        Load More
    </button>
</template>
