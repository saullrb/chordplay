<script setup>
defineProps({
    loading: Boolean,
    onLoadMore: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <button
        :disabled="loading"
        class="btn btn-soft btn-primary"
        :class="{
            'cursor-wait': loading,
        }"
        @click="onLoadMore"
    >
        <span v-if="loading" class="loading loading-xs loading-spinner" />
        <slot />
    </button>
</template>
