<script setup>
import { StarIconSolid } from './Icons';

defineProps({
    isFavorited: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <StarIconSolid
        class="invisible size-4"
        :class="{
            'visible text-yellow-500': isFavorited,
        }"
    />
</template>
