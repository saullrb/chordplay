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
    hasImage: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <ul class="list">
        <li
            v-for="(item, i) in items"
            :key="item"
            class="list-row hover:bg-primary/10 relative"
        >
            <Link
                :href="route(showRouteName, parent ? [parent, item] : item)"
                class="absolute inset-0 z-10"
                :prefetch="i < 5 ? 'mount' : 'click'"
            />

            <div>
                <img
                    class="rounded-box size-10"
                    :class="hasImage ? 'block' : 'hidden'"
                    :src="item.profile_image_url ?? '/images/artist.webp'"
                />
            </div>
            <div class="flex items-center gap-2">
                <FavoriteIndicator :is-favorited="item.is_favorited" />
                <div>{{ item.name }}</div>
            </div>
            <span class="text-base-content/70 text-xs lg:text-sm"
                >{{ item.views }} views</span
            >
        </li>
    </ul>
</template>
