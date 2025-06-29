<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    route_name: String,
    params: Object,
});
</script>

<template>
    <Link
        :href="route(route_name, params)"
        class="text-base-content/70 font-bold"
        :class="{
            'bg-base-300 text-base-content/100 font-bold':
                route().current(route_name),
        }"
    >
        <slot />
    </Link>
</template>
