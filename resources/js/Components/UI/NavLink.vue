<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    routeName: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'get',
        validator: (value) =>
            ['get', 'post', 'put', 'patch', 'delete'].includes(value),
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
        :method="method"
        :prefetch="method === 'get'"
        class="font-semibold"
        :class="{
            'bg-base-300': route().current(routeName),
        }"
    >
        <slot />
    </Link>
</template>
