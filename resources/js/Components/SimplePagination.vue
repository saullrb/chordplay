<script setup>
import IconLink from './IconLink.vue';

defineProps({
    pagination: Object,
});
</script>

<template>
    <ul
        v-if="pagination.total > 0"
        class="mt-2 flex items-center justify-center gap-3 text-gray-900 dark:text-white"
    >
        <li
            v-if="pagination.prev_page_url"
            class="flex size-8 items-center justify-center text-xs"
        >
            <IconLink :href="pagination.prev_page_url">
                <i class="fa-solid fa-chevron-left"></i>
            </IconLink>
        </li>

        <li
            v-else
            class="flex size-8 items-center justify-center px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400"
        >
            <i class="fa-solid fa-chevron-left"></i>
        </li>

        <li
            class="flex items-center justify-center text-sm text-gray-500 dark:text-gray-400"
        >
            {{ pagination.current_page }}/{{ pagination.last_page }}
        </li>

        <li
            v-if="pagination.next_page_url"
            class="flex size-8 items-center justify-center text-xs"
        >
            <IconLink :href="pagination.next_page_url">
                <i class="fa-solid fa-chevron-right"></i>
            </IconLink>
        </li>

        <li
            v-else
            class="flex size-8 items-center justify-center px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400"
        >
            <i class="fa-solid fa-chevron-right"></i>
        </li>
    </ul>
</template>
