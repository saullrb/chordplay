<script setup>
import Dropdown from '@/Components/UI/Dropdown.vue';
import { CheckIconSolid, ChevronDownIconSolid } from '@/Components/UI/Icons';
import { useTheme } from '@/Composables/useTheme';
import ThemeIcon from './ThemeIcon.vue';

const { persistTheme, available_themes, current_theme } = useTheme();
</script>

<template>
    <Dropdown class="dropdown-end" list-class="w-40" trigger-class="btn-ghost">
        <template #trigger>
            <ThemeIcon />
            <ChevronDownIconSolid class="text-base-content/50 size-4" />
        </template>
        <li v-for="theme in available_themes" :key="theme.name">
            <button class="gap-3 px-2" @click="persistTheme(theme.name)">
                <ThemeIcon :data-theme="theme.name" />
                <div class="truncate">{{ theme.display_name }}</div>
                <CheckIconSolid
                    v-if="theme.name === current_theme"
                    class="size-3 shrink-0"
                />
            </button>
        </li>
    </Dropdown>
</template>
