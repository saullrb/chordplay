<script setup>
import SongContent from '@/Components/Domain/Song/SongContent.vue';
import SongControls from '@/Components/Domain/Song/SongControls.vue';
import ConfirmationDialog from '@/Components/UI/dialog/ConfirmationDialog.vue';
import {
    CheckIconSolid,
    PencilSquareIconSolid,
    TrashIconSolid,
} from '@/Components/UI/Icons';
import PageHeader from '@/Components/UI/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { initSongStore } from '@/Stores/songStore';
import { Link, router } from '@inertiajs/vue3';
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
    can: {
        type: Object,
        default: () => ({
            approveSubmission: {
                type: Boolean,
                default: false,
            },
            updateSubmission: {
                type: Boolean,
                default: false,
            },
        }),
    },
});

initSongStore({
    key: props.song.key,
    initialChords: props.chords,
});

const songControlsRef = ref(null);
const confirmDialog = ref();

function handleConfirmation() {
    router.delete(route('song-submissions.destroy', { id: props.song.id }));
}
</script>

<template>
    <Head :title="`[Preview] ${song.name} - ${song.artist.name}`" />

    <AppLayout>
        <template #header>
            <div
                dusk="preview-notice"
                class="bg-warning text-warning-content mb-4 rounded px-4 py-2"
            >
                <strong>Preview Mode:</strong> Some features are only accessible
                for approved songs.
            </div>
            <div class="flex items-center gap-2">
                <PageHeader :title="song.name" />
                <Link
                    v-if="can.approveSubmission"
                    class="btn btn-success btn-sm btn-circle btn-ghost"
                    dusk="approve-song-button"
                    :href="
                        route('song-submissions.approve', {
                            id: props.song.id,
                        })
                    "
                    method="post"
                >
                    <CheckIconSolid class="size-5" />
                </Link>
                <Link
                    v-if="can.updateSubmission"
                    dusk="edit-song-link"
                    class="btn btn-info btn-sm btn-circle btn-ghost"
                    :href="
                        route('song-submissions.edit', {
                            id: song.id,
                        })
                    "
                >
                    <PencilSquareIconSolid class="size-5" />
                </Link>
                <button
                    class="btn btn-error btn-sm btn-circle btn-ghost"
                    dusk="reject-song-button"
                    @click="confirmDialog.show()"
                >
                    <TrashIconSolid class="size-5" />
                </button>
            </div>
            <Link
                :href="route('artists.show', song.artist)"
                class="text-base-content/70 hover:text-base-content/90"
            >
                {{ song.artist.name }}
            </Link>
            <SongControls ref="songControlsRef" :original-song-key="song.key" />
        </template>
        <div
            class="py-6 dark:text-white"
            :class="{
                'columns-2 gap-8': songControlsRef?.multiColumn ?? false,
            }"
        >
            <SongContent :content="song.lines" />
        </div>

        <ConfirmationDialog
            ref="confirmDialog"
            title="Delete?"
            message="Are you sure you want to reject this submission"
            @confirm="handleConfirmation"
        />
    </AppLayout>
</template>
