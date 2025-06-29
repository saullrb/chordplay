<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: Array,
    show_route_name: String,
    parent: Object,
});
</script>

<template>
    <ul role="list" class="list">
        <li
            v-for="item in items"
            :key="item"
            class="list-row hover:bg-primary/8"
        >
            <Link
                :href="route(show_route_name, parent ? [parent, item] : item)"
                class="list-col-grow flex justify-between"
            >
                {{ item.name }}
                <span class="text-base-content/70 text-xs lg:text-sm"
                    >{{ item.views }} views</span
                >
            </Link>
        </li>
    </ul>
</template>
