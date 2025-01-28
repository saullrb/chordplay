<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    favorite_artists: Array,
    favorite_songs: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <NavBar />

    <main class="mt-6">
        <Container>
            <PageHeader title="Dashboard" />

            <div
                class="mt-6 overflow-hidden p-4 sm:rounded-lg dark:bg-gray-800"
            >
                <h2 class="text-xl dark:text-white">Favorite Songs</h2>
                <div class="mt-2 text-gray-900 dark:text-gray-100">
                    <ul class="divide-y divide-gray-300 dark:divide-gray-700">
                        <li
                            class="flex justify-between gap-x-6 rounded p-2 transition-colors duration-150 hover:bg-gray-300 dark:hover:bg-gray-700"
                            v-for="song in favorite_songs"
                            :key="song.id"
                        >
                            <Link
                                class="flex grow items-center gap-2"
                                :href="
                                    route('artists.songs.show', [
                                        song.artist,
                                        song.slug,
                                    ])
                                "
                            >
                                {{ song.name }} <span>-</span>
                                <span class="text-sm text-gray-500">{{
                                    song.artist.name
                                }}</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
            <div
                class="mt-6 overflow-hidden p-4 sm:rounded-lg dark:bg-gray-800"
            >
                <h2 class="text-xl dark:text-white">Favorite Artists</h2>
                <div class="mt-2 text-gray-900 dark:text-gray-100">
                    <ul class="divide-y divide-gray-300 dark:divide-gray-700">
                        <li
                            class="flex justify-between gap-x-6 rounded p-2 transition-colors duration-150 hover:bg-gray-300 dark:hover:bg-gray-700"
                            v-for="artist in favorite_artists"
                            :key="artist.id"
                        >
                            <Link
                                class="flex grow items-center gap-2"
                                :href="route('artists.show', [artist.slug])"
                            >
                                {{ artist.name }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </Container>
    </main>
</template>
