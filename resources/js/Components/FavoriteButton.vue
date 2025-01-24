<script setup>
import { defineProps, defineEmits } from 'vue';

defineProps({ is_favorited: Boolean });

const emit = defineEmits(['favorite']);
</script>

<template>
    <button class="disabled:cursor-wait" @click="() => emit('favorite')">
        <i
            class="fa-regular fa-star text-xl text-black hover:text-yellow-600 dark:text-white"
            :class="{
                'fa-solid text-yellow-600 hover:brightness-90 dark:text-yellow-600':
                    is_favorited,
            }"
        ></i>
    </button>
</template>
