<script setup>
import InputError from '@/Components/InputError.vue';
import LoadMoreButton from '@/Components/LoadMoreButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
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

        <form @submit.prevent="handleSubmit" class="flex justify-center">
            <input
                v-model="form.query"
                type="text"
                class="z-10 w-full rounded-l border-black dark:border-gray-400 dark:bg-gray-800 dark:text-white"
                placeholder="Search songs or artists..."
            />
            <button
                type="submit"
                :disabled="form.processing"
                class="cursor-pointer rounded-r border border-l-0 border-black bg-white px-4 transition-colors hover:bg-gray-100 disabled:cursor-wait dark:border-gray-400 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
            >
                <i class="fa-solid fa-search"></i>
            </button>
            <InputError :message="form.errors.name" />
        </form>
        <p v-if="form.errors.query" class="mt-1 text-sm text-red-500">
            {{ form.errors.query }}
        </p>
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
                            <i
                                v-if="song.is_favorited"
                                class="fa-solid fa-star text-sm text-yellow-600"
                            ></i>
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
                            <i
                                v-if="artist.is_favorited"
                                class="fa-solid fa-star text-sm text-yellow-600"
                            ></i>
                            {{ artist.name }}
                        </Link>
                    </li>
                </ul>
                <p v-else class="px-3 py-2 dark:text-white">
                    No results found.
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
