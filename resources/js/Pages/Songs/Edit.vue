<script setup>
import SongForm from '@/Components/Domain/Song/SongForm.vue';
import SongPreview from '@/Components/Domain/Song/SongPreview.vue';
import PageHeader from '@/Components/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    artist: Object,
    song: Object,
    available_keys: Array,
});

const form = useForm({
    name: props.song.name,
    key: props.song.key,
    content: props.song.content,
});

function submitForm() {
    form.post(
        route('song_submissions.store', {
            artist: props.artist,
            song: props.song,
        }),
    );
}
</script>

<template>
    <Head :title="`${artist.name} - ${song.name} - Edit`" />

    <AppLayout>
        <template #header>
            <PageHeader title="Edit song" />

            <Link :href="route('artists.show', artist)">
                {{ artist.name }}
            </Link>
        </template>

        <div class="mt-6 grid grid-cols-2 justify-between gap-12 py-6">
            <SongForm
                :available_keys="available_keys"
                :initial_data="form"
                submit_label="Update"
                @submit="submitForm"
            />

            <section class="dark:text-white">
                <h3 class="text-md mb-6">Preview</h3>
                <SongPreview :content="form.content" />
            </section>
        </div>
    </AppLayout>
</template>
