<script setup>
import LoadingButton from '@/Components/LoadingButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { SearchIcon, StarIconSolid } from '@/Components/UI/Icons';
import InputError from '@/Components/UI/InputError.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    songs: {
        type: Object,
        required: true,
    },
    artists: {
        type: Object,
        required: true,
    },
    query: {
        type: String,
        required: true,
    },
});

const loading = ref(false);
const songs = ref(props.songs.data);
const songsNextPage = ref(props.songs.nextPageUrl);
const artists = ref(props.artists.data);
const artistsNextPage = ref(props.artists.nextPageUrl);

const loadMoreSongs = async () => {
    if (!songsNextPage.value) return;

    loading.value = true;

    router.reload({
        only: ['songs'],
        data: { page: props.songs.currentPage + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            songs.value.push(...page.props.songs.data);
            songsNextPage.value = page.props.songs.nextPageUrl;
        },
        onFinish: () => (loading.value = false),
    });
};

const loadMoreArtists = async () => {
    if (!artistsNextPage.value) return;

    loading.value = true;

    router.reload({
        only: ['artists'],
        data: { page: props.artists.currentPage + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            artists.value.push(...page.props.artists.data);
            artistsNextPage.value = page.props.artists.nextPageUrl;
        },
        onFinish: () => (loading.value = false),
    });
};

const form = useForm({
    query: props.query ?? '',
});

function handleSubmit() {
    form.get(route('search'), {
        preserveState: true,
        onSuccess: (page) => {
            songs.value = page.props.songs.data;
            songsNextPage.value = page.props.songs.nextPageUrl;
            artists.value = page.props.artists.data;
            artistsNextPage.value = page.props.artists.nextPageUrl;
        },
    });
}
</script>

<template>
    <Head :title="'Search: ' + query" />

    <AppLayout>
        <template #header>
            <PageHeader :title="'Search: ' + query" />
        </template>

        <form class="w-full" @submit.prevent="handleSubmit">
            <label class="input input-primary validator w-full">
                <SearchIcon class="h-[1em] opacity-50" />
                <input
                    v-model="form.query"
                    dusk="search-input"
                    type="search"
                    placeholder="Search"
                    maxlength="100"
                    required
                    @input="form.errors.query = null"
                />
            </label>
            <InputError class="mt-1" :message="form.errors.query" />
        </form>

        <div
            class="mt-6 grid grid-cols-1 gap-6 border border-gray-700 p-4 sm:grid-cols-2 sm:rounded-lg"
        >
            <div class="">
                <h2
                    class="mb-4 px-3 py-2 text-xl font-semibold dark:text-white"
                >
                    Songs
                </h2>
                <ul
                    v-if="songs.length"
                    class="divide-y-1 divide-gray-400 dark:divide-gray-700"
                >
                    <li
                        v-for="song in songs"
                        :key="song.id"
                        class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
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
                            <StarIconSolid
                                v-if="song.isFavorited"
                                class="size-6 text-yellow-500"
                            />

                            {{ song.name }} <span>-</span>
                            <span class="text-sm text-gray-500">{{
                                song.artist.name
                            }}</span>
                        </Link>
                    </li>
                </ul>
                <p v-else class="px-3 py-2 dark:text-white">
                    No results found.
                </p>
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
                    Artists
                </h2>
                <ul
                    v-if="artists.length"
                    class="divide-y-1 divide-gray-400 dark:divide-gray-700"
                >
                    <li
                        v-for="artist in artists"
                        :key="artist.id"
                        class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
                    >
                        <Link
                            class="flex grow items-center gap-2"
                            :href="route('artists.show', artist)"
                        >
                            <StarIconSolid
                                v-if="artist.isFavorited"
                                class="size-6 text-yellow-500"
                            />
                            {{ artist.name }}
                        </Link>
                    </li>
                </ul>
                <p v-else class="px-3 py-2 dark:text-white">
                    No results found.
                </p>
                <div class="my-6 flex justify-center">
                    <LoadingButton
                        v-if="artistsNextPage"
                        :on-load-more="loadMoreArtists"
                        :loading="loading"
                    >
                        Load More
                    </LoadingButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
