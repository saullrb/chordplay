<script setup>
import Container from '@/Components/Container.vue';
import IconLink from '@/Components/IconLink.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import SongSubmissionTable from './SongSubmissions/partials/SongSubmissionTable.vue';

const props = defineProps({
    favorite_artists: Array,
    favorite_songs: Array,
    submissions: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <NavBar />

    <main class="mt-6 pb-6">
        <Container>
            <PageHeader title="Dashboard" />

            <div
                class="mt-6 overflow-hidden border border-gray-700 p-4 sm:rounded-lg"
            >
                <div class="mb-4 flex items-center justify-between">
                    <Link
                        href="/song-submissions"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xl font-semibold hover:bg-gray-500 hover:text-white dark:text-white dark:hover:bg-gray-800"
                    >
                        <h2>User Submissions</h2>
                        <i class="fa-solid fa-arrow-right"></i>
                    </Link>
                </div>
                <div class="max-h-80 overflow-x-auto">
                    <SongSubmissionTable :submissions="submissions" />
                </div>
            </div>
            <div
                class="mt-6 grid grid-cols-1 gap-6 border border-gray-700 p-4 sm:grid-cols-2 sm:rounded-lg"
            >
                <div class="">
                    <div class="mb-4 flex items-center justify-between">
                        <!-- TODO: link to the favorites page -->
                        <Link
                            href="/"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xl font-semibold hover:bg-gray-500 hover:text-white dark:text-white dark:hover:bg-gray-800"
                        >
                            <h2>Favorite Songs</h2>
                            <i class="fa-solid fa-arrow-right"></i>
                        </Link>
                    </div>
                    <ul
                        v-if="favorite_songs.length"
                        class="divide-y-1 divide-gray-400 dark:divide-gray-700"
                    >
                        <li
                            class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
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
                    <p v-else class="px-3 py-2 dark:text-white">
                        You have no favorite songs yet.
                    </p>
                </div>
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <!-- TODO: link to the favorites page -->
                        <Link
                            href="/"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xl font-semibold hover:bg-gray-500 hover:text-white dark:text-white dark:hover:bg-gray-800"
                        >
                            <h2>Favorite Artists</h2>
                            <i class="fa-solid fa-arrow-right"></i>
                        </Link>
                    </div>
                    <ul
                        v-if="favorite_artists.length"
                        class="divide-y-1 divide-gray-400 dark:divide-gray-700"
                    >
                        <li
                            class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
                            v-for="artist in favorite_artists"
                            :key="artist.id"
                        >
                            <Link
                                class="flex grow items-center gap-2"
                                :href="route('artists.show', artist)"
                            >
                                {{ artist.name }}
                            </Link>
                        </li>
                    </ul>
                    <p v-else class="px-3 py-2 dark:text-white">
                        You have no favorite artists yet.
                    </p>
                </div>
            </div>
        </Container>
    </main>
</template>
