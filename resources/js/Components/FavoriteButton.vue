<script setup>
import { StarIcon, StarIconSolid } from '@/Components/UI/Icons';

defineProps({
    favorited: {
        type: Boolean,
        required: true,
    },
    loading: Boolean,
});

const emit = defineEmits(['favorite']);
</script>

<template>
    <label
        class="swap swap-rotate"
        :class="{
            'cursor-wait': loading,
        }"
        dusk="favorite-button"
    >
        <input
            type="checkbox"
            :checked="favorited"
            aria-label="Favorite"
            @change="emit('favorite')"
        />
        <StarIcon class="swap-off size-6" />
        <StarIconSolid class="swap-on size-6 text-yellow-500" />
    </label>
</template>
