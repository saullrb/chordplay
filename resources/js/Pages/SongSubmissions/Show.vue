<script setup>
import SongContent from '@/Components/Domain/Song/SongContent.vue';
import SongControls from '@/Components/Domain/Song/SongControls.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ConfirmationDialog from '@/Components/UI/dialog/ConfirmationDialog.vue';
import {
    CheckIconSolid,
    PencilSquareIconSolid,
    TrashIconSolid,
} from '@/Components/UI/Icons';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    song: Object,
    artist: Object,
    valid_chords: Object,
    available_keys: Array,
    can: {
        type: Object,
        default: () => ({
            approve_submission: {
                type: Boolean,
                default: false,
            },
            update_submission: {
                type: Boolean,
                default: false,
            },
        }),
    },
});

const song_controls_ref = ref(null);
const confirm_dialog = ref();

function handleConfirmation() {
    router.delete(route('song_submissions.destroy', { id: props.song.id }));
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
                <strong>Preview:</strong> This is a preview of what the page
                will look like if approved.
            </div>
            <div class="flex items-center gap-2">
                <PageHeader :title="song.name" />
                <Link
                    v-if="can.approve_submission"
                    class="btn btn-success btn-sm btn-circle btn-ghost"
                    dusk="approve-song-button"
                    :href="
                        route('song_submissions.approve', {
                            id: props.song.id,
                        })
                    "
                    method="post"
                >
                    <CheckIconSolid class="size-5" />
                </Link>
                <Link
                    dusk="edit-song-link"
                    v-if="can.update_submission"
                    class="btn btn-info btn-sm btn-circle btn-ghost"
                    :href="
                        route('song_submissions.edit', {
                            id: song.id,
                        })
                    "
                >
                    <PencilSquareIconSolid class="size-5" />
                </Link>
                <button
                    @click="confirm_dialog.show()"
                    class="btn btn-error btn-sm btn-circle btn-ghost"
                    dusk="reject-song-button"
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
            <SongControls ref="song_controls_ref" :song_key="song.key" />
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

        <ConfirmationDialog
            ref="confirm_dialog"
            @confirm="handleConfirmation"
            title="Delete?"
            message="Are you sure you want to reject this submission"
        />
    </AppLayout>
</template>
