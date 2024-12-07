<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  items: Array,
  showRouteName: String,
  parent: Object,
});
</script>

<template>
  <ul role="list" class="my-6 divide-y divide-gray-400">
    <li v-for="item in items" class="flex justify-between gap-x-6 px-2 py-5">
      <Link
        :href="route(showRouteName, parent ? [parent, item] : item)"
        class="group flex items-center gap-4 font-semibold"
      >
        <span
          class="text-md text-gray-900 group-hover:text-yellow-600 dark:text-white"
        >
          {{ item.name }}
        </span>
      </Link>
      <span
        class="flex items-center px-2 text-xs font-semibold text-gray-500 dark:text-gray-400"
        >{{ item.views }} views</span
      >
    </li>
  </ul>
</template>
