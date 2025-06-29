<script setup>
import {
    ChevronLeftIconSolid,
    ChevronRightIconSolid,
} from '@/Components/UI/Icons';
import { Link } from '@inertiajs/vue3';

defineProps({
    pagination: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <ul
        v-if="pagination.total > 0"
        class="mt-2 flex items-center justify-center gap-3 text-gray-900 dark:text-white"
    >
        <li class="flex items-center justify-center">
            <Link
                :href="pagination.prevPageUrl ?? ''"
                class="btn btn-xs btn-ghost"
                :class="{
                    'btn-disabled': !pagination.prevPageUrl,
                }"
            >
                <ChevronLeftIconSolid class="size-3" />
            </Link>
        </li>

        <li
            class="flex items-center justify-center text-sm text-gray-500 dark:text-gray-400"
        >
            {{ pagination.currentPage }}/{{ pagination.lastPage }}
        </li>

        <li class="flex items-center justify-center">
            <Link
                :href="pagination.nextPageUrl ?? ''"
                class="btn btn-xs btn-ghost"
                :class="{
                    'btn-disabled': !pagination.nextPageUrl,
                }"
            >
                <ChevronRightIconSolid class="size-3" />
            </Link>
        </li>
    </ul>
</template>
