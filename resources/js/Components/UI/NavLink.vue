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
        class="font-semibold"
        :class="{
            'bg-base-300': route().current(routeName),
        }"
    >
        <slot />
    </Link>
</template>
