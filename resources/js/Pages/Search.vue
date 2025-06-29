<script setup>
import LoadingButton from '@/Components/LoadingButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { SearchIcon, StarIconSolid } from '@/Components/UI/Icons';
import InputError from '@/Components/UI/InputError.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    songs: Object,
    artists: Object,
    query: String,
});

const loading = ref(false);
const songs = ref(props.songs.data);
const songs_next_page = ref(props.songs.next_page_url);
const artists = ref(props.artists.data);
const artists_next_page = ref(props.artists.next_page_url);

const loadMoreSongs = async () => {
    if (!songs_next_page.value) return;

    loading.value = true;

    router.reload({
        only: ['songs'],
        data: { page: props.songs.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            songs.value.push(...page.props.songs.data);
            songs_next_page.value = page.props.songs.next_page_url;
        },
        onFinish: () => (loading.value = false),
    });
};

const loadMoreArtists = async () => {
    if (!artists_next_page.value) return;

    loading.value = true;

    router.reload({
        only: ['artists'],
        data: { page: props.artists.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            artists.value.push(...page.props.artists.data);
            artists_next_page.value = page.props.artists.next_page_url;
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
            songs_next_page.value = page.props.songs.next_page_url;
            artists.value = page.props.artists.data;
            artists_next_page.value = page.props.artists.next_page_url;
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

        <form @submit.prevent="handleSubmit" class="w-full">
            <label class="input input-primary validator w-full">
                <SearchIcon class="h-[1em] opacity-50" />
                <input
                    dusk="search-input"
                    v-model="form.query"
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
                            <StarIconSolid
                                v-if="song.is_favorited"
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
                    v-if="songs_next_page"
                    :onLoadMore="loadMoreSongs"
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
                        class="flex justify-between gap-x-6 rounded px-3 py-2 transition-colors duration-150 *:text-gray-900 hover:bg-gray-300 dark:*:text-white dark:hover:bg-gray-700"
                        v-for="artist in artists"
                        :key="artist.id"
                    >
                        <Link
                            class="flex grow items-center gap-2"
                            :href="route('artists.show', artist)"
                        >
                            <StarIconSolid
                                v-if="artist.is_favorited"
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
                        v-if="artists_next_page"
                        :onLoadMore="loadMoreArtists"
                        :loading="loading"
                        >Load More</LoadingButton
                    >
                </div>
            </div>
        </div>
    </AppLayout>
</template>
