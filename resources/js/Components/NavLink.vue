<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  active: Boolean,
});
</script>

<template>
  <Link
    class="block rounded-lg px-3 py-2 text-sm font-semibold text-gray-500 hover:bg-gray-500 hover:text-white dark:text-gray-400 dark:hover:bg-gray-800"
    :class="{
      'text-gray-900 dark:text-white': active,
    }"
  >
    <slot />
  </Link>
</template>
