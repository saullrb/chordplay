<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const dropdown = ref(null);

defineProps({
    triggerClass: [String, Object],
    listClass: String,
});

function handleClickOutside(event) {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
        dropdown.value.open = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
<template>
    <details class="dropdown block" ref="dropdown">
        <summary class="btn btn-sm after:hidden" :class="triggerClass">
            <slot name="trigger" />
        </summary>
        <ul
            tabindex="0"
            class="menu menu-md dropdown-content bg-base-200 rounded-box z-1 gap-2 p-2 shadow"
            :class="listClass"
        >
            <slot />
        </ul>
    </details>
</template>
