<script setup>
defineProps({
    loading: Boolean,
    onLoadMore: Function,
});
</script>

<template>
    <button
        :disabled="loading"
        @click="onLoadMore"
        class="btn btn-soft btn-primary"
        :class="{
            'cursor-wait': loading,
        }"
    >
        <span v-if="loading" class="loading loading-xs loading-spinner"></span>
        <slot />
    </button>
</template>
