<script setup>
import { StarIcon, StarIconSolid } from '@/Components/UI/Icons';

defineProps({
    favorited: {
        type: Boolean,
        required: true,
    },
    loading: Boolean,
});

defineEmits(['favorite']);
</script>

<template>
    <button
        class="swap swap-rotate disabled:opacity-50"
        :class="{
            'swap-active': favorited,
        }"
        aria-label="Favorite"
        dusk="favorite-button"
        :disabled="loading"
        @click="$emit('favorite')"
    >
        <StarIcon dusk="empty-star" class="swap-off size-6" />
        <StarIconSolid
            dusk="filled-star"
            class="swap-on size-6 text-yellow-500"
        />
    </button>
</template>
