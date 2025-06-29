<script setup>
import LoadingButton from '@/Components/LoadingButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { ArrowRightIcon } from '@/Components/UI/Icons';
import AppLayout from '@/Layouts/AppLayout.vue';
import SongSubmissionTable from '@/Pages/SongSubmissions/partials/SongSubmissionTable.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
            class="border-base-content/10 mt-6 overflow-x-auto rounded border p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <Link
                    href="/song-submissions"
                    class="btn btn-lg btn-soft btn-primary flex w-full items-center justify-between"
                >
                    <h2>User Submissions</h2>
                    <ArrowRightIcon class="size-6" />
                </Link>
            </div>
            <SongSubmissionTable :submissions="submissions" class="max-h-80" />
        </div>
        <div
            class="border-base-content/10 mt-6 grid grid-cols-1 gap-6 rounded border p-4 sm:grid-cols-2"
        >
            <div>
                <h2 class="mb-4 px-3 py-2 text-xl font-semibold">
                    Favorite Songs
                </h2>
                <ul v-if="songs.length" class="list">
                    <li
                        class="list-row hover:bg-primary/8"
                        v-for="song in songs"
                        :key="song.id"
                    >
                        <Link
                            class="list-grow flex items-center gap-1"
                            :href="
                                route('artists.songs.show', [
                                    song.artist,
                                    song.slug,
                                ])
                            "
                        >
                            {{ song.name }} <span>-</span>
                            <span class="text-base-content/50 text-sm">{{
                                song.artist.name
                            }}</span>
                        </Link>
                    </li>
                </ul>
                <p v-else class="px-3 py-2">You have no favorite songs yet.</p>
                <LoadingButton
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
                <ul v-if="artists.length" class="list">
                    <li
                        class="list-row hover:bg-primary/8"
                        v-for="artist in artists"
                        :key="artist.id"
                    >
                        <Link
                            class="list-grow"
                            :href="route('artists.show', artist)"
                        >
                            {{ artist.name }}
                        </Link>
                    </li>
                </ul>
                <p v-else class="px-3 py-2 dark:text-white">
                    You have no favorite artists yet.
                </p>
                <LoadingButton
                    v-if="artists_next_page"
                    :onLoadMore="loadMoreArtists"
                    :loading="loading"
                />
            </div>
        </div>
    </AppLayout>
</template>
