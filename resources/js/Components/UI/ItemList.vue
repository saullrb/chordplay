<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    showRouteName: {
        type: String,
        required: true,
    },
    parent: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <ul role="list" class="list">
        <li
            v-for="(item, i) in items"
            :key="item"
            class="list-row hover:bg-primary/8"
        >
            <Link
                :href="route(showRouteName, parent ? [parent, item] : item)"
                class="list-col-grow flex justify-between"
                :prefetch="i < 5 ? 'mount' : 'click'"
            >
                {{ item.name }}
                <span class="text-base-content/70 text-xs lg:text-sm"
                    >{{ item.views }} views</span
                >
            </Link>
        </li>
    </ul>
</template>
