<script setup>
import SongContent from '@/Components/Domain/Song/SongContent.vue';
import SongControls from '@/Components/Domain/Song/SongControls.vue';
import FavoriteButton from '@/Components/UI/FavoriteButton.vue';
import { PencilSquareIconSolid } from '@/Components/UI/Icons';
import PageHeader from '@/Components/UI/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { initSongStore } from '@/Stores/songStore';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    song: {
        type: Object,
        required: true,
    },
    artist: {
        type: Object,
        required: true,
    },
    availableKeys: {
        type: Array,
        required: true,
    },
    chords: {
        type: Object,
        required: true,
    },
    isFavorited: Boolean,
});

initSongStore({
    key: props.song.key,
    initialChords: props.chords,
    availableKeysArray: props.availableKeys,
});

const user = usePage().props.auth.user;
const songControlsRef = ref(null);
const loading = ref(false);

function handleFavorite() {
    loading.value = true;

    const method = props.isFavorited ? 'delete' : 'post';

    router.visit(
        route('songs.favorite', { artist: props.artist, song: props.song }),
        {
            method,
            only: ['isFavorited'],
            preserveState: true,
            onFinish: () => {
                loading.value = false;
            },
            onError: () => {
                loading.value = false;
            },
        },
    );
}
</script>

<template dusk="song-page">
    <Head :title="`${song.name} - ${artist.name}`" />

    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <PageHeader :title="song.name" />
                <FavoriteButton
                    :favorited="isFavorited"
                    :loading="loading"
                    @favorite="handleFavorite"
                />
                <Link
                    v-if="user"
                    dusk="edit-song-link"
                    class="btn btn-info btn-sm btn-circle btn-ghost"
                    :href="
                        route('artists.songs.edit', {
                            artist,
                            song,
                        })
                    "
                >
                    <PencilSquareIconSolid class="size-5" />
                </Link>
            </div>
            <Link
                class="text-base-content/70 hover:text-base-content/90"
                :href="route('artists.show', artist)"
            >
                {{ artist.name }}
            </Link>
            <SongControls
                ref="songControlsRef"
                :available-keys="availableKeys"
                :show-capo-options="true"
                :show-key-change-buttons="true"
            />
        </template>
        <div
            class="py-6 dark:text-white"
            :class="{
                'gap-8 xl:columns-2': songControlsRef?.multiColumn ?? false,
            }"
        >
            <SongContent :content="song.lines" :show-diagrams="true" />
        </div>
    </AppLayout>
</template>
