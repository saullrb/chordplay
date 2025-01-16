<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';
import { ref } from 'vue';
import SongContent from './partials/SongContent.vue';
import IconLink from '@/Components/IconLink.vue';

defineProps({
    song: Object,
    artist: Object,
    valid_chords: Array,
    can: {
        type: Object,
        default: () => ({
            update_song: {
                type: Boolean,
                default: false,
            },
        }),
    },
});

const isDualColumn = ref(false);
</script>

<template>
    <Head :title="`${song.name} - ${artist.name}`" />
    <NavBar />

    <Container>
        <header>
            <div class="flex items-center gap-2">
                <PageHeader :title="song.name" />
                <IconLink
                    v-if="can.update_song"
                    :href="route('artists.songs.edit', { artist, song })"
                >
                    <i class="fa-solid fa-pencil"></i>
                </IconLink>
            </div>
            <TextLink :href="route('artists.show', artist)">
                {{ artist.name }}
            </TextLink>
            <div class="my-6 flex items-center gap-1 text-sm dark:text-white">
                <span>Key: </span>
                <span class="text-yellow-600">
                    {{ song.key }}
                </span>
            </div>
            <button
                @click="isDualColumn = !isDualColumn"
                class="hidden rounded-md border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-300 hover:text-gray-900 sm:block dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                :class="{
                    'border-gray-400 bg-yellow-600 text-white hover:bg-yellow-600 hover:text-white hover:brightness-105 dark:border-gray-600 dark:bg-yellow-600 dark:text-white dark:hover:bg-yellow-600 dark:hover:brightness-110':
                        isDualColumn,
                }"
            >
                <i class="fa-solid fa-table-columns mr-1"></i>
                Dual Column
            </button>
        </header>
        <main
            class="py-6 dark:text-white"
            :class="{
                'columns-2 gap-8': isDualColumn,
            }"
        >
            <SongContent :content="song.content" :valid_chords="valid_chords" />
        </main>
    </Container>
</template>
