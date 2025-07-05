<script setup>
import FavoriteIndicator from '@/Components/UI/FavoriteIndicator.vue';
import { SearchIcon, StarIconSolid } from '@/Components/UI/Icons';
import InputError from '@/Components/UI/InputError.vue';
import LoadingButton from '@/Components/UI/LoadingButton.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import Panel from '@/Components/UI/Panel.vue';
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

const loadingSongs = ref(false);
const loadingArtists = ref(false);
const songs = ref(props.songs.data);
const songsNextPage = ref(props.songs.next_page_url);
const artists = ref(props.artists.data);
const artistsNextPage = ref(props.artists.next_page_url);

const loadMoreSongs = async () => {
    if (!songsNextPage.value) return;

    loadingSongs.value = true;

    router.reload({
        only: ['songs'],
        data: { page: props.songs.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            songs.value.push(...page.props.songs.data);
            songsNextPage.value = page.props.songs.next_page_url;
        },
        onFinish: () => (loadingSongs.value = false),
    });
};

const loadMoreArtists = async () => {
    if (!artistsNextPage.value) return;

    loadingArtists.value = true;

    router.reload({
        only: ['artists'],
        data: { page: props.artists.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onSuccess: (page) => {
            artists.value.push(...page.props.artists.data);
            artistsNextPage.value = page.props.artists.next_page_url;
        },
        onFinish: () => (loadingArtists.value = false),
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
            songsNextPage.value = page.props.songs.next_page_url;
            artists.value = page.props.artists.data;
            artistsNextPage.value = page.props.artists.next_page_url;
        },
    });
}
</script>

<template>
    <Head :title="'Search: ' + query" />

    <AppLayout dusk="search-page">
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

        <Panel>
            <h2 class="px-3 py-2 text-xl font-semibold">Songs</h2>
            <ul v-if="songs.length" role="list" class="list">
                <li
                    v-for="song in songs"
                    :key="song.id"
                    class="list-row hover:bg-primary/8"
                >
                    <Link
                        :href="
                            route('artists.songs.show', [
                                song.artist,
                                song.slug,
                            ])
                        "
                        class="list-col-grow flex items-center gap-2"
                    >
                        <StarIconSolid
                            class="invisible size-5"
                            :class="{
                                'visible text-yellow-500': song.is_favorited,
                            }"
                        />

                        <b>{{ song.name }}</b> <span>-</span>
                        <span class="text-base-content/70 text-sm">{{
                            song.artist.name
                        }}</span>
                    </Link>
                </li>
            </ul>
            <p v-else class="px-3 py-2">No results found.</p>
            <div class="flex justify-center">
                <LoadingButton
                    v-if="songsNextPage"
                    :on-load-more="loadMoreSongs"
                    :loading="loadingSongs"
                >
                    Load More
                </LoadingButton>
            </div>
        </Panel>

        <Panel>
            <h2 class="px-3 py-2 text-xl font-semibold">Artists</h2>
            <ul v-if="artists.length" role="list" class="list">
                <li
                    v-for="artist in artists"
                    :key="artist.id"
                    class="list-row hover:bg-primary/8"
                >
                    <Link
                        :href="route('artists.show', artist)"
                        class="list-col-grow flex items-center gap-2"
                    >
                        <FavoriteIndicator
                            :is-favorited="artist.is_favorited"
                        />
                        <b>
                            {{ artist.name }}
                        </b>
                    </Link>
                </li>
            </ul>
            <p v-else class="px-3 py-2">No results found.</p>
            <div class="flex justify-center">
                <LoadingButton
                    v-if="artistsNextPage"
                    :on-load-more="loadMoreArtists"
                    :loading="loadingArtists"
                >
                    Load More
                </LoadingButton>
            </div>
        </Panel>
    </AppLayout>
</template>
