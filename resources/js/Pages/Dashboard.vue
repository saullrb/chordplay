<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SongSubmissionTable from './SongSubmissions/partials/SongSubmissionTable.vue';
import { ref } from 'vue';
import LoadMoreButton from '@/Components/LoadMoreButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    favorite_artists: Object,
    favorite_songs: Object,
    submissions: Array,
});

const loading = ref(false);
const songs = ref(props.favorite_songs.data);
const songs_next_page = ref(props.favorite_songs.next_page_url);
const artists = ref(props.favorite_artists.data);
const artists_next_page = ref(props.favorite_artists.next_page_url);

const loadMoreArtists = async () => {
    if (!artists_next_page.value) return;

    loading.value = true;

    router.reload({
        only: ['favorite_artists'],
        data: { page: props.favorite_artists.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            artists.value.push(...page.props.favorite_artists.data);
            artists_next_page.value = page.props.favorite_artists.next_page_url;
        },
        onFinish: () => (loading.value = false),
    });
};

const loadMoreSongs = async () => {
    if (!songs_next_page.value) return;

    loading.value = true;

    router.reload({
        only: ['favorite_songs'],
        data: { page: props.favorite_songs.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            songs.value.push(...page.props.favorite_songs.data);
            songs_next_page.value = page.props.favorite_songs.next_page_url;
        },
        onFinish: () => (loading.value = false),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #header>
            <PageHeader title="Dashboard" />
        </template>

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
                <h2
                    class="mb-4 px-3 py-2 text-xl font-semibold dark:text-white"
                >
                    Favorite Songs
                </h2>
                <ul
                    v-if="songs.length"
                    class="divide-y-1 divide-gray-400 dark:divide-gray-700"
                >
                    <li
                        class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
                        v-for="song in songs"
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
                <LoadMoreButton
                    v-if="songs_next_page"
                    :onLoadMore="loadMoreSongs"
                    :loading="loading"
                />
            </div>
            <div>
                <h2
                    class="mb-4 px-3 py-2 text-xl font-semibold dark:text-white"
                >
                    Favorite Artists
                </h2>
                <ul
                    v-if="artists.length"
                    class="divide-y-1 divide-gray-400 dark:divide-gray-700"
                >
                    <li
                        class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
                        v-for="artist in artists"
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
                <LoadMoreButton
                    v-if="artists_next_page"
                    :onLoadMore="loadMoreArtists"
                    :loading="loading"
                />
            </div>
        </div>
    </AppLayout>
</template>
