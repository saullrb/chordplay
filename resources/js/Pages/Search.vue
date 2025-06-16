<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    results: Object,
    query: String,
});
</script>

<template>
    <Head :title="'Search: ' + query" />

    <AppLayout>
        <template #header>
            <PageHeader :title="'Search: ' + query" />
        </template>

        <div class="flex flex-col gap-4">
            <div class="text-black dark:text-white">
                <h2 class="text-xl">Songs</h2>
                <ul class="mt-2">
                    <li v-for="song of results.songs" :key="song.slug">
                        <Link
                            class="hover:text-yellow-600"
                            :href="
                                route('artists.songs.show', [
                                    song.artist,
                                    song.slug,
                                ])
                            "
                        >
                            <b>{{ song.name }}</b> -
                            {{ song.artist.name }}
                        </Link>
                    </li>
                </ul>
            </div>

            <div class="text-black dark:text-white">
                <h2 class="text-xl">Artists</h2>
                <ul class="mt-2">
                    <li
                        v-for="artist of results.artists"
                        :key="artist.slug"
                        class="hover:text-yellow-600"
                    >
                        <Link :href="route('artists.show', artist)">
                            {{ artist.name }}
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
