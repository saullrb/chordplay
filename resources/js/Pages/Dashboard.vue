<script setup>
import { ArrowRightIcon } from '@/Components/UI/Icons';
import LoadingButton from '@/Components/UI/LoadingButton.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import Panel from '@/Components/UI/Panel.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SongSubmissionTable from '@/Pages/SongSubmissions/partials/SongSubmissionTable.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    favoriteArtists: {
        type: Object,
        required: true,
    },
    favoriteSongs: {
        type: Object,
        required: true,
    },
    submissions: {
        type: Array,
        required: true,
    },
});

const loading = ref(false);
const songs = ref(props.favoriteSongs.data);
const songsNextPage = ref(props.favoriteSongs.nextPageUrl);
const artists = ref(props.favoriteArtists.data);
const artistsNextPage = ref(props.favoriteArtists.nextPageUrl);

const loadMoreArtists = async () => {
    if (!artistsNextPage.value) return;

    loading.value = true;

    router.reload({
        only: ['favoriteArtists'],
        data: { page: props.favoriteArtists.currentPage + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            artists.value.push(...page.props.favoriteArtists.data);
            artistsNextPage.value = page.props.favoriteArtists.nextPageUrl;
        },
        onFinish: () => (loading.value = false),
    });
};

const loadMoreSongs = async () => {
    if (!songsNextPage.value) return;

    loading.value = true;

    router.reload({
        only: ['favoriteSongs'],
        data: { page: props.favoriteSongs.currentPage + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            songs.value.push(...page.props.favoriteSongs.data);
            songsNextPage.value = page.props.favoriteSongs.nextPageUrl;
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

        <Panel>
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
        </Panel>

        <Panel class="grid grid-cols-1 lg:grid-cols-2">
            <div>
                <h2 class="mb-4 px-3 py-2 text-xl font-semibold">
                    Favorite Songs
                </h2>
                <ul v-if="songs.length" class="list">
                    <li
                        v-for="song in songs"
                        :key="song.id"
                        class="list-row hover:bg-primary/8"
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
                            <b>{{ song.name }}</b> <span>-</span>
                            <span
                                class="text-base-content/70 text-sm font-normal"
                                >{{ song.artist.name }}</span
                            >
                        </Link>
                    </li>
                </ul>
                <p v-else class="px-3 py-2">You have no favorite songs yet.</p>
                <LoadingButton
                    v-if="songsNextPage"
                    :on-load-more="loadMoreSongs"
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
                        v-for="artist in artists"
                        :key="artist.id"
                        class="list-row hover:bg-primary/8"
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
                    v-if="artistsNextPage"
                    :on-load-more="loadMoreArtists"
                    :loading="loading"
                />
            </div>
        </Panel>
    </AppLayout>
</template>
