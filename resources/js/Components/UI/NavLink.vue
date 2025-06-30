<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    routeName: {
        type: String,
        required: true,
    },
    params: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <Link
        :href="route(routeName, params)"
        class="text-base-content/70 font-bold"
        :class="{
            'bg-base-300 text-base-content/100 font-bold':
                route().current(routeName),
        }"
    >
        <slot />
    </Link>
</template>
