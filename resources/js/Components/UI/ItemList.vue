<script setup>
import { Link } from '@inertiajs/vue3';
import FavoriteIndicator from './FavoriteIndicator.vue';

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
                <div class="flex items-center gap-2">
                    <FavoriteIndicator :is-favorited="item.is_favorited" />
                    <b>
                        {{ item.name }}
                    </b>
                </div>
                <span class="text-base-content/70 text-xs lg:text-sm"
                    >{{ item.views }} views</span
                >
            </Link>
        </li>
    </ul>
</template>
