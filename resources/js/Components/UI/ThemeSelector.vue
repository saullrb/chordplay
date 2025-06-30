<script setup>
import Dropdown from '@/Components/UI/Dropdown.vue';
import { CheckIconSolid, ChevronDownIconSolid } from '@/Components/UI/Icons';
import { useTheme } from '@/Composables/useTheme';
import ThemeIcon from './ThemeIcon.vue';

const { persistTheme, availableThemes, currentTheme } = useTheme();
</script>

<template>
    <Dropdown class="dropdown-end" list-class="w-40" trigger-class="btn-ghost">
        <template #trigger>
            <ThemeIcon />
            <ChevronDownIconSolid class="text-base-content/70 size-4" />
        </template>
        <li v-for="theme in availableThemes" :key="theme.name">
            <button class="gap-3 px-2" @click="persistTheme(theme.name)">
                <ThemeIcon :data-theme="theme.name" />
                <div class="truncate">
                    {{ theme.displayName }}
                </div>
                <CheckIconSolid
                    v-if="theme.name === currentTheme"
                    class="size-3 shrink-0"
                />
            </button>
        </li>
    </Dropdown>
</template>
