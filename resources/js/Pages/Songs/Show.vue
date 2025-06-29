<script setup>
import SongContent from '@/Components/Domain/Song/SongContent.vue';
import SongControls from '@/Components/Domain/Song/SongControls.vue';
import FavoriteButton from '@/Components/FavoriteButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { PencilSquareIconSolid } from '@/Components/UI/Icons';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    song: Object,
    is_favorited: Boolean,
    artist: Object,
    valid_chords: Object,
    available_keys: Array,
});

const user = usePage().props.auth.user;
const song_controls_ref = ref(null);
const loading = ref(false);

function handleFavorite() {
    loading.value = true;

    const method = props.is_favorited ? 'delete' : 'post';

    router.visit(
        route('songs.favorite', { artist: props.artist, song: props.song }),
        {
            method,
            only: ['is_favorited'],
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
                    :favorited="is_favorited"
                    @favorite="handleFavorite"
                    :loading="loading"
                />
                <Link
                    dusk="edit-song-link"
                    v-if="user"
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
                ref="song_controls_ref"
                :song_key="song.key"
                :available_keys="available_keys"
                :show_capo_options="true"
                :show_key_change_buttons="true"
            />
        </template>
        <div
            class="py-6 dark:text-white"
            :class="{
                'columns-2 gap-8': song_controls_ref?.multi_column ?? false,
            }"
        >
            <SongContent
                :original_key="song.key"
                :key_offset="song_controls_ref?.key_offset ?? 0"
                :available_keys="available_keys"
                :content="song.lines"
                :valid_chords="valid_chords"
            />
        </div>
    </AppLayout>
</template>
